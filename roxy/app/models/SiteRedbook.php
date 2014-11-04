<?php
namespace ISTResearch_Roxy\models;
use ISTResearch_Roxy\models\RoxyCore as BaseModel;
use com\blackmoonit\Strings;
use com\blackmoonit\database\FinallyCursor;
use com\blackmoonit\exceptions\DbException;
use \PDO;
use \PDOStatement;
use \PDOException;
use \DateTime;
use \DateInterval;
{//namespace begin

class SiteRedbook extends BaseModel {
	public $site_id = 'rb'; //this should the part of the URL that defines this site in roxy
	public $site_display_name = 'myRedBook';

	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		$this->tnAds = $this->tbl_.'scraped_rb_ads';
		$this->tnPhotos = $this->tbl_.'scraped_rb_photos';
		$this->tnAdPoprox = $this->tbl_.'poprox_rb_ads';
		//queue for rb does not exist yet
		//$this->tnAdQueue = str_replace($this->dbConnName,'roxy_scrape',$this->tbl_).'???_queue'; //different db, same connection
	}
	
	protected function normalizeAdRow(&$aRow) {
		parent::normalizeAdRow($aRow);
		if (!empty($aRow)) {
			if (!is_null($aRow['provider_id'])) $aRow['provider_id']+=0;
			if (!is_null($aRow['org_id'])) $aRow['org_id']+=0;
			if (!is_null($aRow['orgid'])) $aRow['orgid']+=0;
			if (!is_null($aRow['providerid'])) $aRow['providerid']+=0;
			if (!is_null($aRow['file'])) 
				$aRow['url'] = 'http://classifieds.myredbook.com/classified.php?adid='.$aRow['adid'];
			else
				$aRow['url'] = null;
		}
	}
	
	/**
	 * Get the photos associated witn an ad.
	 * @param int $aRoxyId - the Roxy ID
	 * @throws DbException
	 * @return array Returns an array of photo urls or array() if none found.
	 */
	public function getAdPhotos($aRoxyId) {
		$theResult = array();
		if (!empty($this->db) && !empty($aRoxyId)) try {
			$theRoxyId = $aRoxyId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
	
			$theParams = array();
			$theParamTypes = array();
			$thePhotoUrlPrefix = $this->director->getSiteURL('/res/images/mrb-photos').'/';
			//TODO: ad_id is always NULL due to bug in scraper, use adid instead
			$theSql = 'SELECT CONCAT("'.$thePhotoUrlPrefix.'",SUBSTRING_INDEX(remote_url,\'/\', -1)) AS imgsrc ';
			$theSql .= ' FROM '.$this->tnPhotos.' AS pics JOIN '.$this->tnAds.' AS ads ON pics.adid=ads.adid WHERE ads.id=:roxy_id';
			$theSql .= ' AND remote_url IS NOT NULL';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$theResult = $rs->fetchAll(PDO::FETCH_COLUMN,0);
			$rs->closeCursor();
	
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getAdPhotos='.$theRoxyId.' failed.');
		}
		return $theResult;
	}
	
	public function poproxScore($aAcctId, $aPostVars) {
		$v = &$aPostVars;
		$theUserId = $aAcctId+0;
		$theRoxyId = $v->ad_id+0;
		
		if (!empty($v) && !empty($theRoxyId)) {

			$theParams = array();
			$theParamTypes = array();
			//ensure poprox data exists
			$theSql = 'SELECT ad_id FROM '.$this->tnAdPoprox.' WHERE user_id=:user_id AND ad_id=:roxy_id';
			$theParams['user_id'] = $theUserId;
			$theParamTypes['user_id'] = PDO::PARAM_INT;
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			if ($rs->fetch()===false) {
				$theSql = 'INSERT INTO '.$this->tnAdPoprox.' SET user_id=:user_id, ad_id=:roxy_id, score=0';
				$this->execDML($theSql,$theParams,$theParamTypes);
			}
			$rs->closeCursor();

			$theSql = 'UPDATE '.$this->tnAdPoprox.' SET';
			$theSql .= ' score=:score';
			$theSql .= ', user_comment=:user_comment';
			//modify the following fields to match our particular poprox table
			$theSql .= ', adid=:adid';
			$theSql .= ', orgid=:orgid';
			$theSql .= ', providerid=:providerid';
			$theSql .= ', title=:title';
			$theSql .= ', service=:service';
			$theSql .= ', region=:region';
			$theSql .= ', views=:views';
			$theSql .= ', availability=:availability';
			$theSql .= ', name=:name';
			$theSql .= ', phone=:phone';
			$theSql .= ', rb_inbox=:rb_inbox';
			$theSql .= ', email=:email';
			$theSql .= ', website=:website';
			$theSql .= ', ethnicity=:ethnicity';
			$theSql .= ', age=:age';
			$theSql .= ', eye_color=:eye_color';
			$theSql .= ', hair_color=:hair_color';
			$theSql .= ', build=:build';
			$theSql .= ', height=:height';
			$theSql .= ', bust=:bust';
			$theSql .= ', cup=:cup';
			$theSql .= ', kitty=:kitty';
			$theSql .= ', rate=:rate';
			$theSql .= ', incall=:incall';
			$theSql .= ', outcall=:outcall';
			$theSql .= ', screening=:screening';
			$theSql .= ', text=:ad_text';
			$theSql .= ', city=:city';
			
			$theSql .= ' WHERE user_id=:user_id AND ad_id=:roxy_id';
			
			//param values to save
			$theParams['score'] = (!empty($v->poprox_likely))?1:0;
			$theParams['user_comment'] = $v->poprox_comment;
			//modify the following fields to match our particular poprox table
			$theParams['adid'] = (!empty($v->poprox_adid))?1:0;
			$theParams['orgid'] = (!empty($v->poprox_orgid))?1:0;
			$theParams['providerid'] = (!empty($v->poprox_providerid))?1:0;
			$theParams['title'] = (!empty($v->poprox_title))?1:0;
			$theParams['service'] = (!empty($v->poprox_service))?1:0;
			$theParams['region'] = (!empty($v->poprox_region))?1:0;
			$theParams['views'] = (!empty($v->poprox_views))?1:0;
			$theParams['availability'] = (!empty($v->poprox_availability))?1:0;
			$theParams['name'] = (!empty($v->poprox_name))?1:0;
			$theParams['phone'] = (!empty($v->poprox_phone))?1:0;
			$theParams['rb_inbox'] = (!empty($v->poprox_rb_inbox))?1:0;
			$theParams['email'] = (!empty($v->poprox_email))?1:0;
			$theParams['website'] = (!empty($v->poprox_website))?1:0;
			$theParams['ethnicity'] = (!empty($v->poprox_ethnicity))?1:0;
			$theParams['age'] = (!empty($v->poprox_age))?1:0;
			$theParams['eye_color'] = (!empty($v->poprox_eye_color))?1:0;
			$theParams['hair_color'] = (!empty($v->poprox_hair_color))?1:0;
			$theParams['build'] = (!empty($v->poprox_build))?1:0;
			$theParams['height'] = (!empty($v->poprox_height))?1:0;
			$theParams['bust'] = (!empty($v->poprox_bust))?1:0;
			$theParams['cup'] = (!empty($v->poprox_cup))?1:0;
			$theParams['kitty'] = (!empty($v->poprox_kitty))?1:0;
			$theParams['rate'] = (!empty($v->poprox_rate))?1:0;
			$theParams['incall'] = (!empty($v->poprox_incall))?1:0;
			$theParams['outcall'] = (!empty($v->poprox_outcall))?1:0;
			$theParams['screening'] = (!empty($v->poprox_screening))?1:0;
			$theParams['ad_text'] = (!empty($v->poprox_ad_text))?1:0;
			$theParams['city'] = (!empty($v->poprox_city))?1:0;
			
			$theParamTypes['score'] = PDO::PARAM_INT;
			$theParamTypes['user_comment'] = PDO::PARAM_STR;
			//modify the following fields to match our particular poprox table (all will be PDO::PARAM_INT;)
			$theParamTypes['adid'] = PDO::PARAM_INT;
			$theParamTypes['orgid'] = PDO::PARAM_INT;
			$theParamTypes['providerid'] = PDO::PARAM_INT;
			$theParamTypes['title'] = PDO::PARAM_INT;
			$theParamTypes['service'] = PDO::PARAM_INT;
			$theParamTypes['region'] = PDO::PARAM_INT;
			$theParamTypes['views'] = PDO::PARAM_INT;
			$theParamTypes['availability'] = PDO::PARAM_INT;
			$theParamTypes['name'] = PDO::PARAM_INT;
			$theParamTypes['phone'] = PDO::PARAM_INT;
			$theParamTypes['rb_inbox'] = PDO::PARAM_INT;
			$theParamTypes['email'] = PDO::PARAM_INT;
			$theParamTypes['website'] = PDO::PARAM_INT;
			$theParamTypes['ethnicity'] = PDO::PARAM_INT;
			$theParamTypes['age'] = PDO::PARAM_INT;
			$theParamTypes['eye_color'] = PDO::PARAM_INT;
			$theParamTypes['hair_color'] = PDO::PARAM_INT;
			$theParamTypes['build'] = PDO::PARAM_INT;
			$theParamTypes['height'] = PDO::PARAM_INT;
			$theParamTypes['bust'] = PDO::PARAM_INT;
			$theParamTypes['cup'] = PDO::PARAM_INT;
			$theParamTypes['kitty'] = PDO::PARAM_INT;
			$theParamTypes['rate'] = PDO::PARAM_INT;
			$theParamTypes['incall'] = PDO::PARAM_INT;
			$theParamTypes['outcall'] = PDO::PARAM_INT;
			$theParamTypes['screening'] = PDO::PARAM_INT;
			$theParamTypes['ad_text'] = PDO::PARAM_INT;
			$theParamTypes['city'] = PDO::PARAM_INT;
			
			$this->execDML($theSql,$theParams,$theParamTypes);
		}
	}
	
	public function qroxDashboardStats() {
		$theResults = array();
		$theResults['ads'] = $this->getDashboardStats($this->tnAds, $this->tnAds.'_history');
		$theResults['ads']['display_name'] = 'Ads';
		
		$theResults['photos'] = $this->getDashboardStats($this->tnPhotos);
		$theResults['photos']['display_name'] = 'Photos';
		
		$tnOrgs = $this->tbl_.'scraped_rb_org';
		$theResults['orgs'] = $this->getDashboardStats($tnOrgs, $tnOrgs.'_history');
		$theResults['orgs']['display_name'] = 'Organizations';
		
		$tnProviders = $this->tbl_.'scraped_rb_providers';
		$theResults['providers'] = $this->getDashboardStats($tnProviders, $tnProviders.'_history');
		$theResults['providers']['display_name'] = 'Providers';
		
		$tnReviews = $this->tbl_.'scraped_rb_reviews';
		$theResults['reviews'] = $this->getDashboardStats($tnReviews);
		$theResults['reviews']['display_name'] = 'Reviews';
		return $theResults;
	}
	
	protected function getSqlForGeoLocWhere() {
		return 's.city LIKE :loc OR s.text LIKE :ad_text OR s.region LIKE :region';
	}
	
	/**
	 * Specific query on geo-location stuff.
	 * @param string $aGeoLocString - search criteria
	 * @param string $aBaseLink - poprox link, replace %s with roxy ID.
	 * @return array Returns the set of data results.
	 */
	public function qroxGeoLoc($aGeoLocString) {
		$theResult = array();
		if (!empty($this->db) && !empty($aGeoLocString)) {
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
			// grab a set of results and pick a random one to link to
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT x.phone, x.num, x.ad_id AS an_ad_id FROM (';
			$theSql .= 'SELECT s.phone, count(s.phone) AS num, s.id as ad_id';
			$theSql .= "   FROM {$this->tnAds} AS s";
			$theSql .= '   WHERE (s.phone IS NOT NULL AND ('.$this->getSqlForGeoLocWhere().') )';
			$theSql .= '   GROUP BY s.phone ';
			$theSql .= ') AS x WHERE x.num>1 ORDER BY x.num DESC';
			$theParams['loc'] = '%'.$aGeoLocString.'%';
			$theParamTypes['loc'] = PDO::PARAM_STR;
			$theParams['ad_text'] = '%'.$aGeoLocString.'%';
			$theParamTypes['ad_text'] = PDO::PARAM_STR;
			$theParams['region'] = '%'.$aGeoLocString.'%';
			$theParamTypes['region'] = PDO::PARAM_STR;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			if ($rs) {
				$theResult = $rs->fetchAll();
			}
		}
		return $theResult;
	}
	
	protected function phoneDigitsToSiteFormat($aPhoneDigits) {
		if (strlen($aPhoneDigits)>7) {
			$aPhoneDigits = substr_replace($aPhoneDigits, '-', -7, 0);
		}
		return substr_replace($aPhoneDigits, '-', -4, 0);
	}
	
	protected function getSqlForPhoneAdList() {
		$theSql = 'SELECT id as roxy_id, "'.$this->site_id.'" as site_id, CONCAT("http://classifieds.myredbook.com/classified.php?adid=",adid) as url, phone';
		$theSql .= ' FROM '.$this->tnAds.' WHERE phone LIKE :phone';
		return $theSql;
	}
	
}//end class

}//end namespace

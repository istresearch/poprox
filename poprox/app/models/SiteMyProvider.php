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

class SiteMyProvider extends BaseModel {
	public $site_id = 'myproviderguide'; //this should the part of the URL that defines this site in roxy, e.g. [%site]/poprox/backpage/ads
	public $site_display_name = 'My Provider Guide';  //the nice display name to use
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		$this->tnAds = $this->tbl_.'scraped_myproviderguide_ads';
		$this->tnPhotos = $this->tbl_.'scraped_myproviderguide_photos';
		$this->tnAdPoprox = $this->tbl_.'poprox_myproviderguide_ads';
		$this->tnAdInfoBot = $this->tbl_.'machine_myproviderguide_ads';
		$this->tnAdQueue = str_replace($this->dbConnName,'roxy_scrape',$this->tbl_).'myproviderguide_queue'; //different db, same connection
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
			$theSql .= ', title=:title';
			$theSql .= ', ad_text=:ad_text';
			//modify the following fields to match our particular poprox table
			$theSql .= ', city=:city';
			$theSql .= ', user=:user';
			$theSql .= ', category=:category';
			$theSql .= ', email=:email';
			$theSql .= ', phone=:phone';
			$theSql .= ', website=:website';
			$theSql .= ', available=:available';
			$theSql .= ', hair=:hair';
			$theSql .= ', eyes=:eyes';
			$theSql .= ', height=:height';
			$theSql .= ', weight=:weight';
			$theSql .= ', cup=:cup';
			$theSql .= ', bust=:bust';
			$theSql .= ', waist=:waist';
			$theSql .= ', hips=:hips';
			$theSql .= ', build=:build';
			$theSql .= ', reviews1=:reviews1';
			$theSql .= ', reviews2=:reviews2';
			$theSql .= ', reviews3=:reviews3';
			$theSql .= ', reviews4=:reviews4';
			$theSql .= ', datecheck=:datecheck';
			$theSql .= ', preferred411=:preferred411';
			$theSql .= ', twitter=:twitter';
			$theSql .= ', youtube=:youtube';
			$theSql .= ', latitude=:latitude';
			$theSql .= ', longitude=:longitude';
				
			$theSql .= ' WHERE user_id=:user_id AND ad_id=:roxy_id';
			
			//param values to save
			$theParams['score'] = (!empty($v->poprox_likely))?1:0;
			$theParams['user_comment'] = $v->poprox_comment;
			$theParams['title'] = (!empty($v->poprox_title))?1:0;
			$theParams['ad_text'] = (!empty($v->poprox_ad_text))?1:0;
			//modify the following fields to match our particular poprox table
			$theParams['city'] = (!empty($v->poprox_city))?1:0;
			$theParams['user'] = (!empty($v->poprox_user))?1:0;
			$theParams['category'] = (!empty($v->poprox_category))?1:0;
			$theParams['email'] = (!empty($v->poprox_email))?1:0;
			$theParams['phone'] = (!empty($v->poprox_phone))?1:0;
			$theParams['website'] = (!empty($v->poprox_website))?1:0;
			$theParams['available'] = (!empty($v->poprox_available))?1:0;
			$theParams['hair'] = (!empty($v->poprox_hair))?1:0;
			$theParams['eyes'] = (!empty($v->poprox_eyes))?1:0;
			$theParams['height'] = (!empty($v->poprox_height))?1:0;
			$theParams['weight'] = (!empty($v->poprox_weight))?1:0;
			$theParams['cup'] = (!empty($v->poprox_cup))?1:0;
			$theParams['bust'] = (!empty($v->poprox_bust))?1:0;
			$theParams['waist'] = (!empty($v->poprox_waist))?1:0;
			$theParams['hips'] = (!empty($v->poprox_hips))?1:0;
			$theParams['build'] = (!empty($v->poprox_build))?1:0;
			$theParams['reviews1'] = (!empty($v->poprox_reviews1))?1:0;
			$theParams['reviews2'] = (!empty($v->poprox_reviews2))?1:0;
			$theParams['reviews3'] = (!empty($v->poprox_reviews3))?1:0;
			$theParams['reviews4'] = (!empty($v->poprox_reviews4))?1:0;
			$theParams['datecheck'] = (!empty($v->poprox_datecheck))?1:0;
			$theParams['preferred411'] = (!empty($v->poprox_preferred411))?1:0;
			$theParams['twitter'] = (!empty($v->poprox_twitter))?1:0;
			$theParams['youtube'] = (!empty($v->poprox_youtube))?1:0;
			$theParams['latitude'] = (!empty($v->poprox_latitude))?1:0;
			$theParams['longitude'] = (!empty($v->poprox_longitude))?1:0;
				
			$theParamTypes['score'] = PDO::PARAM_INT;
			$theParamTypes['user_comment'] = PDO::PARAM_STR;
			$theParamTypes['title'] = PDO::PARAM_INT;
			$theParamTypes['ad_text'] = PDO::PARAM_INT;
			//modify the following fields to match our particular poprox table (all will be PDO::PARAM_INT;)
			$theParamTypes['city'] = PDO::PARAM_INT;
			$theParamTypes['user'] = PDO::PARAM_INT;
			$theParamTypes['category'] = PDO::PARAM_INT;
			$theParamTypes['email'] = PDO::PARAM_INT;
			$theParamTypes['phone'] = PDO::PARAM_INT;
			$theParamTypes['website'] = PDO::PARAM_INT;
			$theParamTypes['available'] = PDO::PARAM_INT;
			$theParamTypes['hair'] = PDO::PARAM_INT;
			$theParamTypes['eyes'] = PDO::PARAM_INT;
			$theParamTypes['height'] = PDO::PARAM_INT;
			$theParamTypes['weight'] = PDO::PARAM_INT;
			$theParamTypes['cup'] = PDO::PARAM_INT;
			$theParamTypes['bust'] = PDO::PARAM_INT;
			$theParamTypes['waist'] = PDO::PARAM_INT;
			$theParamTypes['hips'] = PDO::PARAM_INT;
			$theParamTypes['build'] = PDO::PARAM_INT;
			$theParamTypes['reviews1'] = PDO::PARAM_INT;
			$theParamTypes['reviews2'] = PDO::PARAM_INT;
			$theParamTypes['reviews3'] = PDO::PARAM_INT;
			$theParamTypes['reviews4'] = PDO::PARAM_INT;
			$theParamTypes['datecheck'] = PDO::PARAM_INT;
			$theParamTypes['preferred411'] = PDO::PARAM_INT;
			$theParamTypes['twitter'] = PDO::PARAM_INT;
			$theParamTypes['youtube'] = PDO::PARAM_INT;
			$theParamTypes['latitude'] = PDO::PARAM_INT;
			$theParamTypes['longitude'] = PDO::PARAM_INT;
				
			$this->execDML($theSql,$theParams,$theParamTypes);
		}
	}
	
	protected function getSqlForGeoLocWhere() {
		return 's.url LIKE :loc OR s.body LIKE :ad_text OR s.city LIKE :region';
	}
	
	
}//end class

}//end namespace

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

class SiteCraigslist extends BaseModel {
	public $site_id = 'craigslist'; //this should the part of the URL that defines this site in roxy
	public $site_display_name = 'Craigslist';

	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		$this->tnAds = $this->tbl_.'scraped_craigslist_ads';
		$this->tnPhotos = $this->tbl_.'scraped_craigslist_photos';
		$this->tnAdPoprox = $this->tbl_.'poprox_craigslist_ads';
		$this->tnAdInfoBot = $this->tbl_.'machine_craigslist_ads';
		$this->tnAdQueue = str_replace($this->dbConnName,'roxy_scrape',$this->tbl_).'craigslist_queue'; //different db, same connection
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
			$theSql .= ', title=:title';
			$theSql .= ', body=:ad_text';
			$theSql .= ', city=:city';
			$theSql .= ', url=:url';
			$theSql .= ', email=:email';
			$theSql .= ', license=:license';
			
			$theSql .= ' WHERE user_id=:user_id AND ad_id=:roxy_id';
			
			//param values to save
			$theParams['score'] = (!empty($v->poprox_likely))?1:0;
			$theParams['user_comment'] = $v->poprox_comment;
			//modify the following fields to match our particular poprox table
			$theParams['title'] = (!empty($v->poprox_title))?1:0;
			$theParams['ad_text'] = (!empty($v->poprox_ad_text))?1:0;
			$theParams['city'] = (!empty($v->poprox_city))?1:0;
			$theParams['url'] = (!empty($v->poprox_url))?1:0;
			$theParams['email'] = (!empty($v->poprox_email))?1:0;
			$theParams['license'] = (!empty($v->poprox_license))?1:0;
			
			$theParamTypes['score'] = PDO::PARAM_INT;
			$theParamTypes['user_comment'] = PDO::PARAM_STR;
			//modify the following fields to match our particular poprox table (all will be PDO::PARAM_INT;)
			$theParamTypes['title'] = PDO::PARAM_INT;
			$theParamTypes['ad_text'] = PDO::PARAM_INT;
			$theParamTypes['city'] = PDO::PARAM_INT;
			$theParamTypes['url'] = PDO::PARAM_INT;
			$theParamTypes['email'] = PDO::PARAM_INT;
			$theParamTypes['license'] = PDO::PARAM_INT;
			
			$this->execDML($theSql,$theParams,$theParamTypes);
		}
	}
	
	protected function getSqlForGeoLocWhere() {
		return 's.city LIKE :loc OR s.body LIKE :ad_text OR s.url LIKE :region';
	}
	
	
}//end class

}//end namespace

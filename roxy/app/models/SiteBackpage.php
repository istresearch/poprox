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

class SiteBackpage extends BaseModel {
	public $site_id = 'backpage'; //this should the part of the URL that defines this site in roxy
	public $site_display_name = 'Backpage';

	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		$this->tnAds = $this->tbl_.'scraped_backpage_ads';
		$this->tnPhotos = $this->tbl_.'scraped_backpage_photos';
		$this->tnAdPoprox = $this->tbl_.'poprox_backpage_ads';
		$this->tnAdInfoBot = $this->tbl_.'machine_backpage_ads';
		$this->tnAdQueue = str_replace($this->dbConnName,'roxy_scrape',$this->tbl_).'backpage_queue'; //different db, same connection
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
			$theSql .= ', body=:ad_text';
			//modify the following fields to match our particular poprox table
			$theSql .= ', age=:age';
			$theSql .= ', location=:location';
			$theSql .= ', region=:region';
			
			$theSql .= ' WHERE user_id=:user_id AND ad_id=:roxy_id';
			
			//param values to save
			$theParams['score'] = (!empty($v->poprox_likely))?1:0;
			$theParams['user_comment'] = $v->poprox_comment;
			$theParams['title'] = (!empty($v->poprox_title))?1:0;
			$theParams['ad_text'] = (!empty($v->poprox_ad_text))?1:0;
			//modify the following fields to match our particular poprox table
			$theParams['age'] = (!empty($v->poprox_age))?1:0;
			$theParams['location'] = (!empty($v->poprox_location))?1:0;
			$theParams['region'] = (!empty($v->poprox_region))?1:0;
			
			$theParamTypes['score'] = PDO::PARAM_INT;
			$theParamTypes['user_comment'] = PDO::PARAM_STR;
			$theParamTypes['title'] = PDO::PARAM_INT;
			$theParamTypes['ad_text'] = PDO::PARAM_INT;
			//modify the following fields to match our particular poprox table (all will be PDO::PARAM_INT;)
			$theParamTypes['age'] = PDO::PARAM_INT;
			$theParamTypes['location'] = PDO::PARAM_INT;
			$theParamTypes['region'] = PDO::PARAM_INT;
			
			$this->execDML($theSql,$theParams,$theParamTypes);
		}
	}

	/**
	 * Given an "ad ID" and backpage Region, return the roxy ID of the record.
	 * @param mixed $aAdId - the "adid" of the ad (specific to the site scraped)
	 * @param string $aRegion - the *.backpage.com part of the domain.
	 */
	public function getRoxyId($aAdId, $aRegion) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT id FROM '.$this->tnAds.' WHERE adid=:adid AND region=:region ORDER BY id LIMIT 1';
		$theParams['adid'] = $aAdId;
		$theParamTypes['adid'] = PDO::PARAM_STR;
		$theParams['region'] = $aRegion;
		$theParamTypes['region'] = PDO::PARAM_STR;
		$rs = $this->query($theSql,$theParams,$theParamTypes);
		if ($rs) {
			$theResult = $rs->fetchAll(PDO::FETCH_COLUMN,0);
			$rs->closeCursor();
		}
		if ($theResult!==FALSE && $theResult!==null) {
			return $theResult[0];
		} else {
			return null;
		}
	}
	
	/* Since the current data set is wonky, use alternate query for now
	public function qroxNumAdsByWeek($aRegionList, $aWeeksAgoLimit) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		$theParams = (is_array($aRegionList)) ? $aRegionList : explode(',',$aRegionList);
		$theParamTypes = array_fill(0,count($theParams),PDO::PARAM_STR);
		$thePostDateSql = "IFNULL(STR_TO_DATE(post_time,'%W, %M %e, %Y %l:%i %p'), STR_TO_DATE(`timestamp`,'%W, %e %M %Y, %l:%i %p'))";
		$theSql = 'SELECT region, COUNT(region) as NumAds, YEARWEEK('.$thePostDateSql.',3) AS WeekBasis FROM '.$this->tnAds;
		if (!empty($aWeeksAgoLimit)) {
			$theSql .= ' WHERE YEARWEEK('.$thePostDateSql.',3) >= YEARWEEK(TIMESTAMPADD(SQL_TSI_WEEK,-'.$aWeeksAgoLimit.',NOW()),3)';
		} else {
			$theSql .= ' WHERE YEARWEEK('.$thePostDateSql.',3) = YEARWEEK(NOW(),3)';
		}
		$theSql .= '   AND ('.$thePostDateSql.' IS NOT NULL)';
		$theSql .= '   AND region IN ('.str_repeat('?,',count($aRegionList)-1).'?'.')';
		$theSql .= ' GROUP BY region, WeekBasis ORDER BY region, WeekBasis DESC';
//		$rs = $this->query($theSql,$theParams,$theParamTypes);
		$theStatement = $this->db->prepare($theSql);
		$i = 1;
		foreach ($theParams as $theParamValue) {
			$theStatement->bindValue($i++,$theParamValue);
		}
		$theStatement->execute();
		$rs = $theStatement;
		if ($rs) {
			$theResult = $rs->fetchAll();
			$rs->closeCursor();
		}
		if ($theResult!==FALSE && $theResult!==null) {
			return $theResult;
		} else {
			return null;
		}
	}
	*/
	/*
	SELECT count(distinct(a.adid)) from (
	SELECT adid FROM `scraped_backpage_ads` WHERE region = 'chicago' or (region in ('bloomington', 'carbondale', 'chambana', 'chicago', 'decatur', 'lasalle', 'mattoon', 'peoria', 'rockford', 'springfieldil', 'quincy', 'illinois') and location like '%chicago%')
	union
	SELECT adid FROM `scraped_backpage_ads_history` WHERE region = 'chicago' or (region in ('bloomington', 'carbondale', 'chambana', 'chicago', 'decatur', 'lasalle', 'mattoon', 'peoria', 'rockford', 'springfieldil', 'quincy', 'illinois') and location like '%chicago%')
	) as a
	*/
	public function qroxNumAdsByWeek($aRegionList, $aWeeksAgoLimit) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		$theParams = (is_array($aRegionList)) ? $aRegionList : explode(',',$aRegionList);
		$theParamTypes = array_fill(0,count($theParams),PDO::PARAM_STR);
		$thePostDateSql = "IFNULL(STR_TO_DATE(post_time,'%W, %M %e, %Y %l:%i %p'), STR_TO_DATE(`timestamp`,'%W, %e %M %Y, %l:%i %p'))";

		$theSql = 'SELECT region, COUNT(distinct(adid)) as NumAds, YEARWEEK('.$thePostDateSql.',3) AS WeekBasis FROM '.$this->tnAds;
		if (!empty($aWeeksAgoLimit)) {
			$theSql .= ' WHERE YEARWEEK('.$thePostDateSql.',3) >= YEARWEEK(TIMESTAMPADD(SQL_TSI_WEEK,-'.$aWeeksAgoLimit.',NOW()),3)';
		} else {
			$theSql .= ' WHERE YEARWEEK('.$thePostDateSql.',3) = YEARWEEK(NOW(),3)';
		}
		$theSql .= '   AND ('.$thePostDateSql.' IS NOT NULL)';
		$theSql .= '   AND region IN ('.str_repeat('?,',count($theParams)-1).'?'.')';
		$theSql .= ' GROUP BY region, WeekBasis ORDER BY region, WeekBasis DESC';
		//		$rs = $this->query($theSql,$theParams,$theParamTypes);
		$theStatement = $this->db->prepare($theSql);
		$i = 1;
		foreach ($theParams as $theParamValue) {
			$theStatement->bindValue($i++,$theParamValue);
		}
		$theStatement->execute();
		$rs = $theStatement;
		if ($rs) {
			$theResult = $rs->fetchAll();
			$rs->closeCursor();
		}
		if ($theResult!==FALSE && $theResult!==null) {
			return $theResult;
		} else {
			return null;
		}
	}
	
	
}//end class

}//end namespace

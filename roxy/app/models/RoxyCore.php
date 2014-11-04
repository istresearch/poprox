<?php
namespace ISTResearch_Roxy\models;
use BitsTheater\Model as BaseModel;
use com\blackmoonit\Strings;
use com\blackmoonit\database\DbUtils;
use com\blackmoonit\database\FinallyCursor;
use com\blackmoonit\exceptions\DbException;
use \PDO;
use \PDOStatement;
use \PDOException;
use \DateTime;
use \DateInterval;
{//namespace begin

class RoxyCore extends BaseModel {
	public $dbConnName = 'memex_ist';
	//descendants override these vars, if output shows these values, something is most likely wrong
	public $site_id = 'roxycore'; //this should the part of the URL that defines this site in roxy, e.g. [%site]/poprox/backpage/ads
	public $site_display_name = 'Roxy'; //the nice display name to use
	//descendants define these vars
	public $tnAds;        //scraped_*_ads table
	public $tnPhotos;     //scraped_*_photos table
	public $tnAdPoprox;   //poprox_*_ads table
	public $tnAdInfoBot;  //machine_*_ads table
	public $tnAdQueue;    //*_queue table
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		$this->tbl_ = $this->myDbConnInfo->dbName.'.'.$this->tbl_;
	}
	
	public function setupModel() {
		//roxy tables should already exist, do not do anything to create it
		switch ($this->dbType()) {
		case 'mysql': default:
		}
	}
	
	public function isEmpty($aTableName=null) {
		return parent::isEmpty( empty($aTableName) ? $this->tnAds : $aTableName );
	}
	
	public function getSiteId() {
		return $this->site_id;
	}
	
	public function getSiteDisplayName() {
		return $this->site_display_name;
	}
	
	protected function normalizeAdRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
			
			if (!is_null($aRow['adid'])) $aRow['adid']+=0;

			if (!empty($aRow['body'])) {
				$aRow['ad_text'] = $aRow['body'];
				unset($aRow['body']);
			} else if (!empty($aRow['text'])) {
				$aRow['ad_text'] = $aRow['text'];
				unset($aRow['text']);
			}
		}
	}
	
	protected function normalizePoproxRow($aRoxyIdFieldName, &$aRow) {
		if (!empty($aRow)) {
			$aRow['user_id']+=0;
			$aRow[$aRoxyIdFieldName]+=0;
			$aRow['score']+=0;
			if (isset($aRow['body'])) {
				$aRow['ad_text'] = $aRow['body'];
				unset($aRow['body']);
			} else if (!empty($aRow['text'])) {
				$aRow['ad_text'] = $aRow['text'];
				unset($aRow['text']);
			}
		}
	}
	
	/**
	 * Retrieve the poprox data for a given user_id/roxy_id.
	 * @param int $aRoxyId - Roxy ID for the ad in question.
	 * @param int $aAcctId - User ID for the poprox data.
	 * @throws DbException
	 * @return array Returns array of row data or NULL if not found.
	 */
	public function getPoproxDataForAd($aRoxyId, $aAcctId) {
		$theResult = null;
		if (!empty($this->db) && !empty($aRoxyId)) try {
			$theRoxyId = $aRoxyId+0;
			$theUserId = $aAcctId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
				
			//get the poprox data
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$this->tnAdPoprox.' WHERE user_id=:user_id AND ad_id=:roxy_id';
			$theParams['user_id'] = $theUserId;
			$theParamTypes['user_id'] = PDO::PARAM_INT;
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			if (($theResult=$rs->fetch()) !== false) {
				$this->normalizePoproxRow('ad_id',$theResult);
			} else {
				$theResult = null;
			}
			$rs->closeCursor();	
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getPoproxDataForAd='.$theRoxyId.' failed.');
		}
		return $theResult;
	}
	
	/**
	 * Stats to compute:
	 * Total
	 * Total Updates
	 * New  this Week/Day
	 * Updated  this Week/Day
	 * Time since last imported
	 * (Often when we see an ad again, it's an update of the same ad. 
	 * We update the main table and archive a copy of the previous data.)
	 * 
	 * @param string $aRoxyTn - table for stats
	 * @param string $aRoxyTnHistory - history table for update stats
	 */
	public function getDashboardStats($aRoxyTn, $aRoxyTnHistory=null) {
		if (empty($this->db))
			return array();
		$theResult = array();

		//time since last import
		$theSql = 'SELECT MAX(timestamp) AS last_datetime FROM '.$aRoxyTn.' LIMIT 1';
		$theRow = $this->getTheRow($theSql);
		if ($theRow) {
			$theResult['last_datetime'] = $theRow['last_datetime'];
		}
		
		//scraper queue depth
		$theResult['queue_depth'] = null;
		if (!empty($this->tnAdQueue)) {
			//TODO scrapers are in a different database yet... need to get credentials and whatnot like what was needed for memex_ist
			$theSql = 'SELECT COUNT(id) AS queue_depth FROM '.$this->tnAdQueue.' WHERE done=0 LIMIT 1';
			$theRow = $this->getTheRow($theSql);
			if ($theRow) {
				$theResult['queue_depth'] = $theRow['queue_depth'];
			}
			/* */
		}
		
		//total
		$theSql = 'SELECT COUNT(id) AS total FROM '.$aRoxyTn.' LIMIT 1';
		$theRow = $this->getTheRow($theSql);
		if ($theRow) {
			$theResult['total'] = $theRow['total']+0;
		}

		//total updates
		if (!empty($aRoxyTnHistory)) {
			$theSql = 'SELECT COUNT(id) AS total FROM '.$aRoxyTnHistory.' LIMIT 1';
			$theRow = $this->getTheRow($theSql);
			if ($theRow) {
				$theResult['total_updates'] = $theRow['total']+0;
			} else {
				$theResult['total_updates'] = 0;
			}
		} else {
			$theResult['total_updates'] = null;
		}

		//total this week (last 7 days)
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT COUNT(id) AS total FROM '.$aRoxyTn.' WHERE timestamp>=:ts'.' LIMIT 1';
		$theParams['ts'] = DbUtils::cnvDaysToDateTimeStr(7);
		$theParamTypes['ts'] = PDO::PARAM_STR;
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		if ($theRow) {
			$theResult['total_last_7_days'] = $theRow['total']+0;
		}

		//total last 24 hours
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT COUNT(id) AS total FROM '.$aRoxyTn.' WHERE timestamp>=:ts'.' LIMIT 1';
		$theParams['ts'] = DbUtils::cnvDaysToDateTimeStr(1);
		$theParamTypes['ts'] = PDO::PARAM_STR;
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		if ($theRow) {
			$theResult['total_last_24_hrs'] = $theRow['total']+0;
		}

		if (!empty($aRoxyTnHistory)) {
			//total updated this week (last 7 days)
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT COUNT(id) AS total FROM '.$aRoxyTnHistory.' WHERE timestamp>=:ts'.' LIMIT 1';
			$theParams['ts'] = DbUtils::cnvDaysToDateTimeStr(7);
			$theParamTypes['ts'] = PDO::PARAM_STR;
			$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
			if ($theRow) {
				$theResult['total_updated_last_7_days'] = $theRow['total']+0;
			} else {
				$theResult['total_updated_last_7_days'] = 0;
			}
		} else {
			$theResult['total_updated_last_7_days'] = null;
		}

		if (!empty($aRoxyTnHistory)) {
			//total updated last 24 hours
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT COUNT(id) AS total FROM '.$aRoxyTnHistory.' WHERE timestamp>=:ts'.' LIMIT 1';
			$theParams['ts'] = DbUtils::cnvDaysToDateTimeStr(1);
			$theParamTypes['ts'] = PDO::PARAM_STR;
			$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
			if ($theRow) {
				$theResult['total_updated_last_24_hrs'] = $theRow['total']+0;
			} else {
				$theResult['total_updated_last_24_hrs'] = 0;
			}
		} else {
			$theResult['total_updated_last_24_hrs'] = null;				
		}

		return $theResult;
	}
	
	/**
	 * Report the suite of statistics for our particular site (new ads, updates, and photos are default).
	 * @return array Returns an array of statistics as well as a display_name for the stat area.
	 */
	public function qroxDashboardStats() {
		$theResults = array();
		$theResults['ads'] = $this->getDashboardStats($this->tnAds, $this->tnAds.'_history');
		$theResults['ads']['display_name'] = 'Ads';
		$theResults['photos'] = $this->getDashboardStats($this->tnPhotos);
		$theResults['photos']['display_name'] = 'Photos';
		return $theResults;
	}
	
	/**
	 * Given an "ad ID", return the roxy ID of the record.
	 * @param mixed $aAdId - the "adid" of the ad (specific to the site scraped)
	 */
	public function getRoxyId($aAdId) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT id FROM '.$this->tnAds.' WHERE adid=:adid ORDER BY id LIMIT 1';
		$theParams['adid'] = $aAdId;
		$theParamTypes['adid'] = PDO::PARAM_STR;
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

	/**
	 * Randomly pick a roxy_id from the last 100-200 entries. 
	 * The most recent 100 are skipped as they may not have images yet.
	 * @param int $aAcctId - user account of person using poprox
	 * @return number Return a random Roxy ID of an ad.
	 */
	public function poproxRandomAd($aAcctId) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		// grab a set of results and pick a random one
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT c.id FROM '.$this->tnAds.' as c';
		$theSql .= ' LEFT JOIN '.$this->tnAdPoprox.' AS p ON p.user_id=:user_id AND c.id=p.ad_id';
		$theSql .= ' WHERE p.ad_id IS NULL ORDER BY c.id DESC LIMIT 200';
		$theParams['user_id'] = $aAcctId+0;
		$theParamTypes['user_id'] = PDO::PARAM_INT;
		$rs = $this->query($theSql,$theParams,$theParamTypes);
		if ($rs) {
			$theResult = $rs->fetchAll(PDO::FETCH_COLUMN,0);
			$rs->closeCursor();
		}
		if ($theResult!==FALSE && $theResult!==null) {
			$idx = rand(100,count($theResult)-1);
			//print('rand='.$idx.' r='.$theResult[$idx]+0);
			return $theResult[$idx]+0;
		} else {
			return null;
		}
	}
	
	/**
	 * Get the scraped ad data.
	 * @param int $aRoxyId - the Roxy ID
	 * @throws DbException
	 * @return array Returns an array of row data or array() if none found.
	 */
	public function getScrapedAd($aRoxyId) {
		$theResult = array();
		if (!empty($this->db) && !empty($aRoxyId)) try {
			$theRoxyId = $aRoxyId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
		
			//get the primary ad data
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$this->tnAds.' WHERE id=:roxy_id';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			if (($theRow = $rs->fetch()) !== false) {
				$this->normalizeAdRow($theRow);
				$theResult = $theRow;
			}
			$rs->closeCursor();
		
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getScrapedAd='.$theRoxyId.' failed.');
		}
		return $theResult;
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
			$theSql = 'SELECT replace(location,\'?\',\'%3F\') AS imgsrc FROM '.$this->tnPhotos.' WHERE ad_id=:roxy_id';
			$theSql .= ' AND location IS NOT NULL';
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
	
	/**
	 * Retrieve the extracted / cleaned ad info.
	 * @param int $aRoxyId - the Roxy ID
	 * @throws DbException
	 * @return array Returns an array of Roxy bot-extracted ad information rows.
	 */
	public function getAdRoxyInfo($aRoxyId) {
		$theResult = array();
		if (!empty($this->db) && !empty($this->tnAdInfoBot) && !empty($aRoxyId)) try {
			$theRoxyId = $aRoxyId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
	
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$this->tnAdInfoBot.' WHERE ad_id=:roxy_id';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$theSortIndex = array();
			$i = 1;
			while (($theRow = $rs->fetch()) !== false) {
				if (!empty($theRow['phone'])) {
					$theResult['Phone '.$i] = $theRow['phone'];
					$theSortIndex[] = Strings::format('01-%02d',$i); 
				}
				if (!empty($theRow['phone_text'])) {
					$theResult['Phone '.$i.' Orig'] = $theRow['phone_text'];
					$theSortIndex[] = Strings::format('01-%02d-text',$i); 
				}
				if (!empty($theRow['email'])) {
					$theResult['Email '.$i] = $theRow['email'];
					$theSortIndex[] = Strings::format('02-%02d',$i); 
				}
				if (!empty($theRow['website'])) {
					$theResult['Embedded Website '.$i] = $theRow['website'];
					$theSortIndex[] = Strings::format('03-%02d',$i); 
				}
				if (!empty($theRow['ethnicity'])) {
					$theResult['Ethnicity '.$i] = $theRow['ethnicity'];
					$theSortIndex[] = Strings::format('04-%02d',$i); 
				}
				if (!empty($theRow['rate'])) {
					$theResult['Rate '.$i] = $theRow['rate'];
					$theSortIndex[] = Strings::format('05-%02d',$i); 
				}
				if (!empty($theRow['height'])) {
					$theResult['Height '.$i.' (cm)'] = $theRow['height'];
					$theSortIndex[] = Strings::format('06-%02d',$i); 
				}
				if (!empty($theRow['weight'])) {
					$theResult['Weight '.$i.' (kg)'] = $theRow['weight'];
					$theSortIndex[] = Strings::format('07-%02d',$i); 
				}
				if (!empty($theRow['age'])) {
					$theResult['Age '.$i] = $theRow['age'];
					$theSortIndex[] = Strings::format('08-%02d',$i); 
				}
				if (!empty($theRow['name'])) {
					$theResult['Name '.$i] = $theRow['name'];
					$theSortIndex[] = Strings::format('09-%02d',$i); 
				}
				$i += 1;
			}
			$rs->closeCursor();
			//sort the resulting hodge-podge list by $theSortIndex
			array_multisort($theSortIndex, $theResult);
							
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getAdRoxyInfo='.$theRoxyId.' failed.');
		}
		return $theResult;
	}
	
	/**
	 * Retrieves a complete ad with photos and extracted information.
	 * @param int $aRoxyId - the Roxy ID
	 * @throws DbException
	 * @return array Returns an array of ad data with 'photos' and 'extracted_info'.
	 */
	public function getCompleteAd($aRoxyId) {
		$theResult = array();
		if (!empty($this->db) && !empty($aRoxyId)) {
			//get the primary data
			$theResult = $this->getScrapedAd($aRoxyId);
			if (!empty($theResult)) {
				$theResult['photos'] = $this->getAdPhotos($aRoxyId);
				$theResult['extracted_info'] = array();
				$theResult['extracted_info']['Roxy'] = $this->getAdRoxyInfo($aRoxyId);
				//TODO: would historical info be separated from "current"?
				//TODO: human micro-task extracted info would be retrieved here.
			}
		}
		return $theResult;
	}
	
	public function isExistAd($aRoxyId) {
		$theResult = false;
		if (!empty($this->db) && !empty($aRoxyId)) try {
			$theRoxyId = $aRoxyId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
				
			//get the primary ad data
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT id FROM '.$this->tnAds.' WHERE id=:roxy_id';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			if (($theRow = $rs->fetch()) !== false) {
				$theResult = true;
			}
			$rs->closeCursor();
		
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'isExistAd='.$theRoxyId.' failed.');
		}
		return $theResult;		
	}
	
	protected function getSqlForGeoLocWhere() {
		return 's.location LIKE :loc OR s.body LIKE :ad_text OR s.region LIKE :region';
	}
	
	/**
	 * Specific query on geo-location stuff.
	 * @param string $aGeoLocString - search criteria
	 * @param string $aBaseLink - poprox link, replace %s with roxy ID.
	 * @return array Returns the set of data results.
	 */
	public function qroxGeoLoc($aGeoLocString) {
		$theResult = array();
		if (!empty($this->db) && !empty($aGeoLocString) && $this->exists($this->tnAdInfoBot)) {
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
			// grab a set of results and pick a random one to link to
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT x.phone, x.num, x.ad_id AS an_ad_id FROM (';
			$theSql .= 'SELECT m.phone, count(m.phone) AS num, m.ad_id ';
			$theSql .= "   FROM {$this->tnAdInfoBot} AS m JOIN {$this->tnAds} AS s ON m.ad_id=s.id ";
			$theSql .= '   WHERE (m.phone IS NOT NULL AND ('.$this->getSqlForGeoLocWhere().') )';
			$theSql .= '   GROUP BY m.phone ';
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
		return $aPhoneDigits;
	}
	
	protected function getSqlForPhoneAdList() {
		$theSql = 'SELECT m.ad_id as roxy_id, "'.$this->site_id.'" as site_id, s.url, m.phone';
		$theSql .= ' FROM '.$this->tnAdInfoBot.' as m JOIN '.$this->tnAds.' as s ON m.ad_id=s.id';
		$theSql .= ' WHERE m.phone LIKE :phone';
		return $theSql;
	}
	
	/**
	 * Report on what ads have a particular phone number listed within it.
	 * @param string $aPhoneDigits - phone number as just digits [0-9]
	 * @throws DbException
	 * @return array Returns rows as roxy_id, site_id, ad_url, phone.
	 */
	public function qroxPhoneAdList($aPhoneDigits) {
		$theResult = array();	
		$thePhoneFormat = $this->phoneDigitsToSiteFormat($aPhoneDigits);
		if (!empty($this->db) && !empty($thePhoneFormat)) { 
			try {
				$rs = null;
				$myFinally = FinallyCursor::forDbCursor($rs);				
				$theParams = array();
				$theParamTypes = array();
				$theSql = $this->getSqlForPhoneAdList();
				$theParams['phone'] = '%'.$thePhoneFormat;
				$theParamTypes['phone'] = PDO::PARAM_STR;
				$rs = $this->query($theSql,$theParams,$theParamTypes);
				$rs->setFetchMode(PDO::FETCH_ASSOC);
				$theResult = $rs->fetchAll();
				$rs->closeCursor();				
			} catch (PDOException $pdoe) {
				throw new DbException($pdoe, 'getPhoneAdList='.$thePhoneFormat.' failed.');
			}
		}
		return $theResult;
	}
	
	/**
	 * Get a random phone number to micro-task.
	 * @return array Returns an empty array if none were found, else returns
	 * array['roxy_id', 'phone', 'phone_text'].
	 */
	public function getRandomPhoneToScore() {
		$theResult = array();
		if (!empty($this->db) && $this->exists($this->tnAdInfoBot)) {
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
				
			$theFromWhereSql = " FROM {$this->tnAdInfoBot} AS m";
			$theFromWhereSql .= ' WHERE m.phone IS NOT NULL';
			$theFromWhereSql .= ' AND length(m.phone)=10';
			$theFromWhereSql .= ' AND m.phone_text <> "unknown"';
			$theFromWhereSql .= ' AND length(m.phone_text) < 200';
			$theFromWhereSql .= ' AND (length(TRIM(m.phone_text))-length(TRIM(m.phone))) > 5';
	
			$theSql = 'SELECT count(m.ad_id)'.$theFromWhereSql;
			if (($rs=$this->query($theSql)) !== false) {
				$theCount = $rs->fetch(PDO::FETCH_COLUMN,0)+0;
				//randomly pick from 0 to $theCount-1
				$x = rand(0,$theCount-1);
				//get that random record
				$theSql = "SELECT m.id as attr_id, m.ad_id as phone_id, m.phone, REPLACE(REPLACE(TRIM(m.phone_text), '\n', ''), '\r', '') as phone_text".$theFromWhereSql.' LIMIT '.$x.', 1';
				$theResult = $this->getTheRow($theSql);
			}
		}
		return $theResult;
	}
	
	
}//end class

}//end namespace

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
use BitsTheater\costumes\SqlBuilder;
{//namespace begin

class MemexHt extends BaseModel {
	public $dbConnName = 'memex_ht';
	//descendants override these vars, if output shows these values, something is most likely wrong
	public $site_id = 'memexht'; //this should the part of the URL that defines this site in roxy, e.g. [%site]/poprox/backpage/ads
	public $site_display_name = 'MemexHT'; //the nice display name to use
	//ad related tables
	public $tnAds;
	public $tnAdAttrs;
	public $tnImages;
	public $tnImageAttrs;
	public $tnReviews;
	public $tnReviewAttrs;
	
	//metadata tables
	public $tnSources;
	public $tnSourceAttrs;
	
	//poprox related tables
	public $tnAdPoprox;   //poprox_*_ads table
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		$this->tbl_ = $this->myDbConnInfo->dbName.'.'.$this->tbl_;
	
		$this->tnAds = $this->tbl_.'ads';
		$this->tnAdAttrs = $this->tbl_.'ads_attributes';
		$this->tnImages = $this->tbl_.'images';
		$this->tnImageAttrs = $this->tbl_.'images_attributes';
		$this->tnReviews = $this->tbl_.'reviews';
		$this->tnReviewAttrs = $this->tbl_.'reviews_attributes';
		$this->tnSources = $this->tbl_.'sources';
		$this->tnSourceAttrs = $this->tbl_.'sources_attributes';
	}
	
	public function setupModel() {
		//tables should already exist, do not do anything to create it
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
	
	/*
CREATE TABLE IF NOT EXISTS `sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Auto incremented row identifier, unique to table.',
  `name` varchar(32) NOT NULL COMMENT 'Name of the site being scraped',
  `scrapedelay` smallint(6) unsigned NOT NULL DEFAULT '10' COMMENT 'Delay in seconds between the scraper touching the website to avoid being banned.',
  `ad-photourlprefix` varchar(128) DEFAULT NULL COMMENT 'Prepend this to what the adphotoregex selects in the case of relative URLs so the scrapers know what to get.',
  `ad-errorurl` varchar(64) DEFAULT NULL COMMENT 'If this is in the URL then it''s an error page and should not be imported. Sometimes there is more then one, also check attributes table.',
  `ad-requiredattribute` varchar(32) NOT NULL DEFAULT 'text' COMMENT 'If the page does not have this attribute then it''s an error and should not be imported. Sometimes there is more then one, also check attributes table.',
  `ad-revisionfield` varchar(32) NOT NULL DEFAULT 'url' COMMENT 'Use this field to determine if it''s a new revision of a prior ad. Usually URL. Sometimes there is more then one, also check attributes table.',
  `modtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the most recent change to the site.',
  PRIMARY KEY (`id`),
  KEY `name` (`name`(4))
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=24 ;
	 */
	protected function normalizeSourceRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
			$aRow['source_id'] = $aRow['id'];
			$aRow['display_name'] = $aRow['name']; //default display_name to name
			
			if (!is_null($aRow['scrapedelay'])) $aRow['scrapedelay']+=0;

			//MySQL date time string
			$aRow['updated_ts'] = $aRow['modtime'];
		}
	}

	/*
CREATE TABLE IF NOT EXISTS `sources_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the attribute entry',
  `sources_id` int(11) unsigned NOT NULL COMMENT 'Parent ID for this attribute.',
  `regex` tinyint(1) DEFAULT NULL COMMENT '1 if the value is to be treated as regex.',
  `regexpriority` tinyint(1) DEFAULT NULL COMMENT 'The order the regex should be evaluated, lower is sooner. ',
  `attribute` varchar(32) NOT NULL COMMENT 'Attribute name (Age, location, etc.)',
  `value` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT 'Value of the attribute.',
  `modtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the most recent change to the attribute.',
  PRIMARY KEY (`id`),
  KEY `attribute` (`attribute`(4)),
  KEY `value` (`value`(16)),
  KEY `reviews_id` (`sources_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=996 ;
	 */
	protected function normalizeSourceAttrRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
			$aRow['source_attribute_id'] = $aRow['id'];
			$aRow['sources_id']+=0;
			$aRow['source_id'] = $aRow['sources_id'];
			$aRow['name'] = $aRow['attribute'];
			$aRow['regex']+=0;
			$aRow['is_regex'] = (empty($aRow['regex'])) ? false : true;
			$aRow['regexpriority']+=0;
			$aRow['regex_priority'] = $aRow['regexpriority'];
			$aRow['regex_value'] = $aRow['value'];
			//NOTE: updated_ts is updated whenever master rec is updated, too
			//      (python script does a delete/re-insert on child records (attributes)
			$aRow['updated_ts'] = $aRow['modtime'];
		}
	}
	
	/**
	 * Return Source data and source attributes.
	 * @param int $aSourceId - (optional) id of source to load; NULL loads all; Default NULL.
	 * @param boolean $bIncludeAttributes - (optional) TRUE will load values from attribute table, too.
	 * @throws DbException
	 * @return array Returns associative array of a single row if $aSourceId is not NULL,
	 * else an array of rows keyed by id and sorted by name.
	 */
	public function getSourceInfo($aSourceId=null, $bIncludeAttributes=false) {
		$theResultSet = array();
		if (!empty($this->db)) try {
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
		
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$this->tnSources;
			if (!empty($aSourceId)) {
				$theSql .= ' WHERE id=:source_id';
				$theParams['source_id'] = $aSourceId+0;
				$theParamTypes['source_id'] = PDO::PARAM_INT;
			}
			$theSql .= ' ORDER by name';
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$theResultSet = $rs->fetchAll();
			if (!empty($theResultSet)) {
				foreach($theResultSet as &$theRow) {
					$this->normalizeSourceRow($theRow);
					if ($bIncludeAttributes)
						$theRow['attributes'] = $this->getSourceAttrs($theRow['source_id']);
				}
			}
			$rs->closeCursor();
		
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getSourceInfo('.$theSourceId.','.($bIncludeAttributes?'true':'false').') failed.');
		}
		if (!empty($aSourceId) && !empty($theResultSet)) {
			return array_shift($theResultSet);
		} else {
			return $theResultSet;
		}
	}

	/**
	 * Given a source id, return the attributes for that source.
	 * @param int $aSourceId - the source id (required)
	 * @throws DbException
	 * @return array Returns the associated array of attribute rows.
	 */
	public function getSourceAttrs($aSourceId, $aFilterList=null) {
		$theResultSet = array();
		if (!empty($this->db)) try {
			$theSourceId = $aSourceId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
		
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$this->tnSourceAttrs;
			$theSql .= ' WHERE sources_id=:source_id';
			$theParams['source_id'] = $theSourceId;
			$theParamTypes['source_id'] = PDO::PARAM_INT;
			if (!empty($aFilterList)) {
				foreach($aFilterList as $theSqlWhereFragment) {
					$theSql .= ' AND '.$theSqlWhereFragment;
				}
			}
			$theSql .= ' ORDER BY attribute';
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			while (($theRow = $rs->fetch()) !== false) {
				$this->normalizeSourceAttrRow($theRow);
				$theResultSet[$theRow['source_attribute_id']] = $theRow;
			}
			$rs->closeCursor();
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getSourceAttrs('.$theSourceId.') failed.');
		}
		return $theResultSet;
	}
	
	/**
	 * Get a list of sources supported by Memex ordered by name.
	 * @param boolean $bKeyById - (optional) array results are keyed by id if TRUE. Default is FALSE.
	 * @return array[id]=name Returns array of names keyed by id (int).
	 */
	public function getSourceList($bKeyById=false) {
		$theSql = 'SELECT id, name FROM '.$this->tnSources;
		$theSql .= ' ORDER BY name';
		$rs = $this->query($theSql);
		if ($bKeyById)
			return DbUtils::cnvRowsToArray($rs, 'id', 'name');
		else
			return $rs->fetchAll();
	}

	/**
	 * Figure out what a source's display name should be based on
	 * Source row data and possibly any included Attribute rows data.
	 * @param array $aSourceInfo - array of source info data
	 * @return string Returns the display name to use.
	 */
	public function getSourceDisplayName($aSourceInfo) {
		if ($aSourceInfo!=null) {
			$theName = $aSourceInfo['display_name'];
			$theAttrRows = $this->getSourceAttrs($aSourceInfo['source_id'],array(
					'attribute=\'display_name\'',
					'regex<>1',
			));
			if (!empty($theAttrRows)) {
				foreach($theAttrRows as &$theAttrRow) {
					if (!empty($theAttrRow['value'])) {
						$theName = $theAttrRow['value'];
						break;
					}
				}
			}
			return $theName;
		}
	}
	
	/**
	 * Return "Last X Days" ad stats.
	 * @param number $aDaysAgo - include ads from this many days ago
	 * @param number $aSiteId - only return stats for this site, or all if NULL (default)
	 * @param boolean $bUseImportTimes - ingest stats use importtime, report stats use posttime
	 * @return number Returns the number of distinct ads during the time period.
	 */
	public function getTotalAdsLastXDays($aDaysAgo, $aSiteId=null, $bUseImportTimes=false) {
		//TODO total unique last x days
		$theTsColumn = ($bUseImportTimes ? 'importtime' : 'posttime');
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT COUNT(id) AS total FROM '.$this->tnAds;
		$theSql .= ' WHERE '.$theTsColumn.'>=:ts';
		if (!empty($aSiteId)) {
			$theSql .= ' AND sources_id=:site_id';
			$theParams['site_id'] = $aSiteId;
			$theParamTypes['site_id'] = PDO::PARAM_INT;
		}
		$theSql .= ' LIMIT 1';
		$theParams['ts'] = DbUtils::cnvDaysToDateTimeStr($aDaysAgo);
		$theParamTypes['ts'] = PDO::PARAM_STR;
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		return (!empty($theRow)) ? $theRow['total']+0 : 0;
	}
	
	public function getTotalDistinctAds($aSiteId=null) {
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT COUNT(id) AS total FROM '.$this->tnAds.' WHERE first_id IS NULL';
		if (!empty($aSiteId)) {
			$theSql .= ' AND sources_id=:site_id';
			$theParams['site_id'] = $aSiteId;
			$theParamTypes['site_id'] = PDO::PARAM_INT;
		}
		$theSql .= ' LIMIT 1';
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		return (!empty($theRow)) ? $theRow['total']+0 : 0;
	}

	public function getTotalAds($aSiteId=null) {
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT COUNT(id) AS total FROM '.$this->tnAds;
		if (!empty($aSiteId)) {
			$theSql .= ' WHERE sources_id=:site_id';
			$theParams['site_id'] = $aSiteId;
			$theParamTypes['site_id'] = PDO::PARAM_INT;
		}
		$theSql .= ' LIMIT 1';
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		return (!empty($theRow)) ? $theRow['total']+0 : 0;
	}

	public function getTotalImages($aSiteId=null) {
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT COUNT(i.id) AS total FROM '.$this->tnImages.' as i';
		if (!empty($aSiteId)) {
			$theSql .= ' JOIN '.$this->tnAds.' as a ON i.ads_id=a.id WHERE a.sources_id=:site_id';
			$theParams['site_id'] = $aSiteId;
			$theParamTypes['site_id'] = PDO::PARAM_INT;
		}
		$theSql .= ' LIMIT 1';
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		return (!empty($theRow)) ? $theRow['total']+0 : 0;
	}

	public function getDashboardStats($bUseImportTimes, $aSiteId=null) {
		if (empty($this->db))
			return array();
		$theResult = array();

		//time since last import
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT MAX(importtime) AS last_imported';
		if ($bUseImportTimes) {
			$theSql .= ', MAX(importtime) as last_importtime, MIN(importtime) as first_importtime';
			$theSql .= ', DATEDIFF(MAX(importtime), MIN(importtime)) as day_count';
		} else {
			$theSql .= ', MAX(posttime) as last_posttime, MIN(posttime) as first_posttime';
			$theSql .= ', DATEDIFF(MAX(posttime), MIN(posttime)) as day_count';
		}
		$theSql .= ' FROM '.$this->tnAds;
		if (!empty($aSiteId)) {
			$theSql .= ' WHERE sources_id=:site_id';
			$theParams['site_id'] = $aSiteId;
			$theParamTypes['site_id'] = PDO::PARAM_INT;
		}
		//$theSql .= ' LIMIT 1';
		$theRow = $this->getTheRow($theSql,$theParams,$theParamTypes);
		if ($theRow) {
			$theResult['last_imported'] = $theRow['last_imported'];
			if ($bUseImportTimes) {
				$theResult['last_importtime'] = $theRow['last_importtime'];
				$theResult['first_importtime'] = $theRow['first_importtime'];
			} else {
				$theResult['last_posttime'] = $theRow['last_posttime'];
				$theResult['first_posttime'] = $theRow['first_posttime'];
			}
			$theResult['total_day_count'] = $theRow['day_count'];
		} else {
			$theResult['last_imported'] = 'n/a';
			if ($bUseImportTimes) {
				$theResult['last_importtime'] = 'n/a';
				$theResult['first_importtime'] = 'n/a';
			} else {
				$theResult['last_posttime'] = 'n/a';
				$theResult['first_posttime'] = 'n/a';
			}
			$theResult['total_day_count'] = 0;
		}
		
		$theResult['total_ads_01'] = $this->getTotalAdsLastXDays(01,$aSiteId,$bUseImportTimes);
		$theResult['total_ads_07'] = $this->getTotalAdsLastXDays(07,$aSiteId,$bUseImportTimes);
		$theResult['total_ads_30'] = $this->getTotalAdsLastXDays(30,$aSiteId,$bUseImportTimes);
		$theResult['total_ads_90'] = $this->getTotalAdsLastXDays(90,$aSiteId,$bUseImportTimes);
		
		$theResult['total_ads_distinct'] = $this->getTotalDistinctAds($aSiteId);
		$theResult['total_ads'] = $this->getTotalAds($aSiteId);
		$theResult['total_images'] = $this->getTotalImages($aSiteId);
		$theResult['total_entries'] = $theResult['total_ads']+$theResult['total_images'];
		
		return $theResult;
	}
	
	/**
	 * Report the suite of statistics for our particular site (new ads, updates, and photos are default).
	 * @return array Returns an array of statistics as well as a display_name for the stat area.
	 */
	public function qroxDashboardStats($bUseImportTimes=false) {
		$theResults = array();
		$theResults['totals'] = $this->getDashboardStats($bUseImportTimes);
		$theSql = 'SELECT id, name FROM '.$this->tnSources;
		$rs = $this->query($theSql);
		if ($rs) {
			foreach ($rs as $theRow) {
				$theResults[$theRow['name']] = $this->getDashboardStats($bUseImportTimes, $theRow['id']);
			}
		}
		return $theResults;
	}
	
	public function qroxMemexHtStats($aSourceId) {
		if ($aSourceId==0) {
			return $this->getDashboardStats(false);
		} else {
			return $this->getDashboardStats(false, $aSourceId);
		}
	}
	
	public function qroxIngestStats($aSourceId) {
		if ($aSourceId==0) {
			return $this->getDashboardStats(true);
		} else {
			return $this->getDashboardStats(true, $aSourceId);
		}
	}
	
	/*
CREATE TABLE IF NOT EXISTS `ads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Auto incremented row identifier, unique to table.',
  `first_id` int(10) unsigned DEFAULT NULL COMMENT 'ID in this table of the first time this ad was seen.',
  `sources_id` int(10) unsigned NOT NULL COMMENT 'ID of the source the ad came from.',
  `incoming_id` int(11) unsigned NOT NULL COMMENT 'ID of the raw HTML in the incoming table.',
  `url` varchar(2083) CHARACTER SET utf8 NOT NULL COMMENT 'Web address of posting.',
  `title` varchar(1024) CHARACTER SET utf8mb4 NOT NULL COMMENT 'User populated title, usually from the HTML title tag.',
  `text` mediumtext CHARACTER SET utf8mb4 NOT NULL COMMENT 'User populated text of post. Included in, but not the entirety of HTML body.',
  `type` varchar(16) DEFAULT NULL COMMENT 'Type of entity advertised. Allowed values are: person, location, organization.',
  `sid` varchar(64) DEFAULT NULL COMMENT 'Site defined identification number of ad. Not necessarily unique.',
  `region` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Advertising region of post. Defined per website.',
  `city` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT 'City advertised in post.',
  `state` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Advertised state or province.',
  `country` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Advertised country.',
  `phone` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Phone number listed in post.',
  `age` varchar(10) DEFAULT NULL COMMENT 'Age of person indicated in post.',
  `website` varchar(2048) DEFAULT NULL COMMENT 'Website of entity advertised in post.',
  `email` varchar(512) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Email address listed in post.',
  `gender` varchar(20) DEFAULT NULL COMMENT 'Listed gender. Suggested values are: female, male, trans.',
  `service` varchar(16) DEFAULT NULL COMMENT 'Services offered. Example values are: Escorts, Massage, BDSM, GFE',
  `posttime` datetime DEFAULT NULL COMMENT 'Site populated timestamp of when post was created.',
  `importtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when post was imported into database.',
  `modtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the most recent modification.',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`importtime`),
  KEY `First ID` (`first_id`),
  KEY `type` (`type`(3)),
  KEY `URL` (`url`(128)),
  KEY `region` (`region`(5)),
  KEY `city` (`city`(6)),
  KEY `state` (`state`(3)),
  KEY `country` (`country`(4)),
  KEY `phone` (`phone`),
  KEY `age` (`age`),
  KEY `email` (`email`(20)),
  KEY `service` (`service`(1)),
  KEY `sid` (`sid`(4)),
  KEY `incomingandsite` (`sources_id`,`incoming_id`),
  KEY `first_idandid` (`first_id`,`id`),
  KEY `posttimeandsites` (`posttime`,`sources_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=18102446 ;
	 */
	protected function normalizeAdRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
			$aRow['roxy_id'] = $aRow['id'];
			$aRow['memex_id'] = $aRow['id'];
				
			if (!is_null($aRow['first_id'])) $aRow['first_id']+=0;
			if (!is_null($aRow['sources_id'])) $aRow['sources_id']+=0;
			$aRow['source_id'] = $aRow['sources_id'];
			if (!is_null($aRow['incoming_id'])) $aRow['incoming_id']+=0;
				
			$aRow['ad_title'] = $aRow['title'];
			$aRow['entity_type'] = $aRow['type'];
			$aRow['ad_text'] = $aRow['text'];
			$aRow['site_ad_id'] = $aRow['sid'];
			
			//MySQL date time string
			$aRow['post_time'] = $aRow['posttime'];
			
			//MySQL date time string
			$aRow['import_ts'] = $aRow['importtime'];

			//MySQL date time string
			$aRow['updated_ts'] = $aRow['modtime'];
		}
	}
	
	/*TODO nothing, just marking for easy bookmark
CREATE TABLE IF NOT EXISTS `ads_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the attribute entry',
  `ads_id` int(11) unsigned NOT NULL COMMENT 'Parent ID for this attribute.',
  `attribute` varchar(32) NOT NULL COMMENT 'Attribute name (Age, location, etc.)',
  `value` varchar(2500) CHARACTER SET utf8 NOT NULL COMMENT 'Value of the attribute.',
  `extracted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'If no the value was from the structure of the website. If yes we used an algorithm on the text to get the value and it may be less accurate.',
  `extractedraw` varchar(512) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Raw text of the data if extracted.',
  `modtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the most recent modification.',
  PRIMARY KEY (`id`),
  KEY `ads_id` (`ads_id`),
  KEY `attribute` (`attribute`(4)),
  KEY `extracted` (`extracted`),
  KEY `value` (`value`(16))
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=66991079 ;
	 */
	protected function normalizeAdAttrRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
			
			$aRow['ads_id']+=0;
			$aRow['roxy_id'] = $aRow['ads_id'];
			$aRow['memex_id'] = $aRow['ads_id'];
			
			$aRow['name'] = $aRow['attribute']; 								//alias
			$aRow['extracted']+=0;
			$aRow['is_extracted'] = (empty($aRow['extracted'])) ? false : true; //alias
			$aRow['extracted_raw'] = $aRow['extractedraw']; 					//alias
			$aRow['extracted_value'] = $aRow['value'];  						//alias
			//NOTE: updated_ts is updated whenever master rec is updated, too
			//      (python script does a delete/re-insert on child records (attributes)
			$aRow['updated_ts'] = $aRow['modtime'];
		}
	}
	
	/**
	 * Retrieve the poprox data for a given user_id/roxy_id.
	 * @param int $aRoxyId - Roxy ID for the ad in question.
	 * @param int $aAcctId - User ID for the poprox data.
	 * @throws DbException
	 * @return array Returns array of row data or NULL if not found.
	 */
	//TODO create poprox tables for Memex_Ht db
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
	 * Randomly pick a roxy_id from the last 100-200 entries.
	 * The most recent 100 are skipped as they may not have images yet.
	 * @param int $aAcctId - user account of person using the site
	 * @return number Return a random Roxy ID of an ad.
	 */
	public function recentRandomAd($aAcctId) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		// grab a set of results and pick a random one
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT c.id FROM '.$this->tnAds.' as c';
		//TODO put back in once poprox tables exist
		/*
		$theSql .= ' LEFT JOIN '.$this->tnAdPoprox.' AS p ON p.user_id=:user_id AND c.id=p.ad_id';
		$theSql .= ' WHERE p.ad_id IS NULL';
		*/
		$theSql .= ' ORDER BY c.id DESC LIMIT 200';
		//TODO put back in once poprox tables exist
		/*
		$theParams['user_id'] = $aAcctId+0;
		$theParamTypes['user_id'] = PDO::PARAM_INT;
		*/
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
	 * Try to sort the list in some meaningful way and show pretty names.
	 * @param array $aAttrRow - associative array of Attribute data
	 * @param int $aIdx - index number we are on
	 * @return $aAttrRow with 'sortkey' and 'display_name' keys added.
	 */
	protected function processAdExtractedInfo($aAttrRow, $aIdx) {
		$theSortKeyFormat = '99-%02d';
		$theDisplayName = $aAttrRow['name'];
		switch ($aAttrRow['name']) {
			case 'phone':
				$theSortKeyFormat = '01-%02d';
				$theDisplayName = 'Phone';
				break;
			case 'email':
				$theSortKeyFormat = '02-%02d';
				$theDisplayName = 'Email';
				break;
			case 'website':
				$theSortKeyFormat = '03-%02d';
				$theDisplayName = 'Embedded Website';
				break;
			case 'ethnicity':
				$theSortKeyFormat = '04-%02d';
				$theDisplayName = 'Ethnicity';
				break;
			case 'height':
				$theSortKeyFormat = '06-%02d';
				$theDisplayName = 'Height (cm)';
				break;
			case 'weight':
				$theSortKeyFormat = '07-%02d';
				$theDisplayName = 'Weight (kg)';
				break;
			case 'age':
				$theSortKeyFormat = '08-%02d';
				$theDisplayName = 'Age';
				break;
			case 'name':
				$theSortKeyFormat = '09-%02d';
				$theDisplayName = 'Name';
				break;
			case 'streetaddress':
				$theSortKeyFormat = '10-%02d';
				$theDisplayName = 'Address';
				break;
			default:
				if (Strings::beginsWith($aAttrRow['name'],'rate')) {
					$theSortKeyFormat = '05-%02d';
					$theDisplayName = 'Rate / '.Strings::strstr_after($aAttrRow['name'],'rate').' min';
				}
		}
		$aAttrRow['sortkey'] = Strings::format($theSortKeyFormat,$aIdx);
		$aAttrRow['display_name'] = $theDisplayName;
		return $aAttrRow;
	}
	
	/**
	 * Try to sort the list in some meaningful way and show pretty names.
	 * @param array $aAttrRow - associative array of Attribute data
	 * @param int $aIdx - index number we are on
	 * @return $aAttrRow with 'sortkey' and 'display_name' keys added.
	 */
	protected function processAdAttributeInfo($aAttrRow, $aIdx) {
		$theSortKeyFormat = '99-%02d';
		$theDisplayName = $aAttrRow['name'];
		switch ($aAttrRow['name']) {
			case 'updatetime':
				$theSortKeyFormat = '01-%02d';
				$theDisplayName = 'Last Updated on';
				break;
			case 'username':
				$theSortKeyFormat = '02-%02d';
				$theDisplayName = 'Username';
				break;
			case 'alias':
				$theSortKeyFormat = '03-%02d';
				$theDisplayName = 'Alias';
				break;
			case 'organization':
				$theSortKeyFormat = '04-%02d';
				$theDisplayName = 'Organization';
				break;
			case 'ethnicity':
				$theSortKeyFormat = '06-%02d';
				$theDisplayName = 'ethnicity';
				break;
			default:
				if (Strings::beginsWith($aAttrRow['name'],'rate')) {
					$theSortKeyFormat = '05-%02d';
					$theDisplayName = 'Rate / '.Strings::strstr_after($aAttrRow['name'],'rate').' min';
				}
		}
		$aAttrRow['sortkey'] = Strings::format($theSortKeyFormat,$aIdx);
		$aAttrRow['display_name'] = $theDisplayName;
		return $aAttrRow;
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
		
			$theResult['attributes'] = array();
			$theResult['extracted_info'] = array(); //array of info by user
			$theResult['extracted_info']['Roxy'] = array(); //computer extraction is user "Roxy"
			
			//get the ad attributes
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$this->tnAdAttrs.' WHERE ads_id=:roxy_id';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$aIdx = 1;
			$eIdx = 1;
			while (($theRow = $rs->fetch()) !== false) {
				$this->normalizeAdAttrRow($theRow);
				if ($theRow['extracted']) {
					$theInfo = $this->processAdExtractedInfo($theRow,$eIdx);
					$theResult['extracted_info']['Roxy'][$theInfo['sortkey'].$eIdx] = $theInfo;
					$eIdx += 1;
				} else {
					$theInfo = $this->processAdAttributeInfo($theRow,$aIdx);
					$theResult['attributes'][$theInfo['sortkey'].$aIdx] = $theInfo;
					$aIdx += 1;
				}
			}
			$rs->closeCursor();
			
			//sort the resulting hodge-podge lists by key
			ksort($theResult['extracted_info']['Roxy']);
			ksort($theResult['attributes']);
			
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getScrapedAd='.$theRoxyId.' failed.');
		}
		return $theResult;
	}
	
	/**
	 * Get the subset of photos associated witn an ad via the Image Attribute table.
	 * @param int $aMemexId - the ad id given by memex (formerly known as roxy_id).
	 * @throws DbException
	 * @return array Returns an array of photo urls or array() if none found.
	 */
	protected function getAdImagesFromImageAttr($aMemexId) {
		$theResult = array();
		if ($this->isConnected() && !empty($aMemexId)) try {
			$ps = null;
			$myFinally = FinallyCursor::forDbCursor($ps);
			
			$theSql = SqlBuilder::withModel($this)->obtainParamsFrom(array(
					'attribute' => 'ads_id',
					'value' => $aMemexId,
			));
			$theSql->add('SELECT replace(i.location,\'?\',\'%3F\') AS imgsrc');
			$theSql->add('FROM')->add($this->tnImageAttrs)->add('AS ia');
			$theSql->add('JOIN')->add($this->tnImages)->add('AS i ON ia.images_id=i.id');
			$theSql->startWhereClause();
			$theSql->mustAddParam('value');
			$theSql->setParamPrefix(' AND ');
			$theSql->mustAddParam('attribute');
			/**/
			$theSql->endWhereClause();
			//$this->debugLog(__METHOD__.' sql='.$this->debugStr($theSql));
			$ps = $theSql->query();
			$theResult = $ps->fetchAll(PDO::FETCH_COLUMN,0);
			foreach ($theResult as &$theImageUrl) {
				if (empty($theImageUrl))
					unset($theImageUrl);
			}
			$ps->closeCursor();
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, __METHOD__.'('.$aMemexId.') failed. '.$pdoe->getMessage());
		}
		return $theResult;
	}

	/**
	 * Get the photos associated witn an ad.
	 * @param int $aMemexId - the ad id given by memex (formerly known as roxy_id).
	 * @throws DbException
	 * @return array Returns an array of photo urls or array() if none found.
	 */
	public function getAdPhotos($aMemexId) {
		$theResult = array();
		if ($this->isConnected() && !empty($aMemexId)) try {
			$ps = null;
			$myFinally = FinallyCursor::forDbCursor($ps);
			
			$theSql = SqlBuilder::withModel($this)->obtainParamsFrom(array(
					'ads_id' => $aMemexId,
			));
			$theSql->add('SELECT replace(location,\'?\',\'%3F\') AS imgsrc FROM')->add($this->tnImages);
			$theSql->startWhereClause();
			$theSql->mustAddParam('ads_id', 0, PDO::PARAM_INT);
			$theSql->endWhereClause();
			//$this->debugLog(__METHOD__.' sql='.$this->debugStr($theSql));
			$ps = $theSql->query();
			$theResult = $ps->fetchAll(PDO::FETCH_COLUMN,0);
			foreach ($theResult as &$theImageUrl) {
				if (empty($theImageUrl))
					unset($theImageUrl);
			}
			$theResult = array_merge($theResult, $this->getAdImagesFromImageAttr($aMemexId));
			$ps->closeCursor();
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, __METHOD__.'('.$aMemexId.') failed. '.$pdoe->getMessage());
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
				//TODO: human micro-task extracted info would be retrieved here, maybe
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
			$theSql = 'SELECT id, first_id FROM '.$this->tnAds.' WHERE id=:roxy_id';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			if (($theRow = $rs->fetch()) !== false) {
				$theResult = $theRow;
			}
			$rs->closeCursor();
		
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'isExistAd='.$theRoxyId.' failed.');
		}
		return $theResult;
	}
	
	public function getAdRevisionOrig($aRoxyId) {
		$theAd = $this->isExistAd($aRoxyId);
		if (!empty($theAd)) {
			return $theAd['first_id'];
		} else {
			return null;
		}
	}
	
	public function getAdRevisionPrev($aRoxyId) {
		$theAd = $this->isExistAd($aRoxyId);
		if (!empty($theAd)) {
			$theResult = $aRoxyId;
			try {
				$theOrigId = $theAd['first_id'];
				$rs = null;
				$myFinally = FinallyCursor::forDbCursor($rs);
			
				//get the primary ad data
				$theParams = array();
				$theParamTypes = array();
				$theSql = 'SELECT id FROM '.$this->tnAds.' WHERE first_id=:first_id AND id<:roxy_id';
				$theSql .= ' ORDER BY id DESC LIMIT 1';
				$theParams['first_id'] = $theOrigId;
				$theParamTypes['first_id'] = PDO::PARAM_INT;
				$theParams['roxy_id'] = $aRoxyId;
				$theParamTypes['roxy_id'] = PDO::PARAM_INT;
				$rs = $this->query($theSql,$theParams,$theParamTypes);
				if (($theRow = $rs->fetch()) !== false) {
					$theResult = $theRow['id'];
				} else {
					$theResult = $theOrigId;
				}
				$rs->closeCursor();
			
			} catch (PDOException $pdoe) {
				throw new DbException($pdoe, 'getAdRevisionPrev='.$aRoxyId.' failed.');
			}
			return $theResult;
		} else {
			return null;
		}
	}
	
	public function getAdRevisionNext($aRoxyId) {
		$theAd = $this->isExistAd($aRoxyId);
		if (!empty($theAd)) {
			$theResult = $aRoxyId;
			try {
				$theOrigId = $theAd['first_id'];
				if (empty($theOrigId)) {
					$theOrigId = $theAd['id'];
				}
				$rs = null;
				$myFinally = FinallyCursor::forDbCursor($rs);
			
				//get the primary ad data
				$theParams = array();
				$theParamTypes = array();
				$theSql = 'SELECT id FROM '.$this->tnAds.' WHERE first_id=:first_id AND id>:roxy_id';
				$theSql .= ' ORDER BY id ASC LIMIT 1';
				$theParams['first_id'] = $theOrigId;
				$theParamTypes['first_id'] = PDO::PARAM_INT;
				$theParams['roxy_id'] = $aRoxyId;
				$theParamTypes['roxy_id'] = PDO::PARAM_INT;
				$rs = $this->query($theSql,$theParams,$theParamTypes);
				if (($theRow = $rs->fetch()) !== false) {
					$theResult = $theRow['id'];
				}
				$rs->closeCursor();
			
			} catch (PDOException $pdoe) {
				throw new DbException($pdoe, 'getAdRevisionNext='.$aRoxyId.' failed.');
			}
			return $theResult;
		} else {
			return null;
		}
	}
	
	/**
	 * Returns the list of ad revisions given an ad and its first_id value.
	 * @param int $aRoxyId - the id of an ad.
	 * @param int $aFirstRoxyId - the first_id value of an ad.
	 * @throws DbException
	 * @return array Returns array($aRoxyId) if no revisions found, else
	 * an array of the entire list of revisions starting from first_id.
	 */
	public function getAdRevisionList($aRoxyId, $aFirstRoxyId) {
		$theResult = null;
		try {
			$theOrigId = (empty($aFirstRoxyId)) ? $aRoxyId : $aFirstRoxyId;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
				
			//get the primary ad data
			$theParams = array();
			$theParamTypes = array();

			$theSql = 'SELECT id, CONCAT(posttime,importtime) as rank';
			$theSql .= ' FROM '.$this->tnAds.' WHERE id=:orig_id1 OR first_id=:orig_id2';
			$theSql .= ' ORDER BY rank';
			$theParams['orig_id1'] = $theOrigId;
			$theParamTypes['orig_id1'] = PDO::PARAM_INT;
			$theParams['orig_id2'] = $theOrigId;
			$theParamTypes['orig_id2'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$theResult = $rs->fetchAll(PDO::FETCH_COLUMN,0);
			$rs->closeCursor();
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getAdRevisionList('.$aRoxyId.','.$aFirstRoxyId.') failed.');
		}
		return $theResult;
	}
	
	public function getAdRevisionListFromAd($aAd) {
		if (!empty($aAd) && !empty($aAd['id'])) {
			return $this->getAdRevisionList($aAd['id'], $aAd['first_id']);
		}
	}
	
	public function searchAd($aScene, $aSearchText) {
		$theQueryLimit = $aScene->getQueryLimit($this->dbType());
		//blarg
	}
	
	public function searchAdAttrs($aScene, $aSearchText) {
		$theQueryLimit = $aScene->getQueryLimit($this->dbType());
		//blarg
	}
	
	/**
	 * Report on what ads have a particular phone number listed within it.
	 * @param Scene $aScene - (can be null) scene being used in case we need user-defined query limits.
	 * @param string $aPhoneDigits - phone number as just digits [0-9] with optional % at start or end of it.
	 * @param boolean $bReturnJustRoxyIds - (optional, default FALSE) if TRUE, then avoid the JOIN and just
	 * return roxy_id values.
	 * @throws DbException
	 * @return array Returns rows as roxy_id, and if $bReturnJustRoxyIds=FALSE then also source_id, ad_url, phone.
	 */
	public function qroxPhoneAdList($aScene, $aPhoneDigits, $bReturnJustRoxyIds=false) {
		$theQueryLimit = (!empty($aScene)) ? $aScene->getQueryLimit($this->dbType()) : null;
		$theResult = array();
		$thePrefixWildcard = '';
		$theSuffixWildcard = '';
		if (Strings::beginsWith($aPhoneDigits,'%')) {
			$thePrefixWildcard = '%';
		}
		if (Strings::endsWith($aPhoneDigits,'%')) {
			$theSuffixWildcard = '%';
		}
		$thePhone = str_replace('%','',$aPhoneDigits);
		if (!empty($this->db) && !empty($thePhone)) {
			try {
				$rs = null;
				$myFinally = FinallyCursor::forDbCursor($rs);
				
				$theParams = array();
				$theParamTypes = array();
				//construct the query part that stays the same between "get total count" and "get limited result set"
				/* instead of a JOIN-then-WHERE, lets see if WHERE-UNION-then-JOIN is faster
				$theQuery = ' FROM '.$this->tnAdAttrs.' as m JOIN '.$this->tnAds.' as s ON m.ads_id=s.id';
				$theQuery .= ' WHERE m.attribute="phone" AND m.extracted=1';
				$theQuery .= ' AND (m.value LIKE '.$thePrefixWildcard.':phone1'.$theSuffixWildcard;
				$theQuery .= ' OR s.phone LIKE '.$thePrefixWildcard.':phone2'.$theSuffixWildcard.')';
				*/
				$theQueryAttrs = '(SELECT m.ads_id AS ad_id, m.value as phone';
				$theQueryAttrs .= ' FROM '.$this->tnAdAttrs.' as m';
				$theQueryAttrs .= ' WHERE m.attribute="phone" AND m.extracted=1';
				$theQueryAttrs .= ' AND m.value LIKE '.$thePrefixWildcard.':phone1'.$theSuffixWildcard;
				$theQueryAttrs .= ')';
				
				$theQueryAds = '(SELECT a.id AS ad_id, a.phone';
				$theQueryAds .= ' FROM '.$this->tnAds.' as a';
				$theQueryAds .= ' WHERE a.phone LIKE '.$thePrefixWildcard.':phone2'.$theSuffixWildcard;
				$theQueryAds .= ')';
				
				$theQuery = ' FROM ('.$theQueryAds.' UNION '.$theQueryAttrs.') as AuM';
				
				$theParams['phone1'] = $thePhone;
				$theParamTypes['phone1'] = PDO::PARAM_STR;
				$theParams['phone2'] = $thePhone;
				$theParamTypes['phone2'] = PDO::PARAM_STR;
				
				if (!empty($theQueryLimit)) {
					//if we have a query limit, we may be using a pager, get total count for pager display
					$theSql = 'SELECT count(DISTINCT AuM.ad_id) as total_rows';
					$theSql .= $theQuery;
					$rs = $this->getTheRow($theSql,$theParams,$theParamTypes);
					if (!empty($rs)) {
						$aScene->setPagerTotalRowCount($rs['total_rows']+0);
					}
				}
				
				//get actual row data for result
				if ($bReturnJustRoxyIds) {
					$theSql = 'SELECT DISTINCT AuM.ad_id as roxy_id'.$theQuery;
				} else {
					$theSql = 'SELECT DISTINCT AuM.ad_id as roxy_id, s.sources_id as source_id, s.first_id, s.url, AuM.phone';
					$theSql .= $theQuery.' JOIN '.$this->tnAds.' as s ON AuM.ad_id=s.id';
				}
				if (!empty($theQueryLimit)) {
					$theSql .= $theQueryLimit;
				}
				$rs = $this->query($theSql,$theParams,$theParamTypes);
				$theResult = $rs->fetchAll();
			} catch (PDOException $pdoe) {
				throw new DbException($pdoe, 'getPhoneAdList='.$thePhone.' failed.');
			}
		}
		return $theResult;
	}
	
	/**
	 * Count number of ads for each week in the time range (x weeks ago to now).
	 * @param array $aRegionList - backpage regions to limit stats to.
	 * @param int $aWeeksAgoLimit - number of weeks ago to calc stats for.
	 * @return array Return the stat rows per region.
	 */
	public function qroxBackpageNumAdsByWeek($aRegionList, $aWeeksAgoLimit) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$rs = null;
		$myFinally = FinallyCursor::forDbCursor($rs);
		$theParams = (is_array($aRegionList)) ? $aRegionList : explode(',',$aRegionList);
		$theParamTypes = array_fill(0,count($theParams),PDO::PARAM_STR);
		//old schema used strings, new has actual timestamps.
		//$thePostDateSql = "IFNULL(STR_TO_DATE(posttime,'%W, %M %e, %Y %l:%i %p'), STR_TO_DATE(importtime,'%W, %e %M %Y, %l:%i %p'))";
		$thePostDateSql = 'IFNULL(posttime,importtime)';
		
		$theSql = 'SELECT region, COUNT(distinct(sid)) as NumAds, YEARWEEK('.$thePostDateSql.',3) AS WeekBasis FROM '.$this->tnAds;
		$theSql .= ' WHERE sources_id=1';
		//$theSql .= ' AND ((posttime IS NOT NULL) OR (importtime IS NOT NULL))'; //importtime is never NULL
		$theSql .= ' AND region IN ('.str_repeat('?,',count($theParams)-1).'?'.')';
		if (!empty($aWeeksAgoLimit)) {
			$theSql .= ' AND YEARWEEK('.$thePostDateSql.',3) >= YEARWEEK(TIMESTAMPADD(SQL_TSI_WEEK,-'.$aWeeksAgoLimit.',NOW()),3)';
		} else {
			$theSql .= ' AND YEARWEEK('.$thePostDateSql.',3) = YEARWEEK(NOW(),3)';
		}
		$theSql .= ' GROUP BY region, WeekBasis ORDER BY region, WeekBasis DESC';
		
		$theStatement = $this->db->prepare($theSql);
		$i = 1;
		foreach ($theParams as $theParamValue) {
			$theStatement->bindValue($i++,$theParamValue);
		}
		if ($theStatement->execute()) {
			return $theStatement->fetchAll();
		}
	}
	
	
}//end class

}//end namespace

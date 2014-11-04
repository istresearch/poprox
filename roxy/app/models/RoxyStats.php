<?php
namespace ISTResearch_Roxy\models;
use BitsTheater\Model as BaseModel;
use com\blackmoonit\Strings;
use com\blackmoonit\Arrays;
use com\blackmoonit\database\FinallyCursor;
use com\blackmoonit\exceptions\DbException;
use \PDO;
use \PDOStatement;
use \PDOException;
use \DateTime;
use \DateInterval;
use ISTResearch_Roxy\models\MemexHt;
	/* @var $dbMemexHt MemexHt */
{//namespace begin

class RoxyStats extends BaseModel {
	public $dbConnName = 'memex_ist';
	
	public $tnAdPostStats;
	public $tnIngestStats;
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		$this->tnAdPostStats = $this->myDbConnInfo->dbName.'.'.$this->tbl_.'poststats';
		$this->tnIngestStats = $this->myDbConnInfo->dbName.'.'.$this->tbl_.'importstats';
	}

	/*
CREATE TABLE IF NOT EXISTS `poststats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Auto incremented row identifier, unique to table.',
  `sources_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Id of the source the ad came from.',
  `ads1day` int(10) unsigned NOT NULL DEFAULT '0',
  `ads7days` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Total number of ads imported in the last 7 days.',
  `ads30days` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Total number of ads imported in the last 30 days.',
  `ads90days` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Total number of ads imported in the last 90 days.',
  `adsdistinct` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Total number of distinct ads.',
  `adstotal` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Total number of ads.',
  `images` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Total number of images.',
  `queuedepth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of urls not yet scraped in queue.',
  `daycount` int(10) unsigned NOT NULL DEFAULT '0',
  `modtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of the most recent count.',
  PRIMARY KEY (`id`),
  KEY `sources_id` (`sources_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=ascii AUTO_INCREMENT=28 ;
	 */
	
	protected function normalizeStatsRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
			
			$aRow['sources_id']+=0;
			$aRow['source_id'] = $aRow['sources_id'];
			
			$aRow['total_ads_01'] = $aRow['ads1day']+0;
			$aRow['total_ads_07'] = $aRow['ads7days']+0;
			$aRow['total_ads_30'] = $aRow['ads30days']+0;
			$aRow['total_ads_90'] = $aRow['ads90days']+0;
			$aRow['total_ads_distinct'] = $aRow['adsdistinct']+0;
			$aRow['total_ads'] = $aRow['adstotal']+0;
			$aRow['total_images'] = $aRow['images']+0;
			$aRow['total_entries'] = $aRow['total_ads'] + $aRow['total_images'];
			$aRow['queue_depth'] = $aRow['queuedepth']+0;
			$aRow['total_day_count'] = $aRow['daycount']+0;
			
			$aRow['updated_ts'] = $aRow['modtime'];
		}
	}
	
	protected function getXStats($aTableName, $aSourceId=null) {
		$theResultSet = null;
		if (!empty($this->db)) try {
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
		
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT * FROM '.$aTableName;
			if (!empty($aSourcesId)) {
				$theSql .= ' WHERE sources_id=:source_id';
				$theParams['source_id'] = $aSourceId;
				$theParamTypes['source_id'] = PDO::PARAM_INT;
			}
			$theSql .= ' ORDER BY sources_id';
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$theResultSet = $rs->fetchAll();
			foreach($theResultSet as &$theRow) {
				$this->normalizeStatsRow($theRow);
			}
			$rs->closeCursor();
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getXStats('.$aTableName.','.$aSourceId.') failed.');
		}
		return $theResultSet;
	}
	
	protected function getAdPostStats($aSourceId=null) {
		return $this->getXStats($this->tnAdPostStats,$aSourceId);
	}
	
	protected function getIngestStats($aSourceId=null) {
		return $this->getXStats($this->tnIngestStats,$aSourceId);
	}
	
	/**
	 * Accumulate row data into a total row.
	 * @param array $aRow - single row of stats data.
	 * @param array $aTotalsRow - the total row to accumulate.
	 */
	protected function addStatsTotals(&$aRow, &$aTotalsRow) {
		$aTotalsRow['total_ads_01'] += $aRow['total_ads_01'];
		$aTotalsRow['total_ads_07'] += $aRow['total_ads_07'];
		$aTotalsRow['total_ads_30'] += $aRow['total_ads_30'];
		$aTotalsRow['total_ads_90'] += $aRow['total_ads_90'];
		$aTotalsRow['total_ads_distinct'] += $aRow['total_ads_distinct'];
		$aTotalsRow['total_ads'] += $aRow['total_ads'];
		$aTotalsRow['total_images'] += $aRow['total_images'];
		$aTotalsRow['total_entries'] += $aRow['total_entries'];
		$aTotalsRow['queue_depth'] = max($aTotalsRow['queue_depth'], $aRow['queue_depth']);
		$aTotalsRow['total_day_count'] = max($aTotalsRow['total_day_count'], $aRow['total_day_count']);
		if (empty($aTotalsRow['updated_ts']) || $aTotalsRow['updated_ts']<$aRow['updated_ts']) {
			$aTotalsRow['updated_ts'] = $aRow['updated_ts'];
		}
	}

	/**
	 * Prepare the stats data for HTML display (adding in totals).
	 * @return array Returns completed stat data.
	 */
	protected function prepareStatsForDisplay(&$aStatsData) {
		$theStatsSet =& $aStatsData;
		
		$dbMemexHt = $this->getProp('MemexHt');
		$theSourceSet = $dbMemexHt->getSourceInfo();
		//print(Strings::debugStr($theSourceSet));

		$theResultSet = array('totals' => array(
				'display_name' => 'Totals',
				'source_info' => null,
				'stats_info' => array(
						'total_ads_01' => 0,
						'total_ads_07' => 0,
						'total_ads_30' => 0,
						'total_ads_90' => 0,
						'total_ads_distinct' => 0,
						'total_ads' => 0,
						'total_images' => 0,
						'total_entries' => 0,
						'queue_depth' => 0,
						'total_day_count' => 0,
						'updated_ts' => null,
				),
		));

		foreach ($theSourceSet as &$theSourceRow) {
			$theStatsRow =& $theStatsSet[$theSourceRow['source_id']];
			$theResultSet[$theSourceRow['name']] = array(
					'display_name' => $dbMemexHt->getSourceDisplayName($theSourceRow),
					'source_info' => $theSourceRow,
					'stats_info' => $theStatsRow,
			);
			$this->addStatsTotals($theStatsRow, $theResultSet['totals']['stats_info']);
		}
		return $theResultSet;
	}
	
	/**
	 * Read in the stats table and prepare it for HTML display (adding in totals).
	 * @return array Returns completed stat data.
	 */
	public function qroxAdPostStats() {
		$theStatsData = Arrays::array_column_as_key($this->getAdPostStats(), 'source_id');
		return $this->prepareStatsForDisplay($theStatsData);
	}
	
	/**
	 * Read in the ingest stats table and prepare it for HTML display (adding in totals).
	 * @return array Returns completed stat data.
	 */
	public function qroxIngestStats() {
		$theStatsData = Arrays::array_column_as_key($this->getIngestStats(), 'source_id');
		return $this->prepareStatsForDisplay($theStatsData);
	}
	
	
}//end class

}//end namespace

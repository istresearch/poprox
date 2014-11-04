<?php
namespace ISTResearch_Roxy\models;
use BitsTheater\Model as BaseModel;
use com\blackmoonit\Strings;
use com\blackmoonit\database\FinallyCursor;
use com\blackmoonit\exceptions\DbException;
use \PDO;
use \PDOStatement;
use \PDOException;
use \DateTime;
use \DateInterval;
{//namespace begin

class Scrapes extends BaseModel {
	public $dbConnName = 'roxy_scrape';
	public $site_id = 'scrape'; //this should the part of the URL that defines this site in roxy, e.g. [%site]/poprox/backpage/ads
	public $site_display_name = 'Scrape'; //the nice display name to use
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		//... nothing here yet
	}
	
	public function getQueueDepth($aSourceName) {
		if (empty($this->db))
			return null;
		$theResult = null;
		$theSql = 'SELECT COUNT(id) AS queue_depth FROM '.$aSourceName.'_queue WHERE done=0 LIMIT 1';
		$rs = $this->query($theSql);
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
	
	public function calcQueueDepths(&$aSourceList) {
		foreach($aSourceList as &$theSourceRow) {
			$theSourceRow['queue_depth'] = $this->getQueueDepth($theSourceRow['name']);
		}
	}
	
	
}//end class

}//end namespace

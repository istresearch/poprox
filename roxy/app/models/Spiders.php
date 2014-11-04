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

class Spiders extends BaseModel {
	public $tnSpiders;
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		
		$this->tnSpiders = $this->tbl_.'spiders';
		//per Trevor, no need to "start" the monitor from PHP anymore
		//$this->engageSpiderMonitor();
	}
	
	public function setupModel() {
		//roxy exists independent of the website, do not create/modify tables
		switch ($this->dbType()) {
		case 'mysql': default:
		}
	}
	
	public function isEmpty($aTableName=null) {
		if ($aTableName==null)
			$aTableName = $this->tnSpiders;
		return parent::isEmpty($aTableName);
	}
	
	protected function normalizeRow(&$aRow) {
		if (!empty($aRow)) {
			$aRow['id']+=0;
		}
	}
	
	/*
	protected function engageSpiderMonitor() {
		$theCurl = curl_init("https://scraper1.istresearch.com:6800/schedule.json");
		curl_setopt($theCurl, CURLOPT_POSTFIELDS, array('project'=>'roxy', 'spider'=>'roxymonitor'));
		curl_setopt($theCurl, CURLOPT_HEADER, 0);
		curl_setopt($theCurl, CURLOPT_RETURNTRANSFER, true);
		$theResponse = curl_exec($theCurl);
		//do not really care about the response, just making sure it is not output to page
		curl_close($theCurl);
	}
	*/
	
	public function getSpider($aSpiderId) {
		if (empty($this->db))
			return array();
		$theResult = null;
		if ($aSpiderId!=null) try {
			$theRoxyId = $aSpiderId+0;
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
				
			//get the primary data
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT id, name, domain, url, pid, runtime, log_page, created_on, updated_on';
			$theSql .= ", CONCAT(IF(name LIKE '%_indexer','000',LPAD(id,3,'0')),name) AS name_sort"; 
			$theSql .= ", IF(name LIKE '%_indexer', SUBSTRING_INDEX(name, '_', 1), '') AS source_name"; 
			$theSql .= ' FROM '.$this->tnSpiders.' WHERE id=:roxy_id';
			$theSql .= ' ORDER BY name_sort';
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			if (($theResult = $rs->fetch()) !== false) {
				$this->normalizeRow($theResult);
			}
			$rs->closeCursor();
				
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getSpider='.$theRoxyId.' failed.');
		}
		return $theResult;
	}
	
	public function getSpiders() {
		if (empty($this->db))
			return array();
		$theResult = array();
		try {
			$rs = null;
			$myFinally = FinallyCursor::forDbCursor($rs);
	
			//get the primary data
			$theParams = array();
			$theParamTypes = array();
			$theSql = 'SELECT id, name, domain, url, pid, runtime, log_page, created_on, updated_on';
			$theSql .= ", IF(name LIKE '%_indexer', REPLACE(name,'_indexer','0000'), REPLACE(name,'_helper',LPAD(id,4,'0')) ) AS name_sort"; 
			$theSql .= ", IF(name LIKE '%_indexer', SUBSTRING_INDEX(name, '_', 1), '') AS source_name"; 
			$theSql .= ' FROM '.$this->tnSpiders;
			$theSql .= ' ORDER BY name_sort';
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			while (($theRow = $rs->fetch()) !== false) {
				$this->normalizeRow($theRow);
				$theResult[] = $theRow;
			}
			$rs->closeCursor();
	
		} catch (PDOException $pdoe) {
			throw new DbException($pdoe, 'getSpiders failed.');
		}
		return $theResult;
	}
	
	
}//end class

}//end namespace

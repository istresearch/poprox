<?php

namespace ISTResearch_Roxy\models;
use BitsTheater\Model as BaseModel;
use com\blackmoonit\Strings;
use com\blackmoonit\database\DbUtils;
use com\blackmoonit\database\FinallyCursor;
use com\blackmoonit\exceptions\DbException;
use com\blackmoonit\exceptions\IllegalArgumentException;
use \PDO;
use \PDOStatement;
use \PDOException;
use \DateTime;
use \DateInterval;
use ISTResearch_Roxy\costumes\RoxyPayloadPhone;
{//begin namespace

/**
 * Class used in the Roxy website.
 */
class RoxymicrotaskPhone extends BaseModel {
	public $tnPhoneList; const TABLE_PhoneList = 'phone_numbers_curated';
	public $tnPhoneScore; const TABLE_PhoneScore = 'phone_scores';
	
	public function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		$this->tbl_ = $this->myDbConnInfo->dbName.'.'.$this->tbl_;
		
		$this->tnPhoneList = $this->tbl_.self::TABLE_PhoneList;
		$this->tnPhoneScore = $this->tbl_.self::TABLE_PhoneScore;
	}
	
	public function setupModel() {
		switch ($this->dbType()) {
		case self::DB_TYPE_MYSQL: default:
			$theSql = "CREATE TABLE IF NOT EXISTS {$this->tnPhoneList} ".
					"( phone_id INT(11) NOT NULL AUTO_INCREMENT".
					", phone VARCHAR(30) NOT NULL".
					", phone_text TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL".
					", levenshtein_distance INT(11) NOT NULL".
					", PRIMARY KEY (phone_id)".
					") ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin";
			$this->execDML($theSql);
			$theSql = "CREATE TABLE IF NOT EXISTS {$this->tnPhoneScore} ".
					"( contact_id CHAR(64) CHARACTER SET ascii NOT NULL DEFAULT ''".
					", phone_id INT(11) NOT NULL DEFAULT '0'".
					", `result` ENUM('0','1','2','3','4') CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL".
					", suggestion VARCHAR(30) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL".
					", PRIMARY KEY (contact_id,phone_id)".
					", KEY `result` (`result`)".
					") ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin";
			$this->execDML($theSql);
			break;
		}
	}
	
	public function isEmpty($aTableName=null) {
		return parent::isEmpty( empty($aTableName) ? $this->tnPhoneScore : $aTableName );
	}
	
	public function getNextPhoneId($aContactId) {
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT contact_id, MAX(phone_id)+1 as phone_id';
		$theSql .= ' FROM '.$this->tnPhoneScore.' WHERE contact_id=:contact_id';
		$theParams['contact_id'] = $aContactId;
		$theParamTypes['contact_id'] = PDO::PARAM_STR;
		$theContact = $this->getTheRow($theSql,$theParams,$theParamTypes);
		//Strings::debugLog('getNextPhoneId='.Strings::debugStr($theContact));
		if (empty($theContact['contact_id'])) {
			$theContact['phone_id'] = 1;
		} else {
			$thePhoneRow = $this->getTheRow('SELECT MAX(phone_id) as max_phone_id FROM '.$this->tnPhoneList);
			if ($theContact['phone_id']>$thePhoneRow['max_phone_id']) {
				$theContact['phone_id'] =  rand(0,$thePhoneRow['max_phone_id']-2)+1;
			}
		}
		return $theContact['phone_id'];
	}
	
	public function getPhoneToScore(RoxyPayloadPhone $aPayload) {
		$thePhoneId = $this->getNextPhoneId($aPayload->contact_id);
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT phone_id, phone, phone_text';
		$theSql .= ' FROM '.$this->tnPhoneList.' WHERE phone_id=:phone_id';
		$theParams['phone_id'] = $thePhoneId;
		$theParamTypes['phone_id'] = PDO::PARAM_INT;
		//Strings::debugLog('getPhoneToScore='.Strings::debugStr($theContact));
		return $this->getTheRow($theSql,$theParams,$theParamTypes);
	}
	
	public function scorePhone(RoxyPayloadPhone $aPayload) {
		$theParams = array();
		$theParamTypes = array();
		$theSql = 'SELECT * FROM '.$this->tnPhoneScore;
		$theSql .= ' WHERE contact_id=:contact_id AND phone_id=:phone_id';
		$theParams['contact_id'] = $aPayload->contact_id;
		$theParamTypes['contact_id'] = PDO::PARAM_STR;
		$theParams['phone_id'] = $aPayload->phone_id;
		$theParamTypes['phone_id'] = PDO::PARAM_INT;
		$theContact = $this->getTheRow($theSql,$theParams,$theParamTypes);
	
		//result and suggestion are common between insert/update
		if (in_array($aPayload->result,array('1','2','3','0'))) {
			$theParams['result'] = $aPayload->result;
			$theParamTypes['result'] = PDO::PARAM_STR;
			$theParams['suggestion'] = null;
			$theParamTypes['suggestion'] = PDO::PARAM_STR;
		} else {
			$theParams['result'] = '4';
			$theParamTypes['result'] = PDO::PARAM_STR;
			$theParams['suggestion'] = $aPayload->result;
			$theParamTypes['suggestion'] = PDO::PARAM_STR;
		}
	
		if (empty($theContact)) {
			$theSql = 'INSERT INTO '.$this->tnPhoneScore;
			$theSql .= ' SET contact_id=:contact_id, phone_id=:phone_id, result=:result, suggestion=:suggestion';
		} else {
			$theSql = 'UPDATE '.$this->tnPhoneScore.' SET result=:result, suggestion=:suggestion';
			$theSql .= ' WHERE contact_id=:contact_id AND phone_id=:phone_id';
		}
		$this->execDML($theSql,$theParams,$theParamTypes);
	}

	/**
	 * Show the phone crowdsource results.
	 * @param Scene $aScene - scene being used in case we need user-defined query limits.
	 * @return array Returns the array of phone scores.
	 */
	public function getPhoneScores($aScene) {
		$theQueryLimit = $aScene->getQueryLimit($this->dbType());
		if (!empty($theQueryLimit)) {
			//if we have a query limit, we may be using a pager, get total count for pager display
			$theSql = 'SELECT count(phone_id) as total_rows FROM '.$this->tnPhoneList.' WHERE phone_id IS NOT NULL';
			$rs = $this->getTheRow($theSql);
			if (!empty($rs)) {
				$aScene->setPagerTotalRowCount($rs['total_rows']+0);
			}
		}
		$theSql = 'SELECT l.phone_id, l.phone, l.phone_text, l.levenshtein_distance,';
		$theSql .= ' SUM(CASE WHEN s.result="1" THEN 1 ELSE 0 END) as num_yes,';
		$theSql .= ' SUM(CASE WHEN s.result="2" THEN 1 ELSE 0 END) as num_no,';
		$theSql .= ' SUM(CASE WHEN s.result="3" THEN 1 ELSE 0 END) as num_idk,';
		$theSql .= ' SUM(CASE WHEN s.result="4" THEN 1 ELSE 0 END) as num_suggestions,';
		$theSql .= ' SUM(CASE WHEN s.result="0" THEN 1 ELSE 0 END) as num_dumb';
		$theSql .= ' FROM '.$this->tnPhoneList.' as l LEFT JOIN '.$this->tnPhoneScore.' as s ON l.phone_id=s.phone_id';
		$theSql .= ' WHERE s.phone_id IS NOT NULL';
		$theSql .= ' GROUP BY l.phone_id';
		$theSql .= ' ORDER BY l.phone_id';
		if (!empty($theQueryLimit)) {
			$theSql .= $theQueryLimit;
		}
		$rs = $this->query($theSql);
		if (!empty($rs)) {
			return $rs->fetchAll();
		}
	}

}//end class

}//end namespace

/**
 * Class used in the WebSocket website.
 */
namespace ISTResearch_Joka\models;
use ISTResearch_Roxy\models\RoxymicrotaskPhone as BaseModel;
{//begin namespace

class RoxymicrotaskPhone extends BaseModel {
	public $dbConnName = 'memex_ist_webapp';
	
}//end class
}//end namespace


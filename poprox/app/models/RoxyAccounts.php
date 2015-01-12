<?php

namespace ISTResearch_Roxy\models;
use BitsTheater\Model as BaseModel;
use com\blackmoonit\exceptions\IllegalArgumentException;
use com\blackmoonit\exceptions\DbException;
{//begin namespace

/**
 * In order to keep the same code between two sites (Joka/Roxy),
 * make the Accounts class descend from this class in Roxy.
 */
class RoxyAccounts extends BaseModel {
	public $tnAccounts; const TABLE_Accounts = 'accounts';

	public function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		$this->tnAccounts = $this->tbl_.self::TABLE_Accounts;
	}
	
	public function setupModel() {
		switch ($this->dbType()) {
		case 'mysql': default:
			$theSql = "CREATE TABLE IF NOT EXISTS {$this->tnAccounts} ".
				"( account_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY".
				", account_name NCHAR(60) NOT NULL".
				", phone NCHAR(16) NULL".
				", external_id INT".
				", KEY idx_external_id (external_id)".
				", UNIQUE KEY idx_account_name_ci (account_name) ".
				") CHARACTER SET utf8 COLLATE utf8_general_ci";
		}
		$this->execDML($theSql);
	}
	
	public function isEmpty($aTableName=null) {
		if ($aTableName==null)
			$aTableName = $this->tnAccounts;
		return parent::isEmpty($aTableName);
	}
	
	public function getAccount($aAcctId) {
		$theSql = "SELECT * FROM {$this->tnAccounts} WHERE account_id = :acct_id";
		return $this->getTheRow($theSql,array('acct_id'=>$aAcctId));
	}
	
	public function getByName($aName) {
		$theSql = "SELECT * FROM {$this->tnAccounts} WHERE account_name = :acct_name";
		return $this->getTheRow($theSql,array('acct_name'=>$aName));
	}
	
	public function getByExternalId($aExternalId) {
		$theSql = "SELECT * FROM {$this->tnAccounts} WHERE external_id = :external_id";
		return $this->getTheRow($theSql,array('external_id'=>$aExternalId));
	}
	
	public function add($aData) {
		$theResult = null;
		if (!empty($aData)) {
			if (!array_key_exists('account_id',$aData))
				$aData['account_id'] = null;
			if (!array_key_exists('external_id',$aData))
				$aData['external_id'] = null;
			if (!array_key_exists('account_name',$aData))
				throw new IllegalArgumentException('account_name undefined');
			$theSql = "INSERT INTO {$this->tnAccounts} ";
			$theSql .= "(account_id, account_name, external_id, phone)";
			$theSql .= " VALUES ";
			$theSql .= "(:account_id, :account_name, :external_id, :phone) ";
			$this->db->beginTransaction();
			try {
				if ($this->execDML($theSql,$aData)) {
					$theResult = $this->db->lastInsertId();
					$this->db->commit();
				} else {
					$this->db->rollBack();
				}
			} catch (PDOException $pdoe) {
				$this->db->rollBack();
				throw new DbException($pdoe, 'Add Accout failed.');
			}
		}
		return $theResult;
	}
	
	public function del($aAccountId) {
		$theSql = "DELETE FROM {$this->tnAccounts} WHERE account_id=$aAccountId ";
		return $this->execDML($theSql);
	}
	

}//end class

}//end namespace


namespace ISTResearch_Joka\models;
use ISTResearch_Roxy\models\RoxyAccounts as BaseModel;
{//begin namespace

/**
 * "com.istresearch.roxy.microtask" package handler for WebSocket website.
 */
class RoxyAccounts extends BaseModel {
	public $dbConnName = 'memex_ist_webapp';

}//end class

}//end namespace



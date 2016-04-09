<?php

namespace ISTResearch_Roxy\models;
use BitsTheater\models\PropCloset\BitsAccounts as BaseModel;
{//begin namespace

/**
 * In order to keep the same code between two sites (Joka/Roxy),
 * make the Accounts class descend from this class in Roxy.
 */
class RoxyAccounts extends BaseModel {
	
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



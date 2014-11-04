<?php

namespace ISTResearch_Roxy\models;
use BitsTheater\Model as BaseModel;
use BitsTheater\configs\Settings;
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
use BitsTheater\models\JokaQueues;
use BitsTheater\costumes\JokaPackage;
use BitsTheater\costumes\IJokaProcessing;
use ISTResearch_Roxy\costumes\RoxyPayloadPhone;
use ISTResearch_Roxy\models\RoxymicrotaskPhone;
{//begin namespace

/**
 * "com.istresearch.roxymicrotask" JokaPackage handler for Roxy site.
 */
class Roxymicrotask extends BaseModel implements IJokaProcessing {
	//cache the db models we use so that they aren't loaded/unloaded massively
	//  when used as a WebSocket instance.
	/**
	 * @var JokaQueues
	 */
	protected $dbJokaQueues;
	/**
	 * @var RoxymicrotaskPhone
	 */
	protected $dbRoxymicrotaskPhone;
	
	public function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		$this->tbl_ = $this->myDbConnInfo->dbName.'.'.$this->tbl_;

		$this->dbJokaQueues = $this->getProp('JokaQueues');
		$this->dbRoxymicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
	}
	
	/**
	 * Process an incoming Payload meant for me, returning TRUE on success.
	 * @param JokaPackage $aJokaPackage - incoming payload
	 * @return boolean Returns TRUE if payload should be removed from the queue.
	 */
	public function processIncomingPayload(JokaPackage $aJokaPackage) {
		//decode payload
		$theInPayload = RoxyPayloadPhone::fromPackage($aJokaPackage);
		if (!empty($theInPayload)) {
			switch ($theInPayload->action) {
				case RoxyPayloadPhone::ACTION_REQUEST_PHONE:
					if (!empty($this->dbRoxymicrotaskPhone)) {
						/* @var $theOutPayload RoxyPayloadPhone */
						$theOutPayload = RoxyPayloadPhone::fromArray($this->director, $this->dbRoxymicrotaskPhone->getPhoneToScore($theInPayload));
						$theOutPayload->action = RoxyPayloadPhone::ACTION_DELIVER_PHONE;
						$theOutPayload->contact_id = $theInPayload->contact_id;
						
						$theJokaPackage = JokaPackage::replyTo($aJokaPackage);
						$theOutPayload->toPackage($theJokaPackage);
						$this->dbJokaQueues->addOutgoingPayload($theJokaPackage);
					}
					break;
				case RoxyPayloadPhone::ACTION_DELIVER_RESULT:
					if (!empty($this->dbRoxymicrotaskPhone)) {
						$this->dbRoxymicrotaskPhone->scorePhone($theInPayload);
					}
					break;
				default:
			}
		}
		return true;
	}
	
}//end class
	
}//end namespace

/**
 * Class used in the Joka WebSocket site.
 */
namespace ISTResearch_Joka\models;
use ISTResearch_Roxy\models\Roxymicrotask as BaseModel;
{//begin namespace

class Roxymicrotask extends BaseModel {
	public $dbConnName = 'memex_ist_webapp';

}//end class
}//end namespace



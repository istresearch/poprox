<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor;
use BitsTheater\Scene as MyScene;
	/* @var $v MyScene */
use ISTResearch_Roxy\models\RoxymicrotaskPhone;
	/* @var $dbRoxymicrotaskPhone RoxymicrotaskPhone */
use ISTResearch_Roxy\costumes\RoxyPayloadPhone;
use com\blackmoonit\Strings;
{//namespace begin

class Mtask extends Actor {
	const DEFAULT_ACTION = 'phone';
	
	const SESSION_ID_PHONE_TASK = 'chtmtaskpnum';
	
	private function getAnonUserId($aIdName) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if (isset($this->director[$aIdName])) {
			$theResult = $this->director[$aIdName];
		} elseif (isset($_COOKIE[$aIdName])) {
			$theResult = $_COOKIE[$aIdName];
			$this->director[$aIdName] = $v->myUserId;
		} else {
			$theResult = Strings::createUUID();
			setcookie($aIdName,$v->myUserId,time()+60*60*24*30,null,'.istresearch.com');
		}
		$this->director[$aIdName] = $theResult;
		return $theResult;
	}
	
	public function chtmtaskpnum() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		$v->myUserId = $this->getAnonUserId(self::SESSION_ID_PHONE_TASK);
		$this->renderThisView = 'embediframe';
	}
	
	
	public function phone() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if (!$this->isAllowed('roxy','mtask'))
			return $this->getHomePage();
		
		$v->myUserId = $this->getMyAccountID();
		
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('mtask');
		
		//what else needs to be done before we render the query entry page?
		//nothing, so far
	}
	
	public function photo() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if (!$this->isAllowed('roxy','mtask'))
			return $this->getHomePage();
		
		$myUserId = $this->getMyAccountID();
		
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('mtask');
		
		//what else needs to be done before we render the query entry page?
		//nothing, so far
	}
	
	/**
	 * Ajax-only call. Returns Roxy Stats display.
	 */
	public function ajaxPhoneTask($aElementId='') {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if ($this->isAllowed('roxy','mtask')) {
			$v->myUserId = $this->getMyAccountID();
			$v->element_id = $aElementId;
			
			// we want to use a curated dataset initially
			$dbRoxymicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
			$thePayload = new RoxyPayloadPhone($this->director);
			$thePayload->contact_id = $v->myUserId;
			$v->result = $dbRoxymicrotaskPhone->getPhoneToScore($thePayload);
		}
	}
	
	public function ajaxPhoneScore($aUserId, $aPhoneId, $aResult, $aSuggestion=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if ($this->isAllowed('roxy','mtask')) {
			$v->myUserId = $this->getMyAccountID();
			if ($aUserId === $v->myUserId) {
				$dbRoxymicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
				$thePayload = new RoxyPayloadPhone($this->director);
				$thePayload->contact_id = $v->myUserId;
				$thePayload->phone_id = $aPhoneId;
				$theSuggestion = preg_replace('`[^0-9]`', '', $aSuggestion);
				if ($aResult==4 && (strlen($theSuggestion)!=10)) {
					$aResult = 0;
				}
				$thePayload->result = ($aResult!=4) ? $aResult : $theSuggestion;
				$dbRoxymicrotaskPhone->scorePhone($thePayload);
			}
		}
		$this->renderThisView = '_blank'; //will not render a page at all, nor generate a 404 error
	}
	
	public function ajaxChtmtaskpnum() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		$v->myUserId = $this->getAnonUserId(self::SESSION_ID_PHONE_TASK);
		$dbRoxymicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
		$thePayload = new RoxyPayloadPhone($this->director);
		$thePayload->contact_id = $v->myUserId;
		$v->result = $dbRoxymicrotaskPhone->getPhoneToScore($thePayload);
		$this->renderThisView = 'ajaxPhoneTask';
	}
	
	public function ajaxChtmtaskpnumScore($aUserId=null, $aPhoneId=null, $aResult=null, $aSuggestion=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		$v->myUserId = $this->getAnonUserId(self::SESSION_ID_PHONE_TASK);
		$aUserId = (!empty($aUserId)) ? $aUserId : $v->uid;
		$aPhoneId = (!empty($aPhoneId)) ? $aPhoneId : $v->pid;
		$aResult = (!empty($aResult)) ? $aResult : $v->rid;
		$aSuggestion = (!empty($aSuggestion)) ? $aSuggestion : $v->sid;
		if ($aUserId == $v->myUserId) {
			$dbRoxymicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
			$thePayload = new RoxyPayloadPhone($this->director);
			$thePayload->contact_id = $v->myUserId;
			$thePayload->phone_id = $aPhoneId;
			$theSuggestion = preg_replace('`[^0-9]`', '', $aSuggestion);
			if ($aResult==4 && (strlen($theSuggestion)!=10)) {
				$aResult = 0;
			}
			$thePayload->result = ($aResult!=4) ? $aResult : $theSuggestion;
			$dbRoxymicrotaskPhone->scorePhone($thePayload);
		}
		$this->renderThisView = '_blank'; //will not render a page at all, nor generate a 404 error
	}
	
	
}//end class

}//end namespace


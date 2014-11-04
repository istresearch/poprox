<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor;
use BitsTheater\Scene as MyScene;
	/* @var $v MyScene */
use ISTResearch_Roxy\models\AnnotLines;
	/* @var $dbAnnotLines AnnotLines */
use ISTResearch_Roxy\costumes\AnnotPayload;
	/* @var $thePayload AnnotPayload */
use com\blackmoonit\Strings;
{//namespace begin

class Annottask extends Actor {
	const DEFAULT_ACTION = 'chtmtaskpnum';
	
	const SESSION_ID_PHONE_TASK = 'annotlinespnum';
	
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
			$dbAnnotLines = $this->getProp('AnnotLines');
			$thePayload = new AnnotPayload($this->director);
			$thePayload->contact_id = $v->myUserId;
			$v->result = $dbAnnotLines->getPhoneToScore($thePayload);
		}
	}
	
	public function ajaxPhoneScore($aUserId, $aPhoneId, $aResult, $aSuggestion=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if ($this->isAllowed('roxy','mtask')) {
			$v->myUserId = $this->getMyAccountID();
			if ($aUserId === $v->myUserId) {
				$dbAnnotLines = $this->getProp('AnnotLines');
				$thePayload = new AnnotPayload($this->director);
				$thePayload->contact_id = $v->myUserId;
				$thePayload->phone_id = $aPhoneId;
				$theSuggestion = preg_replace('`[^0-9]`', '', $aSuggestion);
				if ($aResult==4 && (strlen($theSuggestion)!=10)) {
					$aResult = 0;
				}
				$thePayload->result = ($aResult!=4) ? $aResult : $theSuggestion;
				$dbAnnotLines->scorePhone($thePayload);
			}
		}
		$this->renderThisView = '_blank'; //will not render a page at all, nor generate a 404 error
	}
	
	public function ajaxChtmtaskpnum() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		$v->myUserId = $this->getAnonUserId(self::SESSION_ID_PHONE_TASK);
		$dbAnnotLines = $this->getProp('AnnotLines');
		$thePayload = new AnnotPayload($this->director);
		$thePayload->contact_id = $v->myUserId;
		$v->result = $dbAnnotLines->getPhoneToScore($thePayload);
		$this->renderThisView = 'ajaxPhoneTask';
	}
	
	public function ajaxChtmtaskpnumScore($aUserId=null, $aPhoneId=null, $aResult=null, $aSuggestion=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		$v->myUserId = $this->getAnonUserId(self::SESSION_ID_PHONE_TASK);
		$aUserId = (!empty($aUserId)) ? $aUserId : $v->uid;
		$aPhoneId = (!empty($aPhoneId)) ? $aPhoneId : $v->pid;
		//$aResult = (!empty($aResult)) ? $aResult : $v->rid;
		$aSuggestion = (!empty($aSuggestion)) ? $aSuggestion : $v->sid;
		if ($aUserId == $v->myUserId) {
			$dbAnnotLines = $this->getProp('AnnotLines');
			$thePayload = new AnnotPayload($this->director);
			$thePayload->contact_id = $v->myUserId;
			$thePayload->annot_id = $aPhoneId;
			//suggestion for Annot can be anything, not just a phone number, remove all phone specific checking
			/*
			$theSuggestion = preg_replace('`[^0-9]`', '', $aSuggestion);
			if ($aResult==4 && (strlen($theSuggestion)!=10)) {
				$aResult = 0;
			}
			$thePayload->result = ($aResult!=4) ? $aResult : $theSuggestion;
			*/
			$thePayload->result = $aSuggestion;
			$dbAnnotLines->scorePhone($thePayload);
		}
		$this->renderThisView = '_blank'; //will not render a page at all, nor generate a 404 error
	}
	
	
}//end class

}//end namespace


<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor;
use ISTResearch_Roxy\scenes\Ads as MyScene;
	/* @var $v MyScene */
use BitsTheater\models\Accounts;
	/* @var $dbAccounts Accounts */
use BitsTheater\models\Auth;
	/* @var $dbAuth Auth */
use ISTResearch_Roxy\models\MemexHt;
	/* @var $dbRoxyAds MemexHt */
use com\blackmoonit\Strings;
use com\blackmoonit\Arrays;
use com\blackmoonit\Widgets;
{//namespace begin

class Ads extends Actor {
	const DEFAULT_ACTION = 'view';
	
	protected function view_ad($aRoxyId) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//page render may be based on individual users, so get account id of whomever is logged in
		$myUserId = $this->getMyAccountID();
		//get the model to use
		$dbRoxyAds = $this->getProp('MemexHt');
		//we are retrieving a particular roxy_id or if empty, then select a random recent one
		$theRoxyId = (!empty($aRoxyId)) ? $aRoxyId : $dbRoxyAds->recentRandomAd($myUserId);
		//get the usual ad data
		if (!empty($theRoxyId) && $dbRoxyAds->isExistAd($theRoxyId)) {
			$v->ad_info = $dbRoxyAds->getCompleteAd($theRoxyId);
			$v->site_info = $dbRoxyAds->getSourceInfo($v->ad_info['source_id']);
			//$v->nextRevId = $dbRoxyAds->getAdRevisionNext($theRoxyId);
			$v->listRevIds = $dbRoxyAds->getAdRevisionList($theRoxyId, $v->ad_info['first_id']);
			$theRevIdx = array_search($theRoxyId, $v->listRevIds);
			$v->current_rev_index = $theRevIdx;
			$v->prevRevId = ($theRevIdx>0) ? $v->listRevIds[$theRevIdx-1] : null;
			$v->nextRevId = ($theRevIdx<(count($v->listRevIds)-1)) ? $v->listRevIds[$theRevIdx+1] : null;
			$v->origRevId = ($v->listRevIds[0]!=$theRoxyId) ? $v->listRevIds[0] : null;
			
			if (!empty($v->prevRevId))
				$v->prev_ad_info = $dbRoxyAds->getCompleteAd($v->prevRevId);
		} else {
			//$v->addUserMsg('Ad with MemexID '.$theRoxyId.' does not exist in this database.');
			return $v->getMyUrl('view');
		}
		//display this particular html page to view
		$this->renderThisView = ($myUserId!=21) ? 'view_ad' : 'view_60min';
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('view');
	}
	
	//============================================================================
	//PUBLIC facing functions, these will all match a URL segment after /ads
	//============================================================================
	
	public function view($aRoxyId=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		//TODO do some real auth here instead of security through obscurity - need to learn how
		//TODO learned how, just need time to work with Oculus on changing this to use it
		if ($v->oculus=='0aca893fbfa448fb64bb165c09abe62410e51d360f9b4c9817199c0af21f4750') {
			$theAccountId = 3; //poprox account ID
			$dbAuth = $this->getProp('Auth');
			$dbAccounts = $this->getProp('Accounts');
			$this->director[$dbAuth::KEY_userinfo] = $theAccountId;
			$this->director->account_info = $dbAccounts->getAccount($theAccountId);
			if (isset($this->director->account_info)) {
				$this->director->account_info['groups'] = $dbAuth->belongsToGroups($theAccountId);
				$dbAuth->updateCookie($theAccountId);
			}
		}
		
		$v->checkForBasicHttpAuth();
		//auth
		if (!$this->isAllowed('roxy','view_data'))
			return $this->getHomePage();
		
		//get the model to use
		$dbRoxyAds = $this->getProp('MemexHt');
		
		//button value from page or [site]/ads/#? (# is our $aRoxyId param)
		$theRoxyId = (!empty($v->jumpto_roxy_id) ? $v->jumpto_roxy_id : $aRoxyId);

		//page may render system message if ID is not found
		if (!empty($theRoxyId) && !$dbRoxyAds->isExistAd($theRoxyId)) {
			$v->addUserMsg('Memex ID '.$theRoxyId.' not found.');
		}
		
		return $this->view_ad($aRoxyId);
	}
	
	public function nav($aRoxyId=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		$dbRoxyAds = $this->getProp('MemexHt');
		
		//PREV
		if (!empty($v->poprox_prev)) {
			return $this->getMyUrl('view/'.($aRoxyId-1));
		//NEXT
		} else if (!empty($v->poprox_next)) {
			return $this->getMyUrl('view/'.($aRoxyId+1));
		//PREV Revision
		} else if (!empty($v->poprox_prev_rev)) {
			$theId = $dbRoxyAds->getAdRevisionPrev($aRoxyId);
			if (empty($theId))
				$theId = $aRoxyId;
			return $this->getMyUrl('view/'.($theId));
		//FIRST Revision
		} else if (!empty($v->poprox_orig_rev)) {
			$theId = $dbRoxyAds->getAdRevisionOrig($aRoxyId);
			if (empty($theId))
				$theId = $aRoxyId;
			return $this->getMyUrl('view/'.($theId));
		//NEXT Revision
		} else if (!empty($v->poprox_next_rev)) {
			$theId = $dbRoxyAds->getAdRevisionNext($aRoxyId);
			if (empty($theId))
				$theId = $aRoxyId;
			return $this->getMyUrl('view/'.($theId));
		//IF JumpTo button pressed or (TextBoxRoxyID not empty and ENTER pressed)
		} else if (!empty($v->poprox_jumpto_id) || !empty($v->jumpto_roxy_id)) {
			$theId = is_numeric($v->jumpto_roxy_id) ? $v->jumpto_roxy_id : 0;
			return $this->getMyUrl('view/'.$theId);
		//PARAMETER not empty
		} else if (!empty($aRoxyId)) {
			$theId = $aRoxyId;
			return $this->getMyUrl('view/'.$theId);
		} else {
			return $this->getMyUrl('view');
		}
	}
	
	protected function getCompleteAdInfo(MemexHt $dbRoxyAds, $aRoxyId) {
		$theRoxyId = $aRoxyId;
		//get the usual ad data
		$theResult = array();
		$theResult['ad_info'] = $dbRoxyAds->getCompleteAd($theRoxyId);
		
		$theSourceId = $theResult['ad_info']['source_id'];
		$theSourceRow = $dbRoxyAds->getSourceInfo($theSourceId);
		$theResult['site_info']['id'] = $theSourceRow['id'];
		$theResult['site_info']['name'] = $theSourceRow['name'];
		$theResult['site_info']['display_name'] = $theSourceRow['display_name'];

		/*
		 $theResult['prevRevId'] = $dbRoxyAds->getAdRevisionPrev($theRoxyId);
		if ($theResult['prevRevId']==$theRoxyId)
			$theResult['prevRevId'] = null;
		$theResult['nextRevId'] = $dbRoxyAds->getAdRevisionNext($theRoxyId);
		if ($theResult['nextRevId']==$theRoxyId)
			$theResult['nextRevId'] = null;
		$theResult['origRevId'] = $dbRoxyAds->getAdRevisionOrig($theRoxyId);
		if ($theResult['origRevId']==$theRoxyId)
			$theResult['origRevId'] = null;
		*/
		$theResult['listRevIds'] = $dbRoxyAds->getAdRevisionList($theRoxyId, $theResult['ad_info']['first_id']);
		$theRevIdx = array_search($theRoxyId, $theResult['listRevIds']);
		$theResult['current_rev_index'] = $theRevIdx;
		$theResult['prevRevId'] = ($theRevIdx>0) ? $theResult['listRevIds'][$theRevIdx-1] : null;
		$theResult['nextRevId'] = ($theRevIdx<(count($theResult['listRevIds'])-1)) ? $theResult['listRevIds'][$theRevIdx+1] : null;
		$theResult['origRevId'] = ($theResult['listRevIds'][0]!=$theRoxyId) ? $theResult['listRevIds'][0] : null;
		
		return $theResult;
	}
	
	public function asJson($aRoxyId) {
		die('JSON feature has been disabled.');
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		$v->checkForBasicHttpAuth();
		//auth
		if (!$this->isAllowed('roxy','view_data'))
			die();
		
		//page render may be based on individual users, so get account id of whomever is logged in
		$myUserId = $this->getMyAccountID();
		//get the model to use
		$dbRoxyAds = $this->getProp('MemexHt');
		
		$bJustTheOne = false;
		$theRoxyIdList = array();
		if (is_numeric($aRoxyId)) {
			$bJustTheOne = true;
			$theRoxyIdList = array($aRoxyId);
		} else if (is_string($aRoxyId) && !empty($v->$aRoxyId) && is_array($v->$aRoxyId)) {
			$theRoxyIdList = $v->$aRoxyId;
		}
	
		$v->result = array();
		foreach ($theRoxyIdList as $theRoxyId) {
			$theResult = $this->getCompleteAdInfo($dbRoxyAds, $theRoxyId);
			if ($bJustTheOne) {
				$v->result = $theResult;
				break;
			} else
				$v->result[] = $theResult;
		}
		//display this particular html page to view
		$this->renderThisView = 'json_response';
	}
	
	public function listByPhoneAsJson($aPhoneDigits) {
		die('JSON feature has been disabled.');
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		$v->checkForBasicHttpAuth();
		//auth
		if (!$this->isAllowed('roxy','view_data'))
			die();
		
		//page render may be based on individual users, so get account id of whomever is logged in
		$myUserId = $this->getMyAccountID();
		//get the model to use
		$dbRoxyAds = $this->getProp('MemexHt');
		
		$theRoxyIdList = array();
		
		$thePhoneDigits = preg_replace('/[^0-9]+/', '', $aPhoneDigits);
		$theReportResults = $dbRoxyAds->qroxPhoneAdList(null,$thePhoneDigits,true);
		if (!empty($theReportResults)) {
			$theRoxyIdList = Arrays::array_column($theReportResults,'roxy_id');
		}
		
		$v->result = array();
		foreach ($theRoxyIdList as $theRoxyId) {
			$theResult = $this->getCompleteAdInfo($dbRoxyAds, $theRoxyId);
			$v->result[] = $theResult;
		}
		//display this particular html page to view
		$this->renderThisView = 'json_response';
	}
	
	public function ajaxToggleBlurPhotos() {
		$this->viewToRender('_blank');
		if (!$this->isGuest()) {
			$this->director['blur_ad_photos'] = !($this->director['blur_ad_photos']);
		}
	}
	
}//end class

}//end namespace

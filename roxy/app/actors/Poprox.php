<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor;
use ISTResearch_Roxy\scenes\Poprox as MyScene;
	/* @var $v MyScene */
use ISTResearch_Roxy\models\SiteBackpage;
	/* @var $dbSiteBackpage SiteBackpage */
use com\blackmoonit\Strings;
use com\blackmoonit\Widgets;
{//namespace begin

class Poprox extends Actor {
	const DEFAULT_ACTION = 'backpage';
	
	protected function jumpToRoxyId($aSiteModel, $aRoxyId=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//get the model to use
		$dbRoxyAds = $this->getProp($aSiteModel);
		
		$theRoxyId = (!empty($v->jumpto_roxy_id) ? $v->jumpto_roxy_id : $aRoxyId);
		if (!empty($theRoxyId) && !$dbRoxyAds->isExistAd($theRoxyId)) {
			$v->addUserMsg('Roxy ID '.$theRoxyId.' not found.');
		}
		return $this->getMyURL($dbRoxyAds->getSiteId().'/ads/'.$theRoxyId);
	}
	
	protected function jumpToRoxyAd($aSiteModel, $aAdId=null, $aRegion=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//get the model to use
		$dbRoxyAds = $this->getProp($aSiteModel);

		$theRegion = (!empty($v->jumpto_ad_region)) ? $v->jumpto_ad_region : $aRegion;
		$theAdId = (!empty($v->jumpto_ad_id)) ? $v->jumpto_ad_id : $aAdId;
		switch ($aSiteModel) {
			case 'SiteBackpage':
				$dbSiteBackpage = $dbRoxyAds;
				if (!empty($theRegion)) {
					$theRoxyId = $dbSiteBackpage->getRoxyId($theAdId, $theRegion);
					if (empty($theRoxyId)) {
						$v->addUserMsg($v->getRes('site_backpage/msg_adid_not_found/'.$theAdId.'/'.$theRegion));
					}
				} else {
					$v->addUserMsg($v->getRes('site_backpage/msg_region_unknown'),$v::USER_MSG_WARNING);
				}
				break;
			default:
				$theRoxyId = $dbRoxyAds->getRoxyId($theAdId);
				if (empty($theRoxyId)) {
					$v->addUserMsg('The ad ID '.$theAdId.' not found.');
				}
				break;
		} //end switch
		return $this->getMyURL($dbRoxyAds->getSiteId().'/ads/'.$theRoxyId);
	}
	
	protected function poprox_ad($aSiteModel, $aRoxyId=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//poprox is based on individual users, so get account id of whomever is logged in
		$myUserId = $this->getMyAccountID();
		//get the model to use
		$dbRoxyAds = $this->getProp($aSiteModel);
		//we are retrieving a particular roxy_id or if empty, then select a random recent one
		$theRoxyId = (!empty($aRoxyId)) ? $aRoxyId : $dbRoxyAds->poproxRandomAd($myUserId);
		//get the usual ad data
		$v->result = $dbRoxyAds->getCompleteAd($theRoxyId);
		//get the poprox score data associated with the ad
		if (!empty($v->result)) {
			$v->result['poprox'] = $dbRoxyAds->getPoproxDataForAd($theRoxyId, $myUserId);
		}
		$v->site_id = $dbRoxyAds->getSiteId();
		$v->site_display_name = $dbRoxyAds->getSiteDisplayName();
		//display this particular html page to view
		$this->renderThisView = $dbRoxyAds->getSiteId().'_ad';
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('poprox');
	}
	
	/**
	 * Handle ad scoring.
	 * ALSO: handle JUMPTO form because HTML cannot handle nested form tags. Outer form is always submitted!
	 */
	protected function poprox_score($aSiteModel) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//poprox is based on individual users, so get account id of whomever is logged in
		$myUserId = $this->getMyAccountID();
		//get the model to use
		$dbRoxyAds = $this->getProp($aSiteModel);
		
		if ($v->poprox_likely || $v->poprox_unlikely) {
			$dbRoxyAds->poproxScore($myUserId,$v);
		} else if ($v->poprox_next) {
			return $this->jumpToRoxyId($aSiteModel,$v->ad_id+1);
		} else if ($v->poprox_jumpto_id) {
			return $this->jumpToRoxyId($aSiteModel,$v->jumpto_roxy_id);
		} else if ($v->poprox_jumpto_ad) {
			return $this->jumpToRoxyAd($aSiteModel,$v->jumpto_ad_id);
		}
		return $v->redirect;
	}
	
	protected function handlePoproxRouting($aSiteModel, $aSubArea, $aRoxyId=null) {
		//auth
		if (!$this->isAllowed('roxy','poprox'))
			return $this->getHomePage();
		//are we displaying an ad to score or saving such scores to the roxydb
		switch ($aSubArea) {
			case 'ads':
				return $this->poprox_ad($aSiteModel, $aRoxyId);
			case 'score':
				return $this->poprox_score($aSiteModel);
		}
	}
	
	//============================================================================
	//PUBLIC facing functions, these will all match a URL segment after /poprox
	//============================================================================
	
	public function jumptoad($aSiteId='backpage', $aRoxyId=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		if ($v->oculus=='0aca893fbfa448fb64bb165c09abe62410e51d360f9b4c9817199c0af21f4750') {
			//FOR SPRINT ONLY
			$theAccountId = 3; //poprox account ID
			$dbAuth = $this->getProp('Auth');
			$dbAccts = $this->getProp('Accounts');
			$this->director[$dbAuth::KEY_userinfo] = $theAccountId;
			$this->director->account_info = $dbAccts->getAccount($theAccountId);
			if (isset($this->director->account_info)) {
				$this->director->account_info['groups'] = $dbAuth->belongsToGroups($theAccountId);
				$dbAuth->updateCookie($theAccountId);
			}
			//return $this->getMyURL($aSiteId.'/ads/'.$aRoxyId);
			return $this->getSiteURL('ads','view',$aRoxyId);
		}
		
		return $this->getMyURL($aSiteId.'/ads');
	}
	
	public function rb($aSubArea='ads', $aRoxyId=null) {
		return $this->handlePoproxRouting('SiteRedbook', $aSubArea, $aRoxyId);
	}
	
	public function backpage($aSubArea='ads', $aRoxyId=null) {
		return $this->handlePoproxRouting('SiteBackpage', $aSubArea, $aRoxyId);
	}
	
	public function craigslist($aSubArea='ads', $aRoxyId=null) {
		return $this->handlePoproxRouting('SiteCraigslist', $aSubArea, $aRoxyId);
	}
	
	public function myproviderguide($aSubArea='ads', $aRoxyId=null) {
		return $this->handlePoproxRouting('SiteMyProvider', $aSubArea, $aRoxyId);
	}
	
	public function naughtyreviews($aSubArea='ads', $aRoxyId=null) {
		return $this->handlePoproxRouting('SiteNaughtyReviews', $aSubArea, $aRoxyId);
	}
	
	public function rubmaps($aSubArea='ads', $aRoxyId=null) {
		//TODO Rubmaps needs to be created
		//return $this->handlePoproxRouting('SiteRubmaps', $aSubArea, $aRoxyId);
	}

	public function classivox($aSubArea='ads', $aRoxyId=null) {
		//TODO once model exists, put this back in
		//return $this->handlePoproxRouting('SiteClassivox', $aSubArea, $aRoxyId);
	}
	
	
}//end class

}//end namespace


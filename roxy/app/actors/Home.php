<?php
namespace BitsTheater\actors;
use BitsTheater\Actor;
use BitsTheater\Scene as MyScene;
	/* @var $v MyScene */
use ISTResearch_Roxy\models\RoxyCore;
	/* @var $dbSiteRoxy¿¿¿ RoxyCore */
use ISTResearch_Roxy\models\Spiders;
	/* @var $dbSpiders Spiders */
use ISTResearch_Roxy\models\MemexHt;
	/* @var $dbMemexHt MemexHt */
use ISTResearch_Roxy\models\RoxyStats;
	/* @var $dbRoxyStats RoxyStats */
{//namespace begin

class Home extends Actor {
	const DEFAULT_ACTION = 'view';

	public function view() {
		if ($this->isAllowed('roxy', 'dashboard')) {
			return $this->getMyUrl('dashboard');
		} elseif ($this->isAllowed('roxy', 'mtask')) {
			return $this->getSiteURL('mtask/phone');
		} elseif ($this->isAllowed('roxy', 'view_data')) {
			return $this->getSiteURL('ads/view');
		}
	}

	public function dashboard() {
		if ($this->isAllowed('roxy', 'dashboard')) {
			//shortcut variable $v also in scope in our view php file.
			$v =& $this->scene;
			
			$myUserId = $this->getMyAccountID();
	
			$theResult = array();
			$v->dashboard_data = $theResult;
			$v->spiders = array();
		} else {
			return $this->getHomePage();
		}
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('home');
	}
	
	/**
	 * Ajax-only call. Returns Roxy Stats display.
	 */
	public function ajaxDashDisplayRoxyStats() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if ($this->isAllowed('roxy', 'dashboard')) {
			$v->results = $this->director->foreachModel('Site*','qroxDashboardStats');
			foreach((array) $v->results as $theSiteModel => $theSiteResults) {
				if (empty($theSiteResults)) {
					unset($v->results[$theSiteModel]);
				}
			}

			//no models for these sites, yet
			$dbSiteRoxy¿¿¿ = $this->getProp('RoxyCore');
			$tnBase = 'scraped_rubmaps';
			$r = array();
			$r['ads'] = $dbSiteRoxy¿¿¿->getDashboardStats($tnBase.'_ads', $tnBase.'_ads_history');
			$r['ads']['display_name'] = 'Ads';
			$v->results['Rubmaps'] = $r;
		}
	}
	
	/**
	 * Ajax-only call. Returns Spider Status display.
	 */
	public function ajaxDashDisplayRoxySpiders() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		
		if ($this->isAllowed('roxy', 'monitoring')) {
			$dbSpiders = $this->getProp('Spiders');
			$v->results = $dbSpiders->getSpiders();
		}
	}
	
	/**
	 * Ajax-only call. Returns MemexHt Stats display.
	 */
	public function ajaxDashDisplayMemexHtStats() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;

		if ($this->isAllowed('roxy', 'dashboard')) {
			$dbMemexHt = $this->getProp('MemexHt');
			$v->results = $dbMemexHt->qroxDashboardStats();
		}
	}
	
	/**
	 * Ajax-only call. Returns cached stats display.
	 */
	public function ajaxDashDisplayCachedStats() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
	
		if ($this->isAllowed('roxy', 'dashboard')) {
			//use the cached stats
			$dbRoxyStats = $this->getProp('RoxyStats');
			$v->results = $dbRoxyStats->qroxAdPostStats();
		}
	}

	/*
	public function debugCachedStats() {
		$this->renderThisView = 'ajaxDashDisplayCachedStats';
		return $this->ajaxDashDisplayCachedStats();
	}
	*/
	
	
}//end class

}//end namespace

<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor;
use BitsTheater\Scene as MyScene;
	/* @var $v MyScene */
use com\blackmoonit\Strings;
use ISTResearch_Roxy\models\MemexHt;
	/* @var $dbMemexHt MemexHt */
use ISTResearch_Roxy\models\SiteBackpage;
	/* @var $dbSiteBackpage SiteBackpage */
use ISTResearch_Roxy\models\RoxyMicrotaskPhone;
	/* @var $dbRoxyMicrotaskPhone RoxyMicrotaskPhone */
{//namespace begin

class Qrox extends Actor {
	const DEFAULT_ACTION = 'search';
	
	public function search() {
		if (!$this->isAllowed('roxy','run_reports'))
			return $this->getHomePage();
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('qrox');

		$v->results = array();
		if ( (!empty($v->btn_qrox_search_body) || !empty($v->btn_qrox_search_attr)) && !empty($v->qrox_search)) {
			$dbMemexHt = $this->getProp('MemexHt');
			if (!empty($v->btn_qrox_search_body))
				$v->results = $dbMemexHt->searchAd($v->qrox_search);
			else
				$v->results = $dbMemexHt->searchAdAttrs($v->qrox_search);
			if (empty($v->results)) {
				$v->results = null;
				$v->addUserMsg($v->getRes('generic/msg_nothing_found'));
			}
		}
	}
	
	public function phoneList() {
		if (!$this->isAllowed('roxy','run_reports'))
			return $this->getHomePage();
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('qrox');

		$v->results = array();
		if (!empty($v->btn_qrox_phone) && !empty($v->qrox_phone_ad_links)) {
			$dbMemexHt = $this->getProp('MemexHt');
			$v->source_list = $dbMemexHt->getSourceList(true);
			$thePhoneDigits = preg_replace('/[^0-9]+/', '', $v->qrox_phone_ad_links);
			$v->results = $dbMemexHt->qroxPhoneAdList(/*$v*/null,$thePhoneDigits);
			if (empty($v->results)) {
				$v->results = null;
				$v->addUserMsg($v->getRes('generic/msg_nothing_found'));
			}
		}
	}
	
	public function bpRegion() {
		if (!$this->isAllowed('roxy','run_reports'))
			return $this->getHomePage();
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('qrox');

		if (!empty($v->btn_qrox_bp_byweek_newyork) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('albany', 'binghampton', 'bronx', 'brooklyn', 'buffalo', 'catskills', 'chautauqua', 'elmira', 'fairfield', 'fingerlakes', 'glensfalls', 'hudsonvalley', 'ithaca', 'longisland', 'manhattan', 'newyork', 'oneonta', 'plattsburgh', 'potsdam', 'queens', 'rochester', 'statenisland', 'syracuse', 'twintiers', 'utica', 'watertown', 'westchester');
		} else if (!empty($v->btn_qrox_bp_byweek_newjersey) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('centraljersey', 'jerseyshore', 'newjersey', 'northjersey', 'southjersey');
		} else if (!empty($v->btn_qrox_bp_byweek_washdc) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('nova', 'southernmaryland', 'dc');
		} else if (!empty($v->btn_qrox_bp_byweek_maryland) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('maryland', 'annapolis', 'baltimore', 'cumberlandvalley', 'easternshore', 'frederick', 'westernmaryland');
		} else if (!empty($v->btn_qrox_bp_byweek_virginia) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('charlottesville', 'chesapeake', 'danville', 'fredericksburg', 'hampton', 'harrisonburg', 'lynchburg', 'blacksburg', 'newportnews', 'norfolk', 'portsmouth', 'richmond', 'roanoke', 'swva', 'suffolk', 'virginiabeach');
		} else if (!empty($v->btn_qrox_bp_byweek_texas) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('abilene', 'amarillo', 'austin', 'beaumont', 'collegestation', 'corpuschristi', 'dallas', 'delrio', 'denton', 'elpaso', 'fortworth', 'galveston', 'houston', 'huntsvilletx', 'killeen', 'laredo', 'lubbock', 'mcallen', 'arlington', 'odessa', 'sanantonio', 'sanmarcos', 'texarkana', 'texoma', 'tyler', 'victoriatx', 'waco', 'wichitafalls');
		} else if (!empty($v->btn_qrox_bp_byweek_illinois) && !empty($v->qrox_bp_byweek_numweeks)) {
			$theRegionList = array('bloomington', 'carbondale', 'chambana', 'chicago', 'decatur', 'lasalle', 'mattoon', 'peoria', 'rockford', 'springfieldil', 'quincy');
		}
		
		$v->results = array();
		if (!empty($theRegionList)) {
			$dbMemexHt = $this->getProp('MemexHt');
			$v->source_list = $dbMemexHt->getSourceList(true);
			$v->results = $dbMemexHt->qroxBackpageNumAdsByWeek($theRegionList, $v->qrox_bp_byweek_numweeks);
			if (empty($v->results)) {
				$v->results = null;
				$v->addUserMsg($v->getRes('generic/msg_nothing_found'));
			}
		}
	}
	
	public function qtaskPhone() {
		if (!$this->isAllowed('roxy','run_reports'))
			return $this->getHomePage();
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('qrox');
		
		$dbRoxyMicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
		$v->results = $dbRoxyMicrotaskPhone->getPhoneScores($v);
	}
	
}//end class

}//end namespace


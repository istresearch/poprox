<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor;
use BitsTheater\Scene as MyScene;
	/* @var $v MyScene */
use ISTResearch_Roxy\models\Spiders;
	/* @var $dbSpiders Spiders */
use ISTResearch_Roxy\models\MemexHt;
	/* @var $dbMemexHt MemexHt */
use ISTResearch_Roxy\models\Scrapes;
	/* @var $dbScrapes Scrapes */
use ISTResearch_Roxy\models\RoxyStats;
	/* @var $dbRoxyStats RoxyStats */
{//namespace begin

class Stats extends Actor {
	const DEFAULT_ACTION = 'view';

	public function view() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'dashboard')) {
			$dbMemexHt = $this->getProp('MemexHt');
			$v->source_list = $dbMemexHt->getSourceList();
			array_unshift($v->source_list, array('id'=>0, 'name'=>'totals') );
		} else {
			return $this->getHomePage();
		}
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('view');
	}

	public function ingest() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'monitoring')) {
			$dbMemexHt = $this->getProp('MemexHt');
			$v->source_list = $dbMemexHt->getSourceList();
			$v->source_list[] = array('id'=>0, 'name'=>'totals');
		} else {
			return $this->getHomePage();
		}
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('view');
	}
	
	public function spiders() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'monitoring')) {
			$v->spiders = array();
		} else {
			return $this->getHomePage();
		}
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('view');
	}
	
	/**
	 * Ingest stats and spiders on one page.
	 * @return string Returns redirect URL, if any.
	 */
	public function monitoring() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if (!$this->isAllowed('roxy', 'monitoring')) {
			return $this->getHomePage();
		}
		//indicate what top menu we are currently in
		$this->setCurrentMenuKey('view');
	}
	
	/**
	 * Ajax-only call. Returns MemexHt Stats display.
	 */
	public function ajaxDisplayMemexHtStats($aSourceId=0) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'dashboard')) {
			$dbMemexHt = $this->getProp('MemexHt');
			if ($aSourceId>0)
				$v->source_row = $dbMemexHt->getSourceInfo($aSourceId);
			else
				$v->source_row = array('id'=>0, 'name'=>'totals');
			$v->results = $dbMemexHt->qroxMemexHtStats($aSourceId,true);
		}
	}
	
	/**
	 * Ajax-only call. Returns MemexHt Stats display.
	 */
	public function ajaxDisplayMemexIngestStats($aSourceId=0) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'monitoring')) {
			$dbMemexHt = $this->getProp('MemexHt');
			$dbScrapes = $this->getProp('Scrapes');
			if ($aSourceId>0)
				$v->source_row = $dbMemexHt->getSourceInfo($aSourceId);
			else
				$v->source_row = array('id'=>0, 'name'=>'totals');
			$v->results = $dbMemexHt->qroxIngestStats($aSourceId,true);
			$v->results['queue_depth'] = $dbScrapes->getQueueDepth($v->source_row['name']);
		}
	}
	
	/**
	 * Ajax-only call. Returns Spider Status display.
	 */
	public function ajaxDisplayRoxySpiders() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'monitoring')) {
			$dbSpiders = $this->getProp('Spiders');
			$v->results = $dbSpiders->getSpiders();
		}
	}
	
	/**
	 * Ajax-only call. Returns cached stats display.
	 */
	public function ajaxDisplayCachedStats() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
	
		if ($this->isAllowed('roxy', 'dashboard')) {
			//use the cached stats
			$dbRoxyStats = $this->getProp('RoxyStats');
			$v->results = $dbRoxyStats->qroxAdPostStats();
		}
	}

	/**
	 * Ajax-only call. Returns cached ingestion stats display.
	 */
	public function ajaxDisplayCachedIngestStats() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
	
		if ($this->isAllowed('roxy', 'monitoring')) {
			//use the cached stats
			$dbRoxyStats = $this->getProp('RoxyStats');
			$v->results = $dbRoxyStats->qroxIngestStats();
		}
	}

}//end class

}//end namespace

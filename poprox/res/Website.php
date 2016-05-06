<?php
namespace BitsTheater\res;
use BitsTheater\res\BitsWebsite as BaseResources;
use com\blackmoonit\Arrays;
{//begin namespace

class Website extends BaseResources {
	public $feature_id = 'IST Research: Poprox website'; //DO NOT TRANSLATE!
	public $version_seq = 3;	//build number, inc if db models need updating
	public $version = '2.5.4';	//displayed version text
	
	public $list_patrons_html = array(
			'prime_investor' => '<a href="http://www.istresearch.com/">IST Research, LLC.</a>',
	);
	
	//public $list_credits_html_more = array(
	//);
	
	/**
	 * Some resources need to be initialized by running code rather than a static definition.
	 * @see \BitsTheater\res\Website::setup()
	 */
	public function setup($aDirector) {
		parent::setup($aDirector);
		
		//we do not want the following libs universally loaded, case by case only
		//Bootstrap
		unset($this->css_load_list['bootstrap/css/bootstrap.css']);
		Arrays::removeValue($this->js_libs_load_list, 'bootstrap/js/bootstrap.min.js');
		//Bootbox
		Arrays::removeValue($this->js_libs_load_list, 'bootbox/bootbox.js');

		//NULL path means use default lib path
		$this->res_array_merge($this->js_load_list, array(
				//minification from http://www.jsmini.com/ using Basic and no jquery included.
				'webapp_mini.js' => WEBAPP_JS_URL,
				//  !-remove the below space and comment out the above line to debug un-minified JS code
				/* * /
				'webapp.js' => WEBAPP_JS_URL,
				'BitsRightGroups.js' => WEBAPP_JS_URL,
				//'AnotherFile.js' => WEBAPP_JS_URL,
				/* end of webapp JS */
		));

		//NULL path means use default lib path
		$this->res_array_merge($this->css_load_list, array(
				'roxy.css' => BITS_RES.'/style',
		));

		$this->res_array_merge($this->list_credits_html, array(
				'roxy_logo' => '<a target="_blank" href="http://www.behance.net/gallery/Human-TraffickingAwareness-Posters/10072737">Logo</a> non-com (c) by <a target="_blank" href="http://paodj.prosite.com/163867/resume">Paola</a> <a target="_blank" href="mailto:me@paodiaz.com">Diaz</a>',
		));
	}
	
	/**
	 * Override this function if your website needs to do some updates that are not database related.
	 * Throw an exception if your update did not succeed.
	 * @param number $aSeqNum - the version sequence number (<= what is defined in your overriden Website class).
	 * @throws Exception on failure.
	 */
	public function updateVersion($aSeqNum) {
		try {
			//nothing to do, yet
		} catch (Exception $e) {
			//throw exception if your update code fails (logging it would be a good idea, too).
			$this->debugLog(__METHOD__.' '.$e->getMessage());
			throw $e;
		}
	}

}//end class

}//end namespace

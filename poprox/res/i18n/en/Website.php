<?php
namespace BitsTheater\res\en;
use BitsTheater\res\Website as BaseResources;
{//begin namespace

class Website extends BaseResources {
	public $feature_id = 'IST Research: Poprox website';
	public $version_seq = 1;	//build number, inc if db models need updating, override this in descendant
	public $version = '2.3.0';	//displayed version text, override this in descendant
	
	public $header_meta_title = 'Roxy';
	public $header_title = 'Roxy';
	public $header_subtitle = 'Turn on the spotlight.';
	
	public $list_patrons_html = array(
			'prime_investor' => '<a href="http://www.istresearch.com/">IST Research, LLC.</a>',
	);
	
	/**
	 * Some resources need to be initialized by running code rather than a static definition.
	 * @see \BitsTheater\res\BaseWebsite::setup()
	 */
	public function setup($aDirector) {
		parent::setup($aDirector);
		
		//we do not want the following libs universally loaded, case by case only
		//Bootstrap
		unset($this->css_load_list['bootstrap/css/bootstrap.css']);
		unset($this->js_libs_load_list['bootstrap/js/bootstrap.min.js']);
		//Bootbox
		unset($this->js_libs_load_list['bootbox/bootbox.js']);

		//NULL path means use default lib path path
		$this->res_array_merge($this->css_load_list, array(
				'roxy.css' => BITS_RES.'/style',
		));

		$this->res_array_merge($this->list_credits_html, array(
				'roxy_logo' => '<a target="_blank" href="http://www.behance.net/gallery/Human-TraffickingAwareness-Posters/10072737">Logo</a> non-com (c) by <a target="_blank" href="http://paodj.prosite.com/163867/resume">Paola</a> <a target="_blank" href="mailto:me@paodiaz.com">Diaz</a>',
		));
	}
	
}//end class

}//end namespace

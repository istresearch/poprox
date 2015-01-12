<?php
namespace BitsTheater\res\en;
use BitsTheater\res\en\CorePermissions as BaseResources;
use BitsTheater\costumes\EnumResEntry;
{//begin namespace

class Permissions extends BaseResources {
	
	public $label_my_namespaces = array(
		'roxy' => 'Roxy',
	);
	public $desc_my_namespaces = array(
		'roxy' => 'Roxy Database Access',
	);
	
	public $label_roxy = array(
		'poprox' => 'PopRox',
		'dashboard' => 'Statistics',
		'monitoring' => 'Scrape Monitoring',
		'mtask' => 'Microtask',
		'view_data' => 'View Records',
		'run_reports' => 'Run Reports',
	);
	public $desc_roxy = array(
		'poprox' => 'Able to score Ads/Providers/Organizations/Reviews',
		'dashboard' => 'View dashboard statistics.',
		'monitoring' => 'View scrape statistics and spider states.',
		'mtask' => 'Able to conduct microtasks.',
		'view_data' => 'View Ads, Reviews, Photos, etc.',
		'run_reports' => 'Conduct searches and run reports.',
	);
	
	
	/**
	 * Some resources need to be initialized by running code rather than a static definition.
	 */
	public function setup($aDirector) {
		$this->res_array_merge($this->label_namespace, $this->label_my_namespaces);
		$this->res_array_merge($this->desc_namespace, $this->desc_my_namespaces);
		//parent can handle the rest once "*_namespace" is updated
		parent::setup($aDirector);
	}
			
}//end class

}//end namespace

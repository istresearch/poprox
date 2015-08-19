<?php
namespace BitsTheater\res\en;
use BitsTheater\res\en\BitsPermissions as BaseResources;
use BitsTheater\costumes\EnumResEntry;
{//begin namespace

class Permissions extends BaseResources {
	
	public $label_my_namespaces = array(
			'roxy' => 'Roxy',
			'roxy_api' => 'Roxy Data API',
	);
	public $desc_my_namespaces = array(
			'roxy' => 'Roxy Database Access',
			'roxy_api' => 'Data API (external/mobile apps)',
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
	
	public $label_roxy_api = array(
			'access' => 'Access the Data API',
	);
	public $desc_roxy_api = array(
			'access' => 'Allows a mobile or external app to interact with the server.',
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

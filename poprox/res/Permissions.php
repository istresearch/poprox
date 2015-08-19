<?php
namespace BitsTheater\res;
use BitsTheater\res\BitsPermissions as BaseResources;
{//begin namespace

class Permissions extends BaseResources {

	public $enum_my_namespaces = array(
			'roxy',
			'roxy_api',
	);
	
	public $enum_roxy = array(
			'poprox',
			'dashboard',
			'monitoring',
			'mtask',
			'view_data',
			'run_reports',
	);
	
	public $enum_roxy_api = array(
			'access',
	);
	
	/**
	 * Some resources need to be initialized by running code rather than a static definition.
	 */
	public function setup($aDirector) {
		$this->res_array_merge($this->enum_namespace, $this->enum_my_namespaces);
		//parent can handle the rest once "enum_namespace" is updated
		parent::setup($aDirector);
	}
	
}//end class

}//end namespace

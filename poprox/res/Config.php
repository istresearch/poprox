<?php
namespace BitsTheater\res;
use BitsTheater\res\CoreConfig as BaseResources;
{//begin namespace

class Config extends BaseResources {

	public $enum_my_namespaces = array(
			'poprox',
	);
	
	public $enum_poprox = array(
			'ht_classifier_score_enabled',
			'ht_classifier_score_url',
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

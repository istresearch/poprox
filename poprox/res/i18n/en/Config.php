<?php
namespace BitsTheater\res\en;
use BitsTheater\res\en\BitsConfig as BaseResources;
use BitsTheater\costumes\ConfigSettingInfo;
use com\blackmoonit\Strings;
{//begin namespace

class Config extends BaseResources {

	public $label_my_namespaces = array(
			'poprox' => 'Poprox',
	);
	public $desc_my_namespaces = array(
			'poprox' => 'Poprox Settings',
	);
	
	public $label_poprox = array(
			'ht_classifier_score_enabled' => 'Use CMU\'s ML Classifier Service',
			'ht_classifier_score_url' => 'CMU\'s ML Classifier Service URL',
	);
	public $desc_poprox = array(
			'ht_classifier_score_enabled' => 'CMU\'s Machine Learning Classifier Service may be employed for additional ad information.',
			'ht_classifier_score_url' => 'URL required to use CMU\'s Machine Learning Classifier Service.',
	);
	public $input_poprox = array(
			'ht_classifier_score_enabled' => array(
					'type' => ConfigSettingInfo::INPUT_BOOLEAN,
					'default' => '0',
			),
			'ht_classifier_score_url' => array(
					'type' => ConfigSettingInfo::INPUT_STRING,
					'default' => 'http://localhost:8000',
			),
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

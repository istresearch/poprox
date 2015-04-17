<?php
namespace BitsTheater\res\en;
use BitsTheater\res\Website as BaseResources;
{//begin namespace

class Website extends BaseResources {
	
	public $header_title = 'Roxy';
	public $header_subtitle = 'Turn on the spotlight.';
	
	public $menu_home_label = 'Home';
	public $menu_home_subtext = '';
	
	/**
	 * Some resources need to be initialized by running code rather than a static definition.
	 * @see \BitsTheater\res\Website::setup()
	 */
	public function setup($aDirector) {
		parent::setup($aDirector);
		
		if (empty($this->header_meta_title)) {
			$this->header_meta_title = 'Roxy';
		}
	}

}//end class

}//end namespace

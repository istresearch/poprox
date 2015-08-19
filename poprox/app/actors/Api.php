<?php
namespace ISTResearch_Roxy\actors;
use BitsTheater\Actor as BaseActor;
use BitsTheater\costumes\SqlBuilder;
use ISTResearch_Roxy\models\MemexHt; /* @var $dbMemexHt MemexHt */
use com\blackmoonit\Strings;
{//namespace begin

class Api extends BaseActor {
	const DEFAULT_ACTION = 'view';

	public function view() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		$this->viewToRender('results_as_json');
		$v->results = null;
	}
	
	/**
	 * A gateway device downloaded a set of surveys and is acknowleging
	 * their successful transmission.
	 */
	public function viewRawHtml() {
		//TODO Memex decided against this route, disable for now
		return $this->view();
		/*
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		//auth
		//if ($this->isAllowed('roxy_api', 'access')) {
		//normally, we would require authentication, but this feature we'll provide for free to the public
		//  since we're trying to display "safe html" without any javascript
		//} else {
		//	$v->dieAsBasicHttpAuthFailure();
		//}
		*/
	}
	
	/**
	 * Provide the blur function as a feature for the viewRawHtml() method.
	 */
	public function ajaxToggleBlurInViewRawHtml() {
		$this->viewToRender('_blank');
		$this->director['viewRawHtml_blur_images'] = !($this->director['viewRawHtml_blur_images']);
	}
	
}//end class

}//end namespace

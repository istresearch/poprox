<?php
namespace BitsTheater\actors;
use BitsTheater\Actor as BaseActor;
use BitsTheater\Scene as MyScene; /* @var $v MyScene */
use ISTResearch_Roxy\models\MemexHt; /* @var $dbMemexHt MemexHt */
use com\blackmoonit\Strings;
{//namespace begin

class AdSources extends BaseActor {
	const DEFAULT_ACTION = 'getList';
	
	public function getList() {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if ($this->isAllowed('roxy', 'monitoring')) {
			$v->dbMemexHt = $this->getProp('MemexHt');
		}
	}
	
	public function view($aSourceId=null) {
		//shortcut variable $v also in scope in our view php file.
		$v =& $this->scene;
		if (!empty($aSourceId)) {
			if ($this->isAllowed('roxy', 'monitoring')) {
				$dbMemexHt = $this->getProp('MemexHt');
				$v->results = $dbMemexHt->getSourceInfo($aSourceId,true);
			}
		} else {
			return $this->getList();
		}
	}
	
}//end class

}//end namespace


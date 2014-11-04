<?php

namespace ISTResearch_Roxy\models;
use ISTResearch_Roxy\models\Roxymicrotask as BaseModel;
{//begin namespace

/**
 * "com.istresearch.roxymicrotask" package handler for WebSocket website.
 */
class RoxymicrotaskTesting extends BaseModel {
	
	public function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		$this->dbRoxyMicrotaskPhone = $this->getProp('RoxymicrotaskPhone');
	}
	
}//end class

}//end namespace
	

namespace ISTResearch_Joka\models;
use ISTResearch_Roxy\models\RoxymicrotaskTesting as BaseModel;
{//begin namespace

/**
 * "com.istresearch.roxymicrotask" package handler for WebSocket website.
 */
class RoxymicrotaskTesting extends BaseModel {
	public $dbConnName = 'roxy_webapp_testing';

}//end class

}//end namespace


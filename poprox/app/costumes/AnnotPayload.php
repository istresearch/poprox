<?php

namespace ISTResearch_Roxy\costumes;
use BitsTheater\costumes\AJokaPayload as BasePayload;
use BitsTheater\costumes\JokaPackage;
use com\blackmoonit\Strings;
{//begin namespace

/**
 * Annot Phone payload based on the standard Joka 
 * ancestor class and used as the JokaPackage->payload.
 */
class AnnotPayload extends BasePayload {
	const ACTION_REQUEST_LINE = 'request_line';   //inbound, get next line to score
	const ACTION_DELIVER_LINE = 'deliver_line';   //outbound, deliver next line to score
	const ACTION_DELIVER_RESULT = 'deliver_result'; //inbound, score current phone entry
	
	//all payloads
	public $action;
	
	//all actions
	public $contact_id;
	
	//action deliver phone & deliver result
	public $annot_id;
	
	//action deliver phone
	public $raw_text;
	
	//action deliver result
	public $result;

}//end class

}//end namespace

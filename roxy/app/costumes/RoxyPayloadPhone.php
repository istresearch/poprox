<?php

namespace ISTResearch_Roxy\costumes;
use BitsTheater\costumes\AJokaPayload as BasePayload;
use BitsTheater\costumes\JokaPackage;
use com\blackmoonit\Strings;
{//begin namespace

/**
 * Roxy Microtask Phone payload based on the standard Joka 
 * ancestor class and used as the JokaPackage->payload.
 */
class RoxyPayloadPhone extends BasePayload {
	const ACTION_REQUEST_PHONE = 'request_phone';   //inbound, get next phone to score
	const ACTION_DELIVER_PHONE = 'deliver_phone';   //outbound, deliver next phone to score
	const ACTION_DELIVER_RESULT = 'deliver_result'; //inbound, score current phone entry
	
	//all payloads
	public $action;
	
	//all actions
	public $contact_id;
	
	//action deliver phone & deliver result
	public $phone_id;
	
	//action deliver phone
	public $phone;
	public $phone_text;
	
	//action deliver result
	public $result;

}//end class

}//end namespace

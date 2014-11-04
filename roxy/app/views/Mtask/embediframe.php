<?php
use com\blackmoonit\Widgets;
use \DateInterval;
use \DateTime;
$w = '';

//typical resource patterns
$aCssPattern = '<link rel="stylesheet" type="text/css" href="'.BITS_RES.'/style/%s">'."\n";
$aScriptPattern = '<script type="text/javascript" src="'.BITS_LIB.'/%s"></script>'."\n";
$jbitsScriptPattern = '<script type="text/javascript" src="'.BITS_LIB.'/com/blackmoonit/jBits/%s"></script>'."\n";
$webappJsPattern = '<script type="text/javascript" src="'.WEBAPP_JS_URL.'/%s"></script>'."\n";

//================= HEADER ======================
print('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n");
print('<html xmlns="http://www.w3.org/1999/xhtml">'."\n");
print('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n");
//Theme
//printf($aCssPattern,'bits.css');
print('<link  rel="stylesheet" type="text/css" href="http://static.squarespace.com/universal/styles-compressed/commerce-517e8f1d51c37ffd67fb64390aa84a79-min.css">'."\n");
print('<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Maven+Pro:400|Raleway:600"/>');
print('<link rel="stylesheet" type="text/css" href="//static.squarespace.com/static/sitecss/51cd7e63e4b0898df4c51446/34/50996af1e4b0d2694881a49d/51cd7e64e4b0898df4c516d2/284/1396642085584/site.css?"/>');

print('<style type="text/css">
html {
	background-color:white;
}
		
div.sqs-embed {
	padding:5px;
	height:100%;
	background-color:white;
}
p, button, span {
	font-family: "proxima-nova","Proxima Nova Regular",sans-serif;
	font-size: 16px;
	text-transform: none;
	letter-spacing: 0px;
	text-align: left;
	font-weight: 400;
	font-style: normal;
}
p, span {
	line-height: 1.9em;
	color: #666666;
}
button:disabled {
	color:lightgrey;
} 
</style>');

//jquery
//print('<script src="https://code.jquery.com/jquery-1.10.1.min.js"></script>'."\n");
printf($aScriptPattern,'/jquery/jquery-1.7.min.js');

//local js
printf($jbitsScriptPattern,'jbits_mini.js');
//printf($webappJsPattern,'webapp_mini.js');

$jsCode = <<<EOD
var qUrl = "{$this->getSiteURL("/mtask")}/";
var aUserId = "{$v->myUserId}";
window.phone_fadein = false;

function start_phone_mtask() {
	//document.getElementById("phone_msg_area").innerHTML="Retrieving...";
	window.phone_mtask = new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnum", null, mtask_displayPhoneTask).start();
	$("#phone_area").fadeTo(700,0,"swing",mtask_displayPhoneTask);
}

function mtask_displayPhoneTask(aHtml) {
	if (aHtml)
		window.phone_innerHTML = aHtml;
	if (window.phone_fadein) {
		window.phone_fadein = false;
		document.getElementById("phone_msg_area").innerHTML = "";
		document.getElementById("phone_area").innerHTML = window.phone_innerHTML;
		var x = document.getElementById("mtask_phone_runscript");
		if (x) {
			eval(x.innerHTML);
		}
		$("#phone_area").fadeTo(700,1,"swing");
	} else {
		window.phone_fadein = true;
	}
}

function mtask_phone_accept(aPhoneId, aPhone) {
	//new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore/"+aUserId+"/"+aPhoneId+"/1").start();
	new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore",null,start_phone_mtask)
		.addPostData("uid",aUserId)
		.addPostData("pid",aPhoneId)
		.addPostData("rid","1")
		.start();
}

function mtask_phone_reject(aPhoneId, aPhone) {
	//new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore/"+aUserId+"/"+aPhoneId+"/2").start();
	new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore",null,start_phone_mtask)
		.addPostData("uid",aUserId)
		.addPostData("pid",aPhoneId)
		.addPostData("rid","2")
		.start();
}

function mtask_phone_idk(aPhoneId, aPhone) {
	//new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore/"+aUserId+"/"+aPhoneId+"/3").start();
	new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore",null,start_phone_mtask)
		.addPostData("uid",aUserId)
		.addPostData("pid",aPhoneId)
		.addPostData("rid","3")
		.start();
}

function mtask_phone_suggestion(aPhoneId, aPhone) {
	//new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore/"+aUserId+"/"+aPhoneId+"/4/"+aPhone).start();
	new AjaxDataUpdater(qUrl+"ajaxChtmtaskpnumScore",null,start_phone_mtask)
		.addPostData("uid",aUserId)
		.addPostData("pid",aPhoneId)
		.addPostData("rid","4")
		.addPostData("sid",aPhone)
		.start();
}

function checkEnableSuggestionBtn() {
	if (document.getElementById("field_mtask_phone_suggestion").value!="") { 
		document.getElementById("btn_mtask_phone_suggestion").disabled = false;
	}
}

EOD;
print($v->createJsTagBlock($jsCode));

//================= BODY ======================
$w .= '</head><body><div class="sqs-embed">';

//$w .= '<h2>Microtask: Phone De-obfuscation</h2>'."\n";
$w .= '<h2>Help Us Win The Fight</h2>'."\n";

$w .= '<div id="phone_msg_area" class="mtask_phone">';

$w .= '<div class="mtask_phone instruction_text"><p>';
/*
$w .= <<<EOD
Part of counter human trafficking support is understanding the data that is hiding right in front of us.
Below we will show you original text with a hidden phone number.
We need your help in making sure that our system is doing a good job!
Thanks for supporting the fight against human trafficking!
EOD;
*/
$w .= <<<EOD
Understanding the data that is hiding right in front of us is a 
critical aspect of the effort to counter human trafficking. 
You can help right now by joining our micro tasking team to help us uncover connections within hidden text. <br>
<br>
All you have to do is click “Opt In” below to get started. We will show you original text with a hidden phone number. 
Your responses to the questions we display will help us make sure that our system is doing a good job. 
Thank you for supporting the fight against human trafficking!<br>
<br>
If you believe there may be other ways that you can help us win the fight against human trafficking, please contact us today.<br>
EOD;
$w .= '</p></div><br />'."\n";

$w .= '</div>'."\n";

$w .= '<div id="phone_area" class="mtask_phone">';
$w .= str_repeat('&nbsp;',8);
$w .= '<button class="mtask_phone" id="btn_mtask_start" onClick="start_phone_mtask();">OPT IN (Let\'s get started!)</button>';
$w .= '</div>';

$w .= '</div></body></html>';
print($w);

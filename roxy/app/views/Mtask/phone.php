<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use \DateInterval;
use \DateTime;
$recite->includeMyHeader();
$w = '';
$jsCode = <<<EOD
var qUrl = "{$this->getSiteURL("/mtask")}/";
var aUserId = {$v->myUserId};
window.phone_fadein = false;

function start_phone_mtask() {
	document.getElementById("phone_msg_area").innerHTML="Retrieving another...";
	window.phone_mtask = new AjaxDataUpdater(qUrl+"ajaxPhoneTask", null, mtask_displayPhoneTask).start();
	$("#phone_area").fadeTo(700,0,"swing",mtask_displayPhoneTask);
}

function mtask_displayPhoneTask(aHtml) {
	if (aHtml)
		window.phone_innerHTML = aHtml;
	if (window.phone_fadein) {
		window.phone_fadein = false;
		document.getElementById("phone_msg_area").innerHTML = "&nbsp;";
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
	new AjaxDataUpdater(qUrl+"ajaxPhoneScore/"+aUserId+"/"+aPhoneId+"/1").start();
	start_phone_mtask();
}

function mtask_phone_reject(aPhoneId, aPhone) {
	new AjaxDataUpdater(qUrl+"ajaxPhoneScore/"+aUserId+"/"+aPhoneId+"/2").start();
	start_phone_mtask();
}

function mtask_phone_idk(aPhoneId, aPhone) {
	new AjaxDataUpdater(qUrl+"ajaxPhoneScore/"+aUserId+"/"+aPhoneId+"/3").start();
	start_phone_mtask();
}

function mtask_phone_suggestion(aPhoneId, aPhone) {
	new AjaxDataUpdater(qUrl+"ajaxPhoneScore/"+aUserId+"/"+aPhoneId+"/4/"+aPhone).start();
	start_phone_mtask();
}

function checkEnableSuggestionBtn() {
	if (document.getElementById("field_mtask_phone_suggestion").value!="") { 
		document.getElementById("btn_mtask_phone_suggestion").disabled = false;
	}
}

EOD;
$w .= $v->createJsTagBlock($jsCode);

$w .= '<h2>Microtask: Phone De-obfuscation</h2>'."\n";

$msgStr = $v->renderMyUserMsgsAsString();
if (!empty($msgStr)) {
	$w .= "<br />\n";
	$w .= $msgStr;
	$w .= "<br />\n";
}

$w .= '<div class="mtask_phone instruction_text"><p>';
$w .= <<<EOD
Many of the human trafficking ads contain phone numbers written to thwart automatic detection. 
We have the ability to learn from our mistakes, but we need to teach the computer to know when it has
made a mistake. You can help out this effort by checking the computer's work for us.  Compare the 
number the computer found with the text in which it found the number and let us know if it was 
correctly identified as a phone number or not. Thanks for your assistance!
EOD;
$w .= '</p></div><br />'."\n";

$w .= '<div id="phone_msg_area" class="mtask_phone"></div>'."\n";
$w .= '<div id="phone_area" class="mtask_phone">';
$w .= '<button class="mtask_phone" id="btn_mtask_start" onClick="start_phone_mtask();">OPT IN (Let\'s get started!)</button>';
$w .= '</div>';

$w .= str_repeat('<br />',8);
print($w);
$recite->includeMyFooter();

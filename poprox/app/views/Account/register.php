<?php
use BitsTheater\scenes\Account as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
$recite->includeMyHeader();
$w = '';

$s = $v->getRes('account/msg_pw_nomatch');
//print "<script>function checkPassword(p1,p2) { if (p1.value!=p2.value) {p2.setCustomValidity('\"'+p1.value+'\"!=\"'+p2.value+'\" $s');} else {p2.setCustomValidity('');} }</script>";
print "<script>function checkPassword(p1,p2) { if (p1.value!=p2.value) {p2.setCustomValidity('$s');} else {p2.setCustomValidity('');} }</script>";

$w .= '<h2>Register</h2>';
if (isset($v->err_msg)) {
	$w .= '<span class="msg-error">'.$v->err_msg.'</span>';
} else {
	$w .= $v->renderMyUserMsgsAsString();
}

$theForm = Widgets::createHiddenPost('post_key', $v->post_key);
$theForm .= '<table class="db-entry">';
//make sure fields will not interfere with any login user/pw field in header
$userKey = $v->getUsernameKey().'_reg';
$pwKey = $v->getPwInputKey().'_reg';
$theForm .= '<tr><td class="db-field-label">'.$v->getRes('account/label_name').':</td><td class="db-field">'.
		Widgets::createTextBox($userKey,$v->$userKey,true)."</td></tr>\n";
$theForm .= '<tr><td class="db-field-label">'.$v->getRes('account/label_email').':</td><td class="db-field">'.
		Widgets::createEmailBox('email',$recite->email,true)."</td></tr>\n";
$theForm .= '<tr><td class="db-field-label">'.$v->getRes('account/label_pwinput').':</td><td class="db-field">'.
		Widgets::createPassBox($pwKey,$v->$pwKey,true,60,120)."</td></tr>\n";
$chkpwJs = "checkPassword(document.getElementById('{$pwKey}'), this);";
$js = "onfocus=\"{$chkpwJs}\" oninput=\"{$chkpwJs}\"";
$theForm .= '<tr><td class="db-field-label">'.$v->getRes('account/label_pwconfirm').':</td><td class="db-field">'.
		Widgets::createPassBox('password_confirm',$recite->password_confirm,true,60,120,$js)."</td></tr>\n";
$theForm .= '<tr><td></td><td><em>'.$v->getRes('account/text_reg_cht_microtasking_regcode').'</em></td></tr>';
$theForm .= '<tr><td class="db-field-label">'.$v->getRes('account/label_regcode').':</td><td class="db-field">'.
		Widgets::createTextBox('reg_code',$recite->reg_code,true)."</td></tr>\n";
$theForm .= '<tr><td></td><td><em>'.$v->getRes('account/text_reg_cht_microtasking_phone').'</em></td></tr>';
$theForm .= '<tr><td class="db-field-label">'.$v->getRes('account/label_phone').':</td><td class="db-field">'.
		Widgets::createTextBox('phone',$v->phone,false)."</td></tr>\n";
$theForm .= '<tr><td class="db-field-label"></td><td class="db-field">'.
		Widgets::createSubmitButton('button_register',$v->getRes('account/label_submit'));
		
$theForm .= "</table>\n";

$w .= Widgets::createHtmlForm($recite->form_name,$recite->action_url_register,$theForm,$v->redirect,false);
print($w);
print(str_repeat('<br />',3));
$recite->includeMyFooter();

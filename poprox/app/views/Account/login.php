<?php
use BitsTheater\scenes\Account as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
$h = '';
$h .= $v->getCSStag('bootstrap/css/bootstrap.css');
$h .= $v->getScriptTag('bootstrap/js/bootstrap.min.js');
$h .= $v->getScriptTag('bootbox/bootbox.js');
$recite->includeMyHeader($h);
$w = '';

$w .= '<h2>Login</h2>';
if (isset($v->err_msg)) {
	$w .= '<span class="msg-error">'.$v->err_msg.'</span>';
} else {
	$w .= $v->renderMyUserMsgsAsString();
}
$w .= '<table class="db-entry">' ;

$w .= '<tr><td class="db-field-label">'
    . $v->getRes('account/label_name')
    . ':</td><td class="db-field">'
    . Widgets::createTextBox( $v->getUsernameKey(), $v->getUsername() )
    . '</td></tr>' . PHP_EOL
    ;
$w .= '<tr><td class="db-field-label">'
    . $v->getRes('account/label_pwinput')
    . ':</td><td class="db-field">'
    . Widgets::createPassBox( $v->getPwInputKey(), $v->getPwInput() )
    . '</td></tr>' . PHP_EOL
    ;
$w .= '<tr><td class="db-field-label"></td><td class="db-field">' . PHP_EOL
    . '    '
    . Widgets::createSubmitButton( 'button_login',
    		$v->getRes('account/label_login') ) . PHP_EOL
    . '    <a class="btn btn-primary" id="btn_Register" href="'
    . $v->action_url_register . '">'
    . $v->getRes('account/label_register') . '</a>' . PHP_EOL
    . '    <a class="btn btn-primary" id="btn_PasswordReset" href="'
    . $v->action_url_requestpwreset . '">'
    . $v->getRes('account/label_requestpwreset') . '</a>' . PHP_EOL
    . '</td></tr>' . PHP_EOL
    ;
$w .= "</table>\n" ;

$form_html = Widgets::createForm($recite->action_url_login,$w,$v->redirect) ;
print( $form_html ) ;
print( str_repeat( '<br/>', 3 ) ) ;
$recite->includeMyFooter() ;

<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$w = '';
$jsCode = <<<EOD

EOD;

$w .= '<span class="mtask_phone phone_text">The original text is </span>';
$w .= '<span class="mtask_phone phone_text">"'.htmlentities(trim($v->result['phone_text'])).'".</span>';
$w .= '<br />'."\n";
$w .= '<span class="mtask_phone phone">Is this the phone number </span>';
$w .= '<span class="mtask_phone phone">"';
$w .= preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '($1) $2-$3', $v->result['phone']);
$w .= '"?</span>';
$w .= '<br />'."\n";

$w .= '<br />'."\n";

$args = $v->result['phone_id'].",field_mtask_phone_suggestion.value";
$w .= '<button id="btn_mtask_phone_accept" class="mtask_phone accept" onClick="mtask_phone_accept('.$args.');">Yes</button>';
$w .= str_repeat('&nbsp;',8);
$w .= '<button id="btn_mtask_phone_reject" class="mtask_phone reject" onClick="mtask_phone_reject('.$args.');">No</button>';
$w .= str_repeat('&nbsp;',8);
$w .= '<button id="btn_mtask_phone_idk" class="mtask_phone idk" onClick="mtask_phone_idk('.$args.');">Unknown</button>';
$w .= str_repeat('&nbsp;',8);
$w .= '<input type="text" id="field_mtask_phone_suggestion" value="" size="15" class="mtask_phone suggestion" onkeyup="checkEnableSuggestionBtn()" />';
$w .= ' <button id="btn_mtask_phone_suggestion" class="mtask_phone suggestion" onClick="mtask_phone_suggestion('.$args.');" disabled>Suggest Number</button>';
$w .= '<br />'."\n";

//eval'd by parent webpage in case jsCode is required to be run
//$w .= $v->createJsTagBlock($jsCode, 'mtask_phone_runscript');
print($w);

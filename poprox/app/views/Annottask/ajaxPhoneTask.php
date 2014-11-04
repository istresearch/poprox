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
$w .= '<br />'."\n";

$w .= '<span class="mtask_phone phone_text"><pre>'.strip_tags(trim($v->result['raw_text'])).'</pre></span>';
$w .= '<br />'."\n";
$w .= '<span class="mtask_phone phone">Please transcribe the text into plain English. </span>';
$w .= '<br />'."\n";

$w .= '<br />'."\n";

$args = $v->result['annot_id'].",field_mtask_phone_suggestion.value";
/*
$w .= '<button id="btn_mtask_phone_accept" class="mtask_phone accept" onClick="mtask_phone_accept('.$args.');">Yes</button>';
$w .= str_repeat('&nbsp;',8);
$w .= '<button id="btn_mtask_phone_reject" class="mtask_phone reject" onClick="mtask_phone_reject('.$args.');">No</button>';
$w .= str_repeat('&nbsp;',8);*/
$w .= '<textarea type="text" style="height:200px;" id="field_mtask_phone_suggestion" value="" class="mtask_phone suggestion" rows="10" cols="50" onkeyup="checkEnableSuggestionBtn()" /></textarea>';
$w .= ' <button id="btn_mtask_phone_suggestion" class="mtask_phone suggestion" onClick="mtask_phone_suggestion('.$args.');" disabled>Submit Transcription</button>';
$w .= str_repeat('&nbsp;',8);
$w .= '<button id="btn_mtask_phone_idk" class="mtask_phone idk" onClick="start_phone_mtask();">Get New</button>';
$w .= '<br />'."\n";
$w .= '<br />'."\n";
$w .= '<a href="https://docs.google.com/document/d/1khNPdRkiM-1OOEoNMw0C1hf29Nv8JO_ywobZfTXIF4I/edit?usp=sharing" target="_blank">Guidelines</a>'."\n";

//eval'd by parent webpage in case jsCode is required to be run
//$w .= $v->createJsTagBlock($jsCode, 'mtask_phone_runscript');
print($w);

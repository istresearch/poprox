<?php
use ISTResearch_Roxy\scenes\Api as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Strings;
use com\blackmoonit\Widgets;
$recite->includeMyHeader();

$theToggleBlurUrl = $v->getSiteURL('api/ajaxToggleBlurInViewRawHtml');
$jsCode = <<<EOD
function toggleBlurPhotos() {
	$(".img-blurable").toggleClass("blur");
	$.post("{$theToggleBlurUrl}");
}

function onKeyUp(e) {
	if (e.keyCode == 120 || e.keyCode == 88) {
		toggleBlurPhotos();
	}
}
document.addEventListener('keyup', onKeyUp, false);
$( document ).ready(function() {
	var theRenderArea = $("#render_area");
	$("img", theRenderArea).addClass("img-blurable");
	if ($("#start_img_blurred")) {
		$("img", theRenderArea).addClass("blur");
	}
});

EOD;

$w = $v->createJsTagBlock($jsCode);
$jsCode = ''; //reset so code downstream will not duplicate the above scripts

$w .= '<h2>Safe Render without any JavaScript or iFrame tags</h2>'."<br />" . PHP_EOL;
$w .= '<button id="toggle_blur" type="button" onClick="toggleBlurPhotos()">Tap X or Click Here to toggle blur</button><br />' . PHP_EOL;
if ($v->getDirector()['viewRawHtml_blur_images']) {
	$w .= '<div id="start_img_blurred">' . PHP_EOL;
}

if (!empty($v->html_to_view)) {
	/* TODO  Memex decided against this route, but maybe it will be useful later.
	$theData = array();
	$theData['html_text'] = $v->html_to_view;
	//TODO use HTMLPurifier?
	$theData['safe_text'] = $v->safeTextifyAll($theData['html_text'], true);
		
	$w .= '<hr />' . PHP_EOL;
	$w .= '<div id="render_area">' . PHP_EOL;
	
	if ($v->getSiteMode() != $v::SITE_MODE_DEMO)
		$theData['ad_text'] = str_replace('src="http://','src="//',$theData['ad_text']);
	else
		$theData['ad_text'] = str_replace('src="http','x="//',$theData['ad_text']);
		
	$w .= '<td class="memex-ad-text" id="memex_ad_text">'.$theData['ad_text'].'</td></tr>'."\n";
	
	$bShowRawHtml = ($theData['raw_ad_text']!=$theData['ad_text']);
	$w .= '<tr>';
	$w .= '<td class="label memex-ad-text">HTML of Text</td>';
	$w .= '<td class="memex-ad-text">';
	$w .= '<button id="raw_ad_text_show" type="button" onClick="showElement(\'raw_ad_text\')"'.(($bShowRawHtml)?' style="visibility: hidden;"':'').'>Show</button>';
	$w .= '<button id="raw_ad_text_hide" type="button" onClick="hideElement(\'raw_ad_text\')"'.(($bShowRawHtml)?'':' style="visibility: hidden;"').'>Hide</button>';
	$w .= '<br />'."\n";
	$w .= '<span id="raw_ad_text"'.(($bShowRawHtml)?'':' style="display:none;"').'>';
	$w .= Strings::wordWrap($theData['raw_ad_text'],$wordWrapAt,"<br />\n");
	$w .= '</span>';
	$w .= '</td></tr>'."\n";
	*/
}
$w .= str_repeat('<br />'.PHP_EOL,8);
$w .= $v->createJsTagBlock($jsCode, 'roxy_view_raw_html_runscript');
print($w);
$recite->includeMyFooter();

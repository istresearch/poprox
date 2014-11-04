<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$recite->includeMyHeader();
$w = '';

$theJsStatsSourceList = json_encode($v->source_list);
$theUpdateSpidersUrl = $v->getSiteUrl("/stats/ajaxDisplayRoxySpiders");
$jsCode = <<<EOD
function dash_displayRoxySpiders(aHtml) {
	//alert("dispSpiders="+aHtml);
	document.getElementById("roxy_spiders").innerHTML=aHtml;
	eval(document.getElementById("roxy_spiders_runscript").innerHTML);
}

function dash_start_spider_stats() {
	document.getElementById("roxy_spiders").innerHTML="Gathering Spider status data...";
	window.roxy_spiders = new AjaxDataUpdater(null, '{$theUpdateSpidersUrl}', dash_displayRoxySpiders, 1000*60*10).start(10);
}

function dash_getSourceCol(aIdx, aColName) {
	return window.stats_source_list[aIdx][aColName];
}

EOD;
$w .= $v->createJsTagBlock($jsCode);
$jsCode = '';

$w .= '<h2>Spider Statuses</h2>'."<br /><br />\n";

$w .= '<div id="roxy_spiders">';
//$w .= '<button onclick="dash_start_spider_stats();">Click to start Spider status data</button>';
$w .= '</div>';

$jsCode .= <<<EOD
window.stats_source_list = {$theJsStatsSourceList};
window.stats_source_idx = 0;
dash_start_spider_stats();

EOD;
$w .= $v->createJsTagBlock($jsCode);

$w .= str_repeat('<br />',8);
print($w);
$recite->includeMyFooter();

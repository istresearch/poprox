<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$recite->includeMyHeader();
$w = '';

$theCachedStatsUrl = $this->getSiteURL("/stats/ajaxDisplayCachedStats");
$jsCode = <<<EOD
function dash_displayCachedStats(aHtml) {
	document.getElementById("cached_stats").innerHTML=aHtml;
	eval(document.getElementById("cached_stats_runscript").innerHTML);
}

function dash_start_cached_stats() {
	document.getElementById("select_cached_stats").style.visibility="visible";
	document.getElementById("cached_stats").innerHTML="Retrieving cached ingestion statistics...";
	window.cached_stats = new AjaxDataUpdater('{$theCachedStatsUrl}', null, dash_displayCachedStats, 1000*60*0).start();
}

EOD;
$w .= $v->createJsTagBlock($jsCode);
$jsCode = '';

$w .= '<h2>Cached Memex HT Statistics</h2>';
$w .= 'All time ranges are based on Post Time for the ad.';
$w .= "<br /><br />\n";

$w .= '<button id="select_cached_stats" onClick="selectElement(\'tbl_roxydb_stats\');" style="visibility: hidden;" >Select Statistics</button>';
$w .= '<div id="cached_stats">';
$w .= '<button onclick="dash_start_cached_stats();">Click to retrieve calculated statistics</button>';
$w .= '</div>';
$w .= "<br />\n";

$w .= str_repeat('<br />',8);
print($w);
$recite->includeMyFooter();
print($v->createJsTagBlock('dash_start_cached_stats();'));

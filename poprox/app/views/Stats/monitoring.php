<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$recite->includeMyHeader();
$w = '';

$theCachedStatsUrl = $this->getSiteUrl("/stats/ajaxDisplayCachedIngestStats");
$theSpidersStatusUrl = $v->getSiteUrl("/stats/ajaxDisplayRoxySpiders");
$jsCode = <<<EOD
function selectElement(aId) {
	var r = document.createRange();
	var e = document.getElementById(aId);
	var s = window.getSelection();
	r.selectNode(e);
	s.removeAllRanges();
	s.addRange(r);
}

function dash_displayCachedStats(aHtml) {
	document.getElementById("cached_stats").innerHTML=aHtml;
	eval(document.getElementById("cached_stats_runscript").innerHTML);
}

function dash_start_cached_stats() {
	document.getElementById("select_cached_stats").style.visibility="visible";
	document.getElementById("cached_stats").innerHTML="Retrieving cached ingestion statistics...";
	window.cached_stats = new AjaxDataUpdater(null, '{$theCachedStatsUrl}', dash_displayCachedStats, 1000*60*15).start();
}

function dash_displayRoxySpiders(aHtml) {
	//alert("dispSpiders="+aHtml);
	document.getElementById("roxy_spiders").innerHTML=aHtml;
	eval(document.getElementById("roxy_spiders_runscript").innerHTML);
}

function dash_start_spider_stats() {
	document.getElementById("roxy_spiders").innerHTML="Gathering Spider status data...";
	window.roxy_spiders = new AjaxDataUpdater(null, '{$theSpidersStatusUrl}', dash_displayRoxySpiders, 1000*60*15).start();
}

EOD;
$w .= $v->createJsTagBlock($jsCode);

$w .= '<h2>Cached Ingestion Statistics</h2>';
//$w .= "<br />\n";
$w .= '<button id="select_cached_stats" onClick="selectElement(\'tbl_roxydb_stats\');" style="visibility: hidden;" >Select Statistics</button>';
$w .= '<div id="cached_stats">';
$w .= '<button onclick="dash_start_cached_stats();">Click to retrieve calculated ingestion statistics</button>';
$w .= '</div>';
$w .= "<br />\n";

$w .= '<h2>Spider Statuses</h2>';
$w .= '<div id="roxy_spiders">';
//$w .= '<button onclick="dash_start_spider_stats();">Click to start Spider status data</button>';
$w .= '</div>';

$w .= str_repeat('<br />',8);
print($w);
$recite->includeMyFooter();
print($v->createJsTagBlock('dash_start_cached_stats(); dash_start_spider_stats();'));

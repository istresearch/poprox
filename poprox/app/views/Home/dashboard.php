<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$recite->includeMyHeader();
$w = '';
	
$theUpdateMemexHtUrl = $this->getSiteURL("/home/ajaxDashDisplayMemexHtStats");
$theUpdateStatsUrl = $this->getSiteURL("/home/ajaxDashDisplayRoxyStats");
$theUpdateSpidersUrl = $this->getSiteURL("/home/ajaxDashDisplayRoxySpiders");
$theCachedStatsUrl = $this->getSiteURL("/home/ajaxDashDisplayCachedStats");
$jsCode = <<<EOD
function selectElement(aId) {
	var r = document.createRange();
	var e = document.getElementById(aId);
	var s = window.getSelection();
	r.selectNode(e);
	s.removeAllRanges();
	s.addRange(r);
}

function dash_displayMemexHtStats(aHtml) {
	document.getElementById("memexht_stats").innerHTML=aHtml;
	eval(document.getElementById("memexht_stats_runscript").innerHTML);
}

function dash_displayRoxyStats(aHtml) {
	document.getElementById("roxy_stats").innerHTML=aHtml;
	eval(document.getElementById("roxy_stats_runscript").innerHTML);
}

function dash_displayRoxySpiders(aHtml) {
	//alert("dispSpiders="+aHtml);
	document.getElementById("roxy_spiders").innerHTML=aHtml;
	eval(document.getElementById("roxy_spiders_runscript").innerHTML);
}

function dash_displayCachedStats(aHtml) {
	document.getElementById("cached_stats").innerHTML=aHtml;
	eval(document.getElementById("cached_stats_runscript").innerHTML);
}

function dash_start_memex_stats() {
	document.getElementById("select_memexht_stats").style.visibility="visible";
	document.getElementById("memexht_stats").innerHTML="Calculating MemexHt statistics...";
	window.memexht_stats = new AjaxDataUpdater(null, '{$theUpdateMemexHtUrl}', dash_displayMemexHtStats, 1000*60*12).start(2000);
}

function dash_start_roxy_stats() {
	document.getElementById("select_roxy_stats").style.visibility="visible";
	document.getElementById("roxy_stats").innerHTML="Calculating Roxy statistics...";
	window.roxy_stats = new AjaxDataUpdater(null, '{$theUpdateStatsUrl}', dash_displayRoxyStats, 1000*60*15).start(5000);
}

function dash_start_spider_stats() {
	document.getElementById("roxy_spiders").innerHTML="Gathering Spider status data...";
	window.roxy_spiders = new AjaxDataUpdater(null, '{$theUpdateSpidersUrl}', dash_displayRoxySpiders, 1000*60*10).start(4000);
}

function dash_start_cached_stats() {
	document.getElementById("select_cached_stats").style.visibility="visible";
	document.getElementById("cached_stats").innerHTML="Retrieving cached statistics...";
	window.cached_stats = new AjaxDataUpdater('{$theCachedStatsUrl}', null, dash_displayCachedStats, 1000*60*0).start();
}

EOD;
$w .= $v->createJsTagBlock($jsCode);

$w .= '<h2>Dashboard (Cached Roxy Statistics)</h2>'."<br /><br />\n";

/*
$w .= '<button id="select_memexht_stats" onClick="selectElement(\'tbl_memexht_stats\');" style="visibility: hidden;" >Select Memex HT Statistics</button>';
$w .= '<div id="memexht_stats">';
$w .= '<button onclick="dash_start_memex_stats();">Click to start Memex Statistics calculations</button>';
$w .= '</div>';
$w .= "<br />\n";

$w .= '<button id="select_roxy_stats" onClick="selectElement(\'tbl_roxy_stats\');" style="visibility: hidden;" >Select Roxy Statistics</button>';
$w .= '<div id="roxy_stats">';
$w .= '<button onclick="dash_start_roxy_stats();">Click to start Roxy Statistics calculations</button>';
$w .= '</div>';
$w .= "<br />\n";

$w .= '<div id="roxy_spiders">';
$w .= '<button onclick="dash_start_spider_stats();">Click to start Spider status data</button>';
$w .= '</div>';
*/
$w .= '<button id="select_cached_stats" onClick="selectElement(\'tbl_roxydb_stats\');" style="visibility: hidden;" >Select Statistics</button>';
$w .= '<div id="cached_stats">';
$w .= '<button onclick="dash_start_cached_stats();">Click to retrieve calculated statistics</button>';
$w .= '</div>';
$w .= "<br />\n";

$w .= str_repeat('<br />',8);
print($w);
$recite->includeMyFooter();
print($v->createJsTagBlock('dash_start_cached_stats();'));

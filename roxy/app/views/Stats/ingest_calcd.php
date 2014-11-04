<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$recite->includeMyHeader();
$w = '';

$theJsStatsSourceList = json_encode($v->source_list);
$theUpdateMemexHtUrl = $v->getSiteUrl("/stats/ajaxDisplayMemexIngestStats/");
$jsCode = <<<EOD
function ingest_getSourceCol(aIdx, aColName) {
	return window.stats_source_list[aIdx][aColName];
}

function ingest_displayMemexHtStats(aHtml) {
	var e = document.getElementById("memexht_stats_"+window.stats_source_idx);

	if (typeof aHtml!=="undefined") {
		e.innerHTML = aHtml;
		var scpt = document.getElementById("memexht_stats_runscript_"+ingest_getSourceCol(window.stats_source_idx, "id"));
		if (scpt) {
			eval(scpt.innerHTML);
		}
		window.stats_source_idx = window.stats_source_idx + 1;
		e = document.getElementById("memexht_stats_"+window.stats_source_idx);
	}
	
	if (window.stats_source_idx<window.stats_source_list.length) {
		e.innerHTML = "Calculating stats for "+ingest_getSourceCol(window.stats_source_idx, "name")+"...";
		window.memexht_stats.push(new AjaxDataUpdater(
		        '{$theUpdateMemexHtUrl}'+ingest_getSourceCol(window.stats_source_idx, "id"), 
		        null,  
		        ingest_displayMemexHtStats, 1,5).start()
	    );
	}
}

EOD;
$w .= $v->createJsTagBlock($jsCode);
$jsCode = '';

$w .= '<h2>Ingestion Statistics</h2>';
$w .= 'All time ranges are based on the import timestamp.';
$w .= "<br /><br />\n";

$w .= '<button id="select_memexht_stats" onClick="selectElement(\'memexht_stats\');" style="visibility: hidden;" >Select Memex HT Statistics</button>';
$w .= '<div id="memexht_stats">';
//$w .= '<button onclick="dash_start_memex_stats();">Click to start Memex Statistics calculations</button>';
foreach ((array)$v->source_list as $theSourceIdx => $theSourceRow) {
	$w .= '<div id="memexht_stats_'.$theSourceIdx.'">';
	$w .= '</div>';
	$w .= "<br />\n";
}
$w .= '</div>';
$w .= "<br />\n";

$jsCode .= <<<EOD
//auto-start Memex Ingestion stats
document.getElementById("select_memexht_stats").style.visibility="visible";
window.memexht_stats = [];
window.stats_source_list = {$theJsStatsSourceList};
window.stats_source_idx = 0;
ingest_displayMemexHtStats();

EOD;
$w .= $v->createJsTagBlock($jsCode);

$w .= str_repeat('<br />',8);
print($w);
$recite->includeMyFooter();

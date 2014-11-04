<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$w = '';
$jsCode = <<<EOD
function z2l(id,phpTs) {
	var sts=new Date(phpTs*1000),s=sts.toLocaleString();
	document.getElementById(id).innerHTML=s;
}

EOD;

function dash_cnvUtcToLocal($aElemId, $aTimestamp) {
	$theTs = new DateTime($aTimestamp.' UTC');
	return 'z2l("'.$aElemId.'",'.$theTs->getTimestamp().');'."\n";
}

$w .= '<table id="tbl_roxydb_stats" class="data-display">';

//header row1
$r = '<tr class="rowh">';
$r .= '<th class="label-col">Source</th>';
$r .= '<th class="stat-col">All Entries</th>';
$r .= '<th class="stat-col">All Images</th>';
$r .= '<th class="stat-col">All Postings</th>';
$r .= '<th class="stat-col">Distinct Ads</th>';
$r .= '<th class="stat-col">Last 90 Days</th>';
$r .= '<th class="stat-col">Last 30 Days</th>';
$r .= '<th class="stat-col">Last 7 Days</th>';
$r .= '<th class="stat-col">Last 1 Day</th>';
$r .= '<th class="stat-col">Day Count</th>';
$w .= $r."</tr>\n";


foreach ($v->results as $theSourceName => &$theSourceData) {
	$theDisplayName = $theSourceData['display_name'];
	$theStatsInfo =& $theSourceData['stats_info'];
	
	//reset the rowclass stripes
	$v->_rowClass = 1;
	
	/*
	//header row1
	$r = '<tr class="rowh">';
	$r .= '<th class="label-col">'.$theDisplayName.'</th>';
	$r .= '<th class="stat-col">All Entries</th>';
	$r .= '<th class="stat-col">All Images</th>';
	$r .= '<th class="stat-col">All Postings</th>';
	$r .= '<th class="stat-col">Distinct Ads</th>';
	$r .= '<th class="stat-col">Last 90 Days</th>';
	$r .= '<th class="stat-col">Last 30 Days</th>';
	$r .= '<th class="stat-col">Last 7 Days</th>';
	$r .= '<th class="stat-col">Last 1 Day</th>';
	$r .= '<th class="stat-col">Day Count</th>';
	$w .= $r."</tr>\n";
	*/
	if (!empty($theStatsInfo['updated_ts'])) {
		$theStatsTs = $theStatsInfo['updated_ts'];
		$theTsId = Strings::createUUID();
		$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theStatsTs);
		$r = '<span id="'.$theTsId.'">'.$theStatsTs.'</span>';
		
		$w .= '<tr><td colspan="42" class="stat-ts">calculated: '.$r.'</td></tr>';
	}
	
	//row data totals
	$r = '<tr class="'.$v->_rowClass.'">';
	$r .= '<td class="label-col"><strong>'.$theDisplayName.'</strong></td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_entries']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_images']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_distinct']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_90']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_30']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_07']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_01']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_day_count']).'</td>';
	
	$w .= $r."</tr>\n";
	
	if ($theStatsInfo['total_day_count']>0) {
		//$avgf = new NumberFormatter('en_US', NumberFormatter::PERCENT);
		//we don't have a NumberFormatter class, use sprintf
		//row data avgs
		$r = '<tr class="'.$v->_rowClass.'">';
		$r .= '<td class="label-col">Avg #/Day:</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_entries']/$theStatsInfo['total_day_count'],1).'</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_images']/$theStatsInfo['total_day_count'],1).'</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads']/$theStatsInfo['total_day_count'],1).'</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_distinct']/$theStatsInfo['total_day_count'],1).'</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_90']/90,1).'</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_30']/30,1).'</td>';
		$r .= '<td class="stat-col">'.number_format($theStatsInfo['total_ads_07']/7,1).'</td>';
		$r .= '<td></td>';
		$r .= '<td></td>';
		$w .= $r."</tr>\n";
	}

	//spacer row
	$w .= '<tr>'.str_repeat('<td></td>',9)."</tr>\n";
	
}
$w .= '</table>';
$w .= $v->createJsTagBlock($jsCode, 'cached_stats_runscript');
print($w);

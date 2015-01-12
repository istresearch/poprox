<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$w = '';
$jsCode = <<<EOD

EOD;

$w .= '<table id="tbl_roxydb_stats" class="db-display tbl_memexht_stats">';

$theRow = $v->results;
$theSourceId = $v->source_row['id'];
$theSourceName = $v->source_row['name'];

//reset the rowclass stripes
$v->_rowClass = 1;

//header row1
$r = '<tr>';
$r .= '<th colspan="8">'.'Timespan: ';


if ($theRow['total_day_count']>0 && !$v->bUseImportTimes) {
	$theTsId = Strings::createUUID();
	$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theRow['first_posttime']);
	$r .= '<span id="'.$theTsId.'">'.$theRow['first_posttime'].'</span>';

	$r .= ' - ';
	
	$theTsId = Strings::createUUID();
	$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theRow['last_posttime']);
	$r .= '<span id="'.$theTsId.'">'.$theRow['last_posttime'].'</span>';
	
	$r .= ' ('.$theRow['total_day_count'].' days)';
} else if ($theRow['total_day_count']>0 && $v->bUseImportTimes) {
	$theTsId = Strings::createUUID();
	$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theRow['first_posttime']);
	$r .= '<span id="'.$theTsId.'">'.$theRow['first_posttime'].'</span>';

	$r .= ' - ';
	
	$theTsId = Strings::createUUID();
	$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theRow['last_posttime']);
	$r .= '<span id="'.$theTsId.'">'.$theRow['last_posttime'].'</span>';
	
	$r .= ' ('.$theRow['total_day_count'].' days)';
} else {
	$r .= 'none found';
}

$w .= $r.'</th></tr>';

//header row2
$r = '<tr class="rowh">';
$r .= '<th class="label-col">'.$theSourceName.'</th>';
$r .= '<th class="stat-col">All Entries</th>';
$r .= '<th class="stat-col">All Images</th>';
$r .= '<th class="stat-col">All Postings</th>';
$r .= '<th class="stat-col">Distinct Ads</th>';
$r .= '<th class="stat-col">Last 90 Days</th>';
$r .= '<th class="stat-col">Last 30 Days</th>';
$r .= '<th class="stat-col">Last 7 Days</th>';
$w .= $r."</tr>\n";

//row data totals
$r = '<tr class="'.$v->_rowClass.'">';
$r .= '<td class="label-col">Totals:</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_entries']).'</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_images']).'</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_ads']).'</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_ads_distinct']).'</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_ads_90']).'</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_ads_30']).'</td>';
$r .= '<td class="stat-col">'.number_format($theRow['total_ads_07']).'</td>';
$w .= $r."</tr>\n";

if ($theRow['total_day_count']>0) {
	//$avgf = new NumberFormatter('en_US', NumberFormatter::PERCENT);
	//we don't have a NumberFormatter class, use sprintf
	//row data avgs
	$r = '<tr class="'.$v->_rowClass.'">';
	$r .= '<td class="label-col">Avg #/Day:</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_entries']/$theRow['total_day_count'],2).'</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_images']/$theRow['total_day_count'],2).'</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_ads']/$theRow['total_day_count'],2).'</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_ads_distinct']/$theRow['total_day_count'],2).'</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_ads_90']/90,2).'</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_ads_30']/30,2).'</td>';
	$r .= '<td class="stat-col">'.number_format($theRow['total_ads_07']/7,2).'</td>';
	$w .= $r."</tr>\n";
}

//spacer row
$w .= '<tr>'.str_repeat('<td></td>',8)."</tr>\n";


$w .= '</table>';
$w .= $v->createJsTagBlock($jsCode, 'memexht_stats_runscript_'.$theSourceId);
print($w);

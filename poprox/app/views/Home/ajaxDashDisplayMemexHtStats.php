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

$w .= '<table id="tbl_roxydb_stats" class="db-display">';
foreach ((array) $v->results as $theSiteName => $theSiteRow) {
	//reset the rowclass stripes
	$v->_rowClass = 1;
	
	//header row1
	$r = '<tr>';
	$r .= '<th colspan="8">'.'Timespan: ';
	
	if ($theSiteRow['total_day_count']>0) {
		$theTsId = Strings::createUUID();
		$jsCode .= dash_cnvUtcToLocal($theTsId, $theSiteRow['first_posttime']);
		$r .= '<span id="'.$theTsId.'">'.$theSiteRow['first_posttime'].'</span>';
	
		$r .= ' - ';
		
		$theTsId = Strings::createUUID();
		$jsCode .= dash_cnvUtcToLocal($theTsId, $theSiteRow['last_posttime']);
		$r .= '<span id="'.$theTsId.'">'.$theSiteRow['last_posttime'].'</span>';
		
		$r .= ' ('.$theSiteRow['total_day_count'].' days)';
	} else {
		$r .= 'none found';
	}
	$w .= $r.'</th></tr>';
	
	//header row2
	$r = '<tr class="rowh">';
	$r .= '<th>'.$theSiteName.'</th>';
	$r .= '<th>All Entries</th>';
	$r .= '<th>All Images</th>';
	$r .= '<th>All Postings</th>';
	$r .= '<th>Distinct Ads</th>';
	$r .= '<th>Last 90 Days</th>';
	$r .= '<th>Last 30 Days</th>';
	$r .= '<th>Last 7 Days</th>';
	$w .= $r."</tr>\n";
	
	//row data totals
	$r = '<tr class="'.$v->_rowClass.'">';
	$r .= '<td class="label-col">Totals:</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_entries']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_images']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_ads']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_ads_distinct']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_ads_90']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_ads_30']).'</td>';
	$r .= '<td class="stat-col">'.number_format($theSiteRow['total_ads_07']).'</td>';
	$w .= $r."</tr>\n";
	
	if ($theSiteRow['total_day_count']>0) {
		//$avgf = new NumberFormatter('en_US', NumberFormatter::PERCENT);
		//we don't have a NumberFormatter class, use sprintf
		//row data avgs
		$r = '<tr class="'.$v->_rowClass.'">';
		$r .= '<td class="label-col">Avg #/Day:</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_entries']/$theSiteRow['total_day_count']).'</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_images']/$theSiteRow['total_day_count']).'</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_ads']/$theSiteRow['total_day_count']).'</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_ads_distinct']/$theSiteRow['total_day_count']).'</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_ads_90']/90).'</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_ads_30']/30).'</td>';
		$r .= '<td class="stat-col">'.sprintf("%.2f",$theSiteRow['total_ads_07']/7).'</td>';
		$w .= $r."</tr>\n";
	}

	//spacer row
	$r = '<tr>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$r .= '<td></td>';
	$w .= $r."</tr>\n";
	
}
$w .= '</table>';
$w .= $v->createJsTagBlock($jsCode, 'memexht_stats_runscript');
print($w);

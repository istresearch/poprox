<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
use \DateInterval;
use \DateTime;
$recite->includeMyHeader();
$w = '';

$jsCode = <<<EOD

EOD;

$w .= '<h2>Query: All Backpage Ads</h2>'."<br />\n";

$theAction = $v->getMyURL('bp_region');
$w .= '<form method="post" action="'.$theAction.'">'."\n";
if (empty($v->qrox_bp_byweek_numweeks)) {
	$v->qrox_bp_byweek_numweeks = 12;
}
$w .= 'Backpage By-Week Region Ad Count: '.Widgets::createNumericBox('qrox_bp_byweek_numweeks', $v->qrox_bp_byweek_numweeks,false,4,1,1,1000);
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_newyork', 'New York', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_newjersey', 'New Jersey', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_washdc', 'D.C.', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_maryland', 'Maryland', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_virginia', 'Virginia', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_texas', 'Texas', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= ' '.Widgets::createSubmitButton('btn_qrox_bp_byweek_illinois', 'Illinois', 'poprox-jumpto').'&nbsp;&nbsp;&nbsp;&nbsp;';
$w .= "<br />\n";
$w .= '</form>';

$w .= "<br />\n";
$w .= $v->renderMyUserMsgsAsString();
$w .= "<br />\n";

if (!empty($v->results)) {
	$w .= '<button onClick="selectElement(\'qrox-results\');">Select Results</button>';
	$w .= '<table id="qrox-results" class="data-display">';

	//Backpage By-Week Region Ad Count
	$theResultSet = $v->results;

	// create our static week columns
	$theWeekCols = array();
	$theWeekHdrs = array();
	$theByWeekSums = array();
	$theWeekInterval = new DateInterval('P1W');
	$theWeekNumRange = new DateTime();
	for ($wkIdx=0; $wkIdx <= $v->qrox_bp_byweek_numweeks; $wkIdx++) {
		$theKey = $theWeekNumRange->format('oW');
		$theWeekCols[$theKey] = $wkIdx;
		$theWeekHdrs[$theKey] = $theWeekNumRange->format('o W');
		$theByWeekSums[$wkIdx] = 0;
		$theWeekNumRange->sub($theWeekInterval);
	}
	
	//re-tabulate and sum our data: array( region => array( WeekBasis => NumAds ) )
	$theRegionData = array();
	$theRegionSums = array();
	foreach ((array) $theResultSet as $theResultRow) {
		$theRegion = $theResultRow['region'];
		if (empty($theRegionData[$theRegion])) {
			$theRegionData[$theRegion] = array();
			foreach ($theWeekCols as $theWeekBasis => $theWeekIdx) {
				$theRegionData[$theRegion][$theWeekIdx] = 0;
			}
			$theRegionSums[$theRegion] = 0;
		}
		$theWeekIdx = $theWeekCols[$theResultRow['WeekBasis']];
		$theRegionData[$theRegion][$theWeekIdx] = $theResultRow['NumAds'];
		$theRegionSums[$theRegion] += $theResultRow['NumAds'];
		$theByWeekSums[$theWeekIdx] += $theResultRow['NumAds'];
	}
		
	//create our header row
	$w .= '<tr class="rowh"><th>Region</th>';
	foreach ($theWeekHdrs as $theColHeader) {
		$w .= '<th>'.$theColHeader.'</th>';
	}
	$w .= '<th>Total</th></tr>';
	//body rows
	foreach ((array) $theRegionData as $theRegion => $theByWeekCols) {
		$theRow = '<tr class="'.$v->_rowClass.'">';
		$theRow .= '<td>'.$theRegion.'</td>';
		foreach ((array) $theByWeekCols as $theNumAds) {
			$theRow .= '<td class="num">'.$theNumAds.'</td>';
		}
		$theRow .= '<td class="num">'.$theRegionSums[$theRegion].'</td>';
		$theRow .= '</tr>';
		$w .= $theRow;
	}
	//totals row
	$theRow = '<tr class="rowf"><th>totals by week</th>';
	foreach ($theByWeekSums as $theByWeekSum) {
		$theRow .= '<th>'.$theByWeekSum.'</th>';
	}
	$theRow .= '<th>'.(array_sum($theByWeekSums)+0).'</th></tr>';
	$w .= $theRow;
	
	/*  RAW OUTPUT
	$w .= '<tr class="rowh"><th>Region</th><th>Count</th><th>WeekBasis</th></tr>';
	foreach((array) $theResultSet as $theResultRow) {
		$theRow = '<tr class="'.$v->_rowClass.'">';
		$theRow .= '<td>'.$theResultRow['region'].'</td>';
		$theRow .= '<td align="center" style="width:96px">'.$theResultRow['NumAds'].'</td>';
		$theRow .= '<td>'.$theResultRow['WeekBasis'].'</td>';
		
		$theRow .= '</tr>';
		$w .= $theRow;
	}
	*/
	
	$w .= '</table>';
	
	$w .= str_repeat('<br />',2);
	$w .= '<a href="#" class="scrollup">Jump to top</a>';
}

$w .= str_repeat('<br />',8);
$w .= $v->createJsTagBlock($jsCode);
print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
$recite->includeMyFooter();

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

$w .= '<h2>Query: All Current Ads</h2>'."<br />\n";

$theAction = $v->getMyURL('qresult');
$w .= '<form method="post" action="'.$theAction.'">'."\n";
$w .= '<table><tr class="'.$v->_rowClass.'"><td>';
$w .= 'Search for: '.Widgets::createInputBox('qrox_search','text',$v->qrox_search, false, 30)."<br />\n";
$w .= ' Use "%" as a wildcard as per SQL "LIKE" comparitor or else it will use exact text matching.'."<br />\n";
$w .= ' '.Widgets::createSubmitButton('btn_qrox_search_body', 'Search in Ad Title/Body', 'poprox-jumpto')."<br />\n";
$w .= ' '.Widgets::createSubmitButton('btn_qrox_search_attr', 'Search in Extracted Ad Info', 'poprox-jumpto')."<br />\n";
$w .= "<br />\n";
$w .= '</td></tr><tr class="'.$v->_rowClass.'"><td>';
$w .= 'Ad List by Phone: '.Widgets::createInputBox('qrox_phone_ad_links', 'text', $v->qrox_phone_ad_links, false, 15);
$w .= ' '.Widgets::createSubmitButton('btn_qrox_phone', 'Search Phone #s', 'poprox-jumpto')."<br />\n";
$w .= "<br />\n";
$w .= '</td></tr><tr class="'.$v->_rowClass.'"><td>';
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
$w .= '</td></tr></table>';

$w .= '</form>';

$w .= "<br />\n";
$w .= $v->renderMyUserMsgsAsString();
$w .= "<br />\n";

//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
if (!empty($v->results)) {
	$w .= '<button onClick="selectElement(\'qrox-results\');">Select Results</button>';
	$w .= '<table id="qrox-results" class="db-display">';

	foreach ((array) $v->results as $theResultsBySource => $theResultSet) {
		$theResultByWhom = $v->site_display_names[$theResultsBySource];
		$w .= '<tr valign="bottom"><th colspan="3" align="left"><br />Results by '.$theResultByWhom.'</th></tr>';

		//IF PLAIN TEXT SEARCH, display this table output
		if (!empty($v->btn_qrox_search_body) || !empty($v->btn_qrox_search_attr)) {
			$w .= '<tr class="rowh"><th>Phone</th><th>Count</th><th>A Roxy Link</th></tr>';
			foreach((array) $theResultSet as $theResultRow) {
				$theRow = '<tr class="'.$v->_rowClass.'">';
				$theRow .= '<td>'.$theResultRow['phone'].'</td>';
				$theRow .= '<td align="center" style="width:96px">'.$theResultRow['num'].'</td>';
				if (!empty($theResultRow['an_ad_id'])) {
					$theLink = '<a href="'.$v->getSiteURL('ads','view',$theResultRow['an_ad_id']).'" target="_blank">';
					$theLink .= $theResultRow['an_ad_id'].'</a>';
				} else {
					$theLink = '<a href="'.$theResultRow['url'].'" target="_blank">'.$theResultRow['url'].'</a>';
				}
				$theRow .= '<td style="width:96px">'.$theLink.'</td>';
				$theRow .= '</tr>';
				$w .= $theRow;
			}
		}

		//IF AD LIST by PHONE, display this table output
		else if (!empty($v->btn_qrox_phone)) {
			$w .= '<tr class="rowh"><th>Ad ID</th><th>Found In</th><th>Roxy Link</th><th>Site Link</th><th>Phone</th></tr>';
			foreach((array) $theResultSet as $theResultRow) {
				$theRow = '<tr class="'.$v->_rowClass.'">';
				$theRow .= '<td>'.$theResultRow['roxy_id'].'</td>';
				$theRow .= '<td align="center" style="width:96px">'.$theResultRow['site_id'].'</td>';

				$theRoxyLink = '<a href="'.$v->getSiteURL('ads','view',$theResultRow['roxy_id']).'" target="_blank">';
				$theRoxyLink .= $theResultRow['roxy_id'].'</a>';
				$theRow .= '<td style="width:96px;align:right">'.$theRoxyLink.'</td>';
				
				$theSiteLink = '<a href="'.$theResultRow['url'].'" target="_blank">';
				$theSiteLink .= $theResultByWhom.' ad'.'</a>';
				$theRow .= '<td>'.$theSiteLink.'</td>';

				$theRow .= '<td>'.$theResultRow['phone'].'</td>';
				$theRow .= '</tr>';
				$w .= $theRow;
			}
		}
		
		//Backpage By-Week Region Ad Count
		else {
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
		}
	}//foreach
	$w .= '</table>';
	
	$w .= "<br />\n";
	$w .= "<br />\n";
	$w .= '<a href="#" class="scrollup">Jump to top</a>';
} else {
	if (!empty($v->qrox_phone) || !empty($v->qrox_geoloc)) {
		$w .= 'No results.';
	}
}

$w .= str_repeat('<br />',8);
$w .= $v->createJsTagBlock($jsCode);
print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
$recite->includeMyFooter();

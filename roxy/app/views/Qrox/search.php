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

$w .= '<h2>Query: All Ads</h2>'."<br />\n";

$theAction = $v->getMyURL('search');
$w .= '<form method="post" action="'.$theAction.'">'."\n";
$w .= 'Search for: '.Widgets::createInputBox('qrox_search','text',$v->qrox_search, false, 30)."<br />\n";
$w .= ' Use "%" as a wildcard as per SQL "LIKE" comparitor or else it will use exact text matching.'."<br />\n";
$w .= ' '.Widgets::createSubmitButton('btn_qrox_search_body', 'Search in Ad Title/Body', 'poprox-jumpto')."<br />\n";
$w .= ' '.Widgets::createSubmitButton('btn_qrox_search_attr', 'Search in Extracted Ad Info', 'poprox-jumpto')."<br />\n";
$w .= "<br />\n";
$w .= '</form>';

$w .= "<br />\n";
$w .= $v->renderMyUserMsgsAsString();
$w .= "<br />\n";

if (!empty($v->results)) {
	$w .= '<button onClick="selectElement(\'qrox-results\');">Select Results</button>';
	$w .= '<table id="qrox-results" class="data-display">';

	foreach ((array) $v->results as $theResultsBySource => $theResultSet) {
		$theResultByWhom = $v->site_display_names[$theResultsBySource];
		$w .= '<tr valign="bottom"><th colspan="3" align="left"><br />Results by '.$theResultByWhom.'</th></tr>';

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

	}//foreach
	$w .= '</table>';
	
	$w .= str_repeat('<br />',2);
	$w .= '<a href="#" class="scrollup">Jump to top</a>';
} else if (!empty($v->qrox_search)) {
	$w .= 'No results.';
}

$w .= str_repeat('<br />',8);
$w .= $v->createJsTagBlock($jsCode);
print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
$recite->includeMyFooter();

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

$w .= '<h2>Query: All Ads</h2>';
$w .= 'Query the ads on the phone field as well as any extracted phone numbers.'."<br />\n";
$w .= "<br />\n";

$theAction = $v->getMyURL('phone_list');
$w .= '<form method="post" action="'.$theAction.'">'."\n";
$w .= 'Ad List by Phone: '.Widgets::createInputBox('qrox_phone_ad_links', 'text', $v->qrox_phone_ad_links, false, 15)."<br />\n";
$w .= ' Use "%" as a wildcard at the beginning or end of the number or else exact matching will be used.'."<br />\n";
$w .= ' '.Widgets::createSubmitButton('btn_qrox_phone', 'Search Phone #s', 'poprox-jumpto')."<br />\n";
$w .= "<br />\n";
$w .= '</form>';

$w .= "<br />\n";
$w .= $v->renderMyUserMsgsAsString();
$w .= "<br />\n";

if (!empty($v->results)) {
	$w .= 'Count of results: '.count($v->results).'<br />';
	$thePager = null;//$v->getPagerHtml('phone_list');
	if (empty($thePager)) {
		$w .= '<button onClick="selectElement(\'qrox-results\');">Select Results</button>';
	} else {
		$w .= $thePager;
	}
	
	$w .= '<table id="qrox-results" class="data-display">';

	$w .= '<tr class="rowh"><th>Memex ID</th><th>Source</th><th>Roxy Link</th><th>Site Link</th><th>Phone</th></tr>';
	foreach((array) $v->results as $theResultRow) {
		$theResultByWhom = $v->source_list[$theResultRow['source_id']];
		
		$theRow = '<tr class="'.$v->_rowClass.'">';
		$theRow .= '<td>'.$theResultRow['roxy_id'].'</td>';
		$theRow .= '<td align="center" style="width:96px">'.$theResultByWhom.'</td>';

		$theRoxyLink = '<a href="'.$v->getSiteURL('ads','view',$theResultRow['roxy_id']).'" target="_blank">';
		$theRoxyLink .= 'memex ad'.'</a>';
		$theRow .= '<td style="width:96px;align:right">'.$theRoxyLink.'</td>';
		
		$theSiteLink = '<a href="'.$theResultRow['url'].'" target="_blank">';
		$theSiteLink .= $theResultByWhom.' ad'.'</a>';
		$theRow .= '<td>'.$theSiteLink.'</td>';

		$theRow .= '<td>'.$theResultRow['phone'].'</td>';
		$theRow .= '</tr>';
		$w .= $theRow;
	}
		
	$w .= '</table>';
	$w .= $thePager;
	
	$w .= str_repeat('<br />',2);
	$w .= '<a href="#" class="scrollup">Jump to top</a>';
} else if (!empty($v->qrox_phone_ad_links)) {
	$w .= 'No results.';
}

$w .= str_repeat('<br />',8);
$w .= $v->createJsTagBlock($jsCode);
print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
$recite->includeMyFooter();

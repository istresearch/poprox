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

$w .= '<h2>Crowdsourced Phone Cleansing Results</h2>'."<br />\n";

$w .= "<br />\n";
$w .= $v->renderMyUserMsgsAsString();
$w .= "<br />\n";

//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
if (!empty($v->results)) {
	$thePager = $v->getPagerHtml('qtask_phone');
	if (empty($thePager)) {
		$w .= '<button onClick="selectElement(\'qrox-results\');">Select Results</button>';
	} else {
		$w .= $thePager;
	}
	
	$w .= '<table id="qrox-results" class="db-display">';

	$w .= '<tr class="rowh">';
	$w .= '<th>ID</th><th>Phone</th><th>Text</th><th>Levenshtein Distance</th>';
	$w .= '<th># Yes</th><th># No</th><th># IDK</th><th># Suggestions</th><th># Unclear</th>';
	$w .= '<th>Total Results</th><th>Confidence %</th>';
	$w .= '</tr>';
	
	foreach($v->results as $theResultRow) {
		$theRow = '<tr class="'.$v->_rowClass.'">';
		$theRow .= '<td align="center">'.$theResultRow['phone_id'].'</td>';
		$theRow .= '<td>'.$theResultRow['phone'].'</td>';
		$theRow .= '<td>'.htmlentities($theResultRow['phone_text'],null,'UTF-8').'</td>';
		$theRow .= '<td align="center">'.$theResultRow['levenshtein_distance'].'</td>';
		$theRow .= '<td align="right">'.$theResultRow['num_yes'].'</td>';
		$theRow .= '<td align="right">'.$theResultRow['num_no'].'</td>';
		$theRow .= '<td align="right">'.$theResultRow['num_idk'].'</td>';
		$theRow .= '<td align="right">'.$theResultRow['num_suggestions'].'</td>';
		$theRow .= '<td align="right">'.$theResultRow['num_dumb'].'</td>';
		$theTotalNum = $theResultRow['num_yes']+$theResultRow['num_no']+$theResultRow['num_idk']+$theResultRow['num_suggestions']+$theResultRow['num_dumb'];
		$theRow .= '<td align="right">'.$theTotalNum.'</td>';
		$theRow .= '<td align="right">'.(($theTotalNum!=0) ? Strings::format('%.2f',($theResultRow['num_yes']/$theTotalNum)*100) : '').'</td>';
		$theRow .= '</tr>';
		$w .= $theRow;
	}
	$w .= '</table>';
	$w .= $thePager;
	
	$w .= str_repeat('<br />',2);
	$w .= '<a href="#" class="scrollup">Jump to top</a>';
}

$w .= str_repeat('<br />',8);
$w .= $v->createJsTagBlock($jsCode);
print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->results,'<br>'))));
$recite->includeMyFooter();

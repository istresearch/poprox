<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
use \DateTime;
$w = '';
$jsCode = <<<EOD

EOD;

$theUpdateOn = new DateTime($v->results[0]['updated_on'].' Z');
$w .= '<h3>Spiders monitered as of: '.$theUpdateOn->format('D, M d @ H:i:s T');
$theTsId = 'client-spider-timestamp';
$w .= '<br />Locally, that would be: <span id="'.$theTsId.'">Calculating...</span>';
$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $v->results[0]['updated_on']);
$w .= '</h3>';
$w .= '<table class="data-display">';
$theHeaderRow = '<tr class="rowh"><th>id</th><th>Domain</th><th>Spider</th><th>PID</th><th>Runtime</th>'."\n";
foreach ((array) $v->results as $theRow) {
	$bIsMasterSpiderRow = Strings::endsWith($theRow['name'],'_master');
	if ($bIsMasterSpiderRow) {
		$w .= '<tr><td colspan="5">&nbsp;</td></tr>';
		$w .= $theHeaderRow;
		$v->_rowClass = 1; //restart row shading
	}
	$w .= '<tr class="'.$v->_rowClass.(empty($theRow['pid'])?' spider-not-running':'').'">';
	$w .= '<td align="center" width="5%">'.$theRow['id'].'</td>';
	$w .= '<td><a href="'.$theRow['url'].'/jobs">'.$theRow['domain'].'</a></td>';
	if ($bIsMasterSpiderRow) {
		$w .= '<td><strong>'.$theRow['name'].'</strong></td>';
	} else {
		$w .= '<td>'.$theRow['name'].'</td>';
	}
	$w .= '<td align="center" width="10%">'.(empty($theRow['pid'])?'':$theRow['pid']).'</td>';
	$w .= '<td align="right">'.(empty($theRow['pid'])?'not running':$theRow['runtime']).'</td>';
	//$w .= '<td align="right">'.$theRow['updated_on'].'</td>';
	$w .= '</tr>'."\n";
}
$w .= '</table>';
$w .= $v->createJsTagBlock($jsCode, 'roxy_spiders_runscript');
print($w);
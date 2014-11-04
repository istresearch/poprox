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
	$theTs = new DateTime($aTimestamp.' Z');
	return 'z2l("'.$aElemId.'",'.$theTs->getTimestamp().');'."\n";
}

$w .= '<table id="tbl_roxy_stats" class="data-display">';
foreach ((array) $v->results as $theSiteModel => $theSiteRows) {
	$theSiteName = (Strings::beginsWith($theSiteModel,'Site')) ? $v->getProp($theSiteModel)->getSiteDisplayName() : $theSiteModel;
	$w .= '<tr class="rowh"><th>'.$theSiteName.'</th><th>Last Import (Local)</th>';
	$w .= '<th>Total</th><th>Updates</th><th>New (7d)</th><th>New (24h)</th><th>Updates (7d)</th><th>Updates (24h)</th><th>Queue Depth</th>';
	$w .= "</tr>\n";
	foreach ((array) $theSiteRows as $theRow) {
		$w .= '<tr class="'.$v->_rowClass.'">';

		$w .= '<td>'.$theRow['display_name'].'</td>';
		$theTsId = Strings::createUUID();
		$jsCode .= dash_cnvUtcToLocal($theTsId, $theRow['last_datetime']);
		$w .= '<td id="'.$theTsId.'">'.$theRow['last_datetime'].'</td>';
		$w .= '<td align="right" width="10%">'.number_format($theRow['total']).'</td>';
		$w .= '<td align="right" width="10%">'.((!is_null($theRow['total_updates']))?number_format($theRow['total_updates']):'').'</td>';
		$w .= '<td align="right" width="10%">'.number_format($theRow['total_last_7_days']).'</td>';
		$w .= '<td align="right" width="10%">'.number_format($theRow['total_last_24_hrs']).'</td>';
		$w .= '<td align="right" width="10%">'.((!is_null($theRow['total_updates']))?number_format($theRow['total_updated_last_7_days']):'').'</td>';
		$w .= '<td align="right" width="10%">'.((!is_null($theRow['total_updates']))?number_format($theRow['total_updated_last_24_hrs']):'').'</td>';
		$w .= '<td align="right" width="10%">'.((!is_null($theRow['queue_depth']))?number_format($theRow['queue_depth']):'').'</td>';
		
		$w .= '</tr>'."\n";
	}
}
$w .= '</table>';
$w .= $v->createJsTagBlock($jsCode, 'roxy_stats_runscript');
print($w);

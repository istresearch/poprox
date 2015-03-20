<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
$recite->includeMyHeader();
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

$w .= '<h2>Sources</h2>';
$w .= $v->renderMyUserMsgsAsString();
$w .= '<table id="tbl_sources" class="db-display">';

//header row1
$r = '<tr class="rowh">';
$r .= '<th class="">ID</th>';
$r .= '<th class="">Name</th>';
$r .= '<th class="">Scrape Delay</th>';
$w .= $r."</tr>\n";


foreach ($v->results as $theSourceInfo) {
	$theDisplayName = $theSourceInfo['display_name'];
	
	$r = '<tr class="'.$v->_rowClass.'">';
	$r .= '<td class=""><a href="'.$v->getMyUrl('view/'.$theSourceInfo['source_id']).'">'.$theSourceInfo['source_id'].'</a></td>';
	$r .= '<td class=""><a href="'.$v->getMyUrl('view/'.$theSourceInfo['source_id']).'">'.$theDisplayName.'</a></td>';
	$r .= '<td class="">'.$theSourceInfo['scrapedelay'].'</td>';
	$w .= $r."</tr>\n";

}
$w .= '</table>';

$w .= str_repeat('<br />',8);

$w .= $v->createJsTagBlock($jsCode, 'roxy_sources_runscript');
print($w);
$recite->includeMyFooter();

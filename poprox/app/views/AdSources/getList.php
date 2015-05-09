<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
use com\blackmoonit\Strings;
use ISTResearch_Roxy\models\MemexHt; /* @var $dbMemexHt MemexHt */
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

print($w);
unset($w); //free up memory

//data has gotten too big to fit into memory, we need to render as we get data now.
$dbMemexHt = $v->dbMemexHt;
$theSourceCursor = $dbMemexHt->getSourceInfoCursor();
if (!empty($theSourceCursor)) {
	$theSourceInfo = $dbMemexHt->fetchSourceInfo($theSourceCursor, true);
	while (!emtpy($theSourceInfo)) {
		$theDisplayName = $theSourceInfo['display_name'];
		
		$r = '<tr class="'.$v->_rowClass.'">';
		$r .= '<td class=""><a href="'.$v->getMyUrl('view/'.$theSourceInfo['source_id']).'">'.$theSourceInfo['source_id'].'</a></td>';
		$r .= '<td class=""><a href="'.$v->getMyUrl('view/'.$theSourceInfo['source_id']).'">'.$theDisplayName.'</a></td>';
		$r .= '<td class="">'.$theSourceInfo['scrapedelay'].'</td>';
		$r .= "</tr>\n";
		print($r);
	
		$theSourceInfo = $dbMemexHt->fetchSourceInfo($theSourceCursor, true);
	}
}

//back to our regularly scheduled page rendering
$w = '';

$w .= '</table>';

$w .= str_repeat('<br />',8);

$w .= $v->createJsTagBlock($jsCode, 'roxy_sources_runscript');
print($w);
$recite->includeMyFooter();

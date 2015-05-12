<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Strings;
use com\blackmoonit\Widgets;
use ISTResearch_Roxy\models\MemexHt; /* @var $dbMemexHt MemexHt */
$recite->includeMyHeader();

$jsCode = <<<EOD

EOD;

$w = $v->createJsTagBlock($jsCode);
$jsCode = ''; //reset so code downstream will not duplicate the above scripts

$theData = $v->results;
$w .= '<h2>Source: '.$theData['display_name'].' (ID '.$theData['id'].')</h2>';
$w .= $v->renderMyUserMsgsAsString();

$theTsId = Strings::createUUID();
$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theData['updated_ts']);
$tempStr = '<span id="'.$theTsId.'">'.$theData['updated_ts'].'</span>';
$theUpdatedTs = $tempStr.' (local)';

$w .= '<table id="memex-source-info" class="db-display">';
//$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">ID</td>'.'<td class="db-field">'.$theData['id'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label" style="min-width:20ch">Scrape Delay</td>'.'<td class="db-field">'.$theData['scrapedelay'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label" style="min-width:20ch">Error URL</td>'.'<td class="db-field">'.$theData['ad-errorurl'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label" style="min-width:20ch">Req Attribute</td>'.'<td class="db-field">'.$theData['ad-requiredattribute'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label" style="min-width:20ch">Revision Field</td>'.'<td class="db-field">'.$theData['ad-revisionfield'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label" style="min-width:20ch">Updated</td>'.'<td class="db-field">'.$theUpdatedTs.'</td></tr>'."\n";
$w .= '</table>';

print($w);
$w = '';

if (empty($v->pagesz))
	$v->pagesz = 1000; //default page size is 1000
$dbMemexHt = $v->dbMemexHt;
$theSourceAttrs = $dbMemexHt->getSourceAttrsToDisplay($theData['id'], $v);

$w .= $v->getPagerHtml( basename(__FILE__, '.php').'/'.$theData['id'] );
$w .= '<table id="memex-source-info" class="db-display">';
print($w);

if (!empty($theSourceAttrs)) {
	foreach ($theSourceAttrs as $theAttrInfo) {
		$theAttrValue = htmlentities($theAttrInfo['value'], ENT_QUOTES|ENT_SUBSTITUTE, "UTF-8");
		
		$r = '<tr class="'.$v->_rowClass.'">';
		$r .= '<td class="db-field-label" style="min-width:20ch">'.$theAttrInfo['name'].'</td>';
		$r .= '<td class="db-field">'.$theAttrValue.'</td>';
		$r .= "</tr>\n";
		print($r);
	}
}
//back to our regularly scheduled page rendering
$w = '</table>';
$w .= $v->getPagerHtml( basename(__FILE__, '.php').'/'.$theData['id'] );

$w .= str_repeat('<br />',8);

$w .= $v->createJsTagBlock($jsCode, 'roxy_view_source_runscript');
print($w);
$recite->includeMyFooter();

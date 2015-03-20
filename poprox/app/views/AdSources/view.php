<?php
use BitsTheater\Scene as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Strings;
use com\blackmoonit\Widgets;
$recite->includeMyHeader();

$jsCode = <<<EOD

EOD;

$w = $v->createJsTagBlock($jsCode);
$jsCode = ''; //reset so code downstream will not duplicate the above scripts

$theData = $v->results;
$w .= '<h2>Source: '.$theData['display_name'].'</h2>';
$w .= $v->renderMyUserMsgsAsString();

$theTsId = Strings::createUUID();
$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theData['updated_ts']);
$tempStr = '<span id="'.$theTsId.'">'.$theData['updated_ts'].'</span>';
$theUpdatedTs = $tempStr.' (local)';

$w .= '<table id="memex-source-info" class="db-display">';
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">ID</td>'.'<td class="db-field">'.$theData['id'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">Scrape Delay</td>'.'<td class="db-field">'.$theData['scrapedelay'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">Error URL</td>'.'<td class="db-field">'.$theData['ad-errorurl'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">Req Attribute</td>'.'<td class="db-field">'.$theData['ad-requiredattribute'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">Revision Field</td>'.'<td class="db-field">'.$theData['ad-revisionfield'].'</td></tr>'."\n";
$w .= '<tr class="'.$v->_rowClass.'"><td class="db-field-label">Updated</td>'.'<td class="db-field">'.$theUpdatedTs.'</td></tr>'."\n";

if (!empty($theData['attributes'])) {
	//print($v->debugPrint($v->debugStr($theData['attributes'])));
	foreach($theData['attributes'] as $theAttrKey => $theAttrInfo) if (!empty($theAttrInfo)) {
		$theAttrValue = htmlentities($theAttrInfo['value'], ENT_QUOTES|ENT_SUBSTITUTE, "UTF-8");
		$w .= '<tr class="'.$v->_rowClass.'">';
		$w .= '<td class="db-field-label">'.$theAttrInfo['name'].'</td>';
		$w .= '<td class="db-field">'.$theAttrValue.'</td>';
		$w .= "</tr>\n";
	}
}

$w .= '</table>';
	
$w .= str_repeat('<br />',8);

$w .= $v->createJsTagBlock($jsCode, 'roxy_view_source_runscript');
print($w);
$recite->includeMyFooter();

<?php
use ISTResearch_Roxy\scenes\Ads as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Strings;
use com\blackmoonit\Widgets;
//$recite->includeMyHeader();

$jsCode = <<<EOD
function showElement(id) {
	//document.getElementById(id).style.visibility="visible";
	document.getElementById(id).style.display="inline";
	document.getElementById(id+"_hide").style.visibility="visible";
	document.getElementById(id+"_show").style.visibility="hidden";
}

function hideElement(id) {
	//document.getElementById(id).style.visibility="hidden";
	document.getElementById(id).style.display="none";
	document.getElementById(id+"_show").style.visibility="visible";
	document.getElementById(id+"_hide").style.visibility="hidden";
}

EOD;

$w = $v->createJsTagBlock($jsCode);
$jsCode = ''; //reset so code downstream will not duplicate the above scripts

/*
$w .= '<h2>Memex: '.$v->site_info['display_name'];
if (!empty($v->ad_info) && !empty($v->ad_info['entity_type'])) {
	$w .= ' '.$v->ad_info['entity_type'];
}
$w .= ' ad</h2>'."<br />\n";
*/

$theJumpForm = '';
//strip nested <form> tag so "ENTER" works as expected.
//$theAction = $v->getMyUrl('nav');
//$theJumpForm .= '<form method="post" action="'.$theAction.'">'."\n";
$theJumpForm .= 'Memex ID: '.Widgets::createInputBox('jumpto_roxy_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_id', 'Jump To Memex ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
/*
$theJumpForm .= "<br />\n";
$theJumpForm .= 'Ad ID: '.Widgets::createInputBox('jumpto_ad_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_ad', 'Jump To Ad ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
*/
//$theJumpForm .= '</form>';

$w .= $v->renderMyUserMsgsAsString();

$theData = $v->ad_info;
if (!empty($theData)) {
	$theAction = $v->getMyUrl('nav/'.$theData['roxy_id']);
	$w .= '<form method="post" action="'.$theAction.'">'."\n";
	//$w .= '<input type="submit" name="next" value="next" class="hidden" />';
	$w .= '<table id="poprox_score">';// width="95%">'."\n";
	$w .= '<tr>';
	$w .= '<td width="30%" rowspan="2">'.$theJumpForm.'</td>'."\n";
	$w .= '<td width="10%" align="left">'.Widgets::createSubmitButton('poprox_prev', 'Previous ID', 'poprox-prev').'</td>';
	/*
	$bOrigAdClass = empty($theData['first_id'])?' hidden':'';
	$bNextRevClass = (empty($v->nextRevId) || $v->nextRevId==$theData['roxy_id'])?' hidden':'';
	$w .= '<td width="10%" align="left">'.Widgets::createSubmitButton('poprox_prev_rev', 'Earlier Revision', 'poprox-prev-rev'.$bOrigAdClass).'</td>';
	$w .= '<td width="10%" align="center">'.Widgets::createSubmitButton('poprox_orig_rev', 'Original', 'poprox-orig-rev'.$bOrigAdClass).'</td>';
	$w .= '<td width="10%" align="right">'.Widgets::createSubmitButton('poprox_next_rev', 'Next Revision', 'poprox-next-rev'.$bNextRevClass).'</td>';
	*/
	$w .= '<td width="10%" align="right">'.Widgets::createSubmitButton('poprox_next', 'Next ID', 'poprox-next').'</td>';
	$w .= '</tr>';
	$w .= '</table>'."\n";
	
	//$w .= '<br />';
	$w .= '<hr />';
	
	$theAdText = $theData['ad_text'];
	if (!empty($v->prev_ad_info))
		$theData['ad_text'] = $v->diffTextBlock($v->prev_ad_info['ad_text'],$theAdText);
	
	$theData['raw_ad_text'] = $v->safeTextifyAll($theAdText,true);
	if (!empty($v->prev_ad_info))
		$theData['raw_ad_text'] = $v->diffTextBlock($v->safeTextifyAll($v->prev_ad_info['ad_text'],true),$theData['raw_ad_text']);
	
	//if ($v->getSiteMode() != $v::SITE_MODE_DEMO)
	//	$theData['ad_text'] = str_replace('src="http://','src="//',$theData['ad_text']);
	//else
		$theData['ad_text'] = str_replace('src="http','x="//',$theData['ad_text']);
		
	$theData['post_time'] = $v->getSafeDiffField('post_time');
	$theData['city'] = $v->getSafeDiffField('city');
	$theData['state'] = $v->getSafeDiffField('state');
	$theData['region'] = $v->getSafeDiffField('region');
	$theData['country'] = $v->getSafeDiffField('country');
	$theData['email'] = $v->getSafeDiffField('email');
	$theData['phone'] = $v->getSafeDiffField('phone');
	$theData['website'] = $v->getSafeDiffField('website');
	$theData['age'] = $v->getSafeDiffField('age');
	$theData['gender'] = $v->getSafeDiffField('gender');
	$theData['service'] = $v->getSafeDiffField('service');
	
	//outer table [photo column, data column], data column is 2 tables: title info, fixed / attribute info
	$w .= '<table class="memex-ad" width="95%">';
	$w .= '<tr><td colspan="2" class="revisions"><span class="pager">Revisions: </span>'.$v->getAdRevisionPagerHtml($v->listRevIds, $v->current_rev_index).'</td></tr>';
	$w .= '<tr><td valign="top">';
	if (!empty($theData['photos'])) {
		foreach ((array)$theData['photos'] as $thePhotoURL) {
			$w .= '<a href="'.$thePhotoURL.'" target="_blank">';
			//if ($v->getSiteMode()!=$v::SITE_MODE_DEMO) {
			//	$w .= '<img src="'.$thePhotoURL.'" alt="'.$thePhotoURL.'" width="128px" />';
			//} else {
				$w .= '<img src="'.BITS_RES.'/images/example_'.rand(0,35).'.png" alt="'.$thePhotoURL.'" width="128px" />';
			//}
			$w .= '</a>';
			$w .= "<br />\n";
		}
	} else {
		$w .= 'No&nbsp;photos.';
	}
	$w .= '</td>'."\n";
	$w .= '<td  valign="top">';
	
	//=====
	//title info table
	//=====
	$w .= '<table width="100%">';
	//Ad Title, Site Ad Id as URL link to orig ad
	$w .= '<tr>';
	$w .= '<td class="memex-ad-title">';
	if (empty($v->prev_ad_info))
		$w .= $theData['ad_title'];
	else
		$w .= Widgets::diffLines($v->prev_ad_info['ad_title'],$theData['ad_title'],' ');//,'<br />');
	$w .= '</td>';
	$theOrigUrlText = (!empty($theData['site_ad_id'])) ? $theData['site_ad_id'] : 'missing';
	$w .= '<td align="right">Site ID: <a href="'.$theData['url'].'" target="_blank">'.$theOrigUrlText.'</a></td>';
	$w .= "</tr>\n";
	//Post Time, Roxy Id as URL link
	$w .= '<tr>';
	$w .= '<td>Posted on '.$theData['post_time'].' </td>';
	$w .= '<td align="right" width="25%">Memex ID: <a href="';
	$w .= $v->getMyUrl('view/'.$theData['roxy_id']);
	$w .= '">'.Strings::format('%09d',$theData['roxy_id']).'</a></td>';
	$w .= "</tr>\n";
	//Import TS, Update TS
	$w .= '<tr>';
	
	$theTsId = Strings::createUUID();
	$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theData['import_ts']);
	$tempStr = '<span id="'.$theTsId.'">'.$theData['import_ts'].'</span>';
	$w .= '<td>Imported: '.$tempStr.' (local) ID: '.$theData['incoming_id'].'</td>';

	if ($theData['import_ts']!=$theData['updated_ts']) {
		$theTsId = Strings::createUUID();
		$jsCode .= Widgets::cnvUtcTs2LocalStr($theTsId, $theData['updated_ts']);
		$tempStr = '<span id="'.$theTsId.'">'.$theData['updated_ts'].'</span>';
		$w .= '<td align="right">Updated: '.$tempStr.' (local)</td>';
	} else {
		$w .= '<td></td>';
	}
	
	$w .= "</tr>\n";
	$w .= '</table>';
	//$w .= "<br />\n";
	$w .= '<hr />';
	
	//=====
	//fixed data, attribute info table
	//=====
	$w .= '<table id="memex-ad-primary" class="">';
	$w .= '<tr><td class="label">City</td>'.'<td>'.$theData['city'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">State</td>'.'<td>'.$theData['state'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Region</td>'.'<td>'.$theData['region'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Country</td>'.'<td>'.$theData['country'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Email</td>'.'<td>'.$theData['email'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Phone</td>'.'<td>'.$theData['phone'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Website</td>'.'<td>'.$theData['website'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Listed Age</td>'.'<td>'.$theData['age'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Listed Gender</td>'.'<td>'.$theData['gender'].'</td></tr>'."\n";
	$w .= '<tr><td class="label">Services Offered</td>'.'<td>'.$theData['service'].'</td></tr>'."\n";
	
	if (!empty($theData['attributes'])) {
		//print($v->debugPrint($v->debugStr($theData['attributes'])));
		foreach($theData['attributes'] as $theAttrKey => $theAttrInfo) if (!empty($theAttrInfo)) {
			$w .= '<tr>';
			$w .= '<td class="label">'.$theAttrInfo['display_name'].'</td>';
			$w .= '<td class="memex-ad-attribute">';
			$w .= $v->diffAdAttribute($theAttrInfo['name'],$theAttrInfo['value']);
			$w .= '</td>';
			$w .= "</tr>\n";
		}
	}
	if (!empty($v->prev_ad_info) && !empty($v->prev_ad_info['attributes'])) {
		//print($v->debugStr($v->prev_ad_info['attributes']).'<br />');
		//print($v->debugStr($v->prev_ad_info['attributes_diff']).'<br />');
		foreach($v->prev_ad_info['attributes'] as $theAttrKey => $theAttrInfo) {
			if (empty($v->prev_ad_info['attributes_diff'][$theAttrKey]) && !empty($theAttrInfo)) {
				$w .= '<tr>';
				$w .= '<td class="label">'.$theAttrInfo['display_name'].'</td>';
				$w .= '<td class="memex-ad-attribute">';
				$w .= '<del>'.$v->safeTextify($theAttrInfo['value'],true).'</del>';
				$w .= '</td>';
				$w .= "</tr>\n";
			}
		}
	}
	
	$wordWrapAt = 120; //75;
	
	//DO NOT USE CLASS="ad-text" AS AdBlock browser addons will set CSS display:none on it!
	$w .= '<tr><td class="label memex-ad-text">Text</td>';
	$w .= '<td class="memex-ad-text">'.$theData['ad_text'].'</td></tr>'."\n";
	
	$bShowRawHtml = ($theData['raw_ad_text']!=$theData['ad_text']);
	$w .= '<tr>';
	$w .= '<td class="label memex-ad-text">HTML of Text</td>';
	$w .= '<td class="memex-ad-text">';
	$w .= '<button id="raw_ad_text_show" type="button" onClick="showElement(\'raw_ad_text\')"'.(($bShowRawHtml)?' style="visibility: hidden;"':'').'>Show</button>';
	$w .= '<button id="raw_ad_text_hide" type="button" onClick="hideElement(\'raw_ad_text\')"'.(($bShowRawHtml)?'':' style="visibility: hidden;"').'>Hide</button>';
	$w .= '<br />'."\n";
	$w .= '<span id="raw_ad_text"'.(($bShowRawHtml)?'':' style="display:none;"').'>';
	$w .= Strings::wordWrap($theData['raw_ad_text'],$wordWrapAt,"<br />\n");
	$w .= '</span>';
	$w .= '</td></tr>'."\n";
	
	$w .= '</table>';
	
	//=====
	//extracted attributes info table
	//=====
	$w .= '<table id="memex-ad-extracted" class="">';
	if (!empty($theData['extracted_info'])) {
		//print($v->debugPrint($v->debugStr($theData['extracted_info'])));
		foreach ($theData['extracted_info'] as $theInfoByWhom => $theInfoSet) if (!empty($theInfoSet)) {
			$w .= '<tr class="section-header"><th colspan="3" align="left"><br />Extracted Information by '.($theInfoByWhom=='Roxy'?'Memex':$theInfoByWhom).'</th></tr>';
			foreach($theInfoSet as $theAttrInfo) if (!empty($theAttrInfo)) {
				$w .= '<tr>';
				$w .= '<td class="label">'.$theAttrInfo['display_name'].'</td>';
				$w .= '<td>'.$v->safeTextify($theAttrInfo['extracted_value'],true).'</td>';
				if (!empty($theAttrInfo['extracted_raw'])) {
					$w .= '<td> from "'.htmlentities($theAttrInfo['extracted_raw'],ENT_NOQUOTES|ENT_SUBSTITUTE,"UTF-8").'"</td>';
				} else {
					$w .= '<td></td>';
				}
				$w .= "</tr>\n";
			}
		}
	}
	$w .= '</table>';
	
	$w .= '</td></tr></table>';

	$w .= "<br />\n";
	$w .= "<br />\n";
	//$w .= '<button onclick="jump_to_top()">Jump to top</button>';
	$w .= '<a href="#" class="scrollup">Jump to top</a>';
	
	$w .= '</form>';
	
} else {
	$w .= $theJumpForm;
	$w .= "<br />\n";
	$w .= "<br />\n";
	$w .= 'Nothing found.';
}
$w .= str_repeat('<br />',8);

$w .= $v->createJsTagBlock($jsCode, 'roxy_view_ad_runscript');
print($w);
//$recite->includeMyFooter();

<?php
use ISTResearch_Roxy\scenes\Poprox as MyScene;
/* @var $recite MyScene */
/* @var $v MyScene */
use com\blackmoonit\Widgets;
$recite->includeMyHeader();

$w = <<<EOD
<script language="JavaScript">
    function jump_to_top() {
		$('html, body').animate({ scrollTop: 0 }, 'fast');
    }
</script>
EOD;
$w .= '<h2>PopRox: myRedBook Ad</h2>'."<br />\n";

$theAction = $v->getMyURL('jumptoad/rb');
$theJumpForm = '<form method="post" action="'.$theAction.'">'."\n";
$theJumpForm .= 'Roxy ID: '.Widgets::createInputBox('jumpto_roxy_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_id', 'Jump To Roxy ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
$theJumpForm .= "<br />\n";
//$theJumpForm .= 'Region: '.Widgets::createInputBox('jumpto_ad_region','text','',false,20).'???.redbook.com';
//$theJumpForm .= "<br />\n";
$theJumpForm .= 'myRedBook ID: '.Widgets::createInputBox('jumpto_ad_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_ad', 'Jump To Ad ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
$theJumpForm .= '</form>';

$w .= $v->renderMyUserMsgsAsString();

$theData = $v->result;
if (!empty($theData)) {
	$theAction = $v->getSiteURL('/poprox/rb/score');
	$w .= '<form method="post" action="'.$theAction.'">'."\n";
	$w .= '<input type="hidden" name="ad_id" value="'.$theData['id'].'" />'."\n";
	$w .= '<input type="hidden" name="redirect" value="'.$v->getSiteURL('/poprox/rb/ads#poprox_score').'" />'."\n";
	//$w .= Widgets::createSubmitButton('poprox_default', 'Skip', 'hidden');
	$w .= '<input type="submit" name="skip" value="skip" class="hidden" />';
	$w .= '<table id="poprox_score" width="95%">'."\n";
	$w .= '<tr>';
	$w .= '<td width="30%" rowspan="2">'.$theJumpForm.'</td>'."\n";
	$w .= '<td width="10%" >'.Widgets::createSubmitButton('poprox_likely', 'Likely', 'poprox-likely').'</td>';
	$w .= '<td width="10%" >'.Widgets::createSubmitButton('poprox_unlikely', 'Unlikely', 'poprox-unlikely').'</td>';
	$w .= '<td width="10%" >'.Widgets::createSubmitButton('poprox_skip', 'Skip', 'poprox-skip').'</td>';
	$w .= '<td width="10%" >'.Widgets::createSubmitButton('poprox_next', 'Next', 'poprox-skip').'</td>';
	$w .= '</tr>';
	$w .= '<tr><td colspan="3">Notes:<br />'.Widgets::createTextArea('poprox_comment', $theData['poprox']['user_comment'],false,5,100).'</td></tr>'."\n";
	$w .= '</table>'."\n";
	
	$theData['post_time'] = $v->safeTextify($theData['post_time']);
	$theData['city'] = $v->safeTextify($theData['city']);
	$theData['email'] = $v->safeTextify($theData['email']);
	$theData['website'] = $v->safeTextify($theData['website']);
	$theData['ad_text'] = $v->safeTextify($theData['ad_text'],true);
	
	$w .= '<table class="rb-ad">';
	$w .= '<tr><td valign="top">';
	if (!empty($theData['photos'])) {
		foreach ((array)$theData['photos'] as $thePhotoURL) {
			$w .= '<a href="'.$thePhotoURL.'" target="_blank">';
			if ($v->getSiteMode()!=$v::SITE_MODE_DEMO) {
				$w .= '<img src="'.$thePhotoURL.'" alt="'.$thePhotoURL.'" width="128px" />';
			} else {
				$w .= '<img src="'.BITS_RES.'/images/example_'.rand(0,4).'.png" alt="'.$thePhotoURL.'" width="128px" />';
			}
			$w .= '</a>';
			$w .= "<br />\n";
		}
	} else {
		$w .= 'No&nbsp;photos.';
	}
	$w .= '</td>'."\n";
	$w .= '<td  valign="top">';
	
	$w .= '<table width="100%">';
	$w .= '<tr>';
	$w .= '<td class="rb-ad-title">'.$theData['title'].'</td>';
	$w .= '<td align="right">Ad ID: <a href="'.$theData['url'].'" target="_blank">'.$theData['adid'].'</a></td>';	
	$w .= "</tr>\n";
	$w .= '<tr>';
	$w .= '<td>by <strong>'.$theData['name'].'</strong> on '.$theData['post_time'].'</td>';
	$w .= '<td align="right" width="25%">Roxy ID: <a href="';
	$w .= $v->getSiteURL('poprox','rb','ads',$theData['id']);
	$w .= '">'.$theData['id'].'</a></td>';
	$w .= "</tr>\n";
	$w .= '<tr>';
	$w .= '<td>City: '.$theData['city'].'</td>';
	$w .= '<td align="right">views: '.$theData['views'].'</td>';
	$w .= "</tr>\n";
	$w .= '</table>';
	//$w .= "<br />\n";
	$w .= '<hr />';
	
	$w .= '<table cellspacing="0" cellpadding="0">';
	$w .= '<tr valign="top"><td nowrap>Availability</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_availability',$theData['poprox']['availability']).'&nbsp;</td><td>'.$theData['availability'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Name</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_name',$theData['poprox']['name']).'&nbsp;</td><td>'.$theData['name'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Phone</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_phone',$theData['poprox']['phone']).'&nbsp;</td><td>'.$theData['phone'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>RB Inbox</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_rb_inbox',$theData['poprox']['rb_inbox']).'&nbsp;</td><td>'.$theData['rb_inbox'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>email</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_email',$theData['poprox']['email']).'&nbsp;</td><td>'.$theData['email'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Web Site</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_website',$theData['poprox']['website']).'&nbsp;</td><td>'.$theData['website'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Ethnicity</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_ethnicity',$theData['poprox']['ethnicity']).'&nbsp;</td><td>'.$theData['ethnicity'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Age</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_age',$theData['poprox']['age']).'&nbsp;</td><td>'.$theData['age'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Eye Color</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_eye_color',$theData['poprox']['eye_color']).'&nbsp;</td><td>'.$theData['eye_color'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Hair Color</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_hair_color',$theData['poprox']['hair_color']).'&nbsp;</td><td>'.$theData['hair_color'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Build</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_build',$theData['poprox']['build']).'&nbsp;</td><td>'.$theData['build'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Height</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_height',$theData['poprox']['height']).'&nbsp;</td><td>'.$theData['height'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Bust</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_bust',$theData['poprox']['bust']).'&nbsp;</td><td>'.$theData['bust'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Cup</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_cup',$theData['poprox']['cup']).'&nbsp;</td><td>'.$theData['cup'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Kitty</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_kitty',$theData['poprox']['kitty']).'&nbsp;</td><td>'.$theData['kitty'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Rate $/hr</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_rate',$theData['poprox']['rate']).'&nbsp;</td><td>'.$theData['rate'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Incall</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_incall',$theData['poprox']['incall']).'&nbsp;</td><td>'.$theData['incall'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Outcall</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_outcall',$theData['poprox']['outcall']).'&nbsp;</td><td>'.$theData['outcall'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Screening</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_screening',$theData['poprox']['screening']).'&nbsp;</td><td>'.$theData['screening'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Text</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_ad_text',$theData['poprox']['ad_text']).'&nbsp;</td><td>'.$theData['ad_text'].'</td></tr>'."\n";

	if (!empty($theData['extracted_info'])) {
		//print($v->debugPrint($v->debugStr($theData['extracted_info'])));
		foreach ($theData['extracted_info'] as $theInfoByWhom => $theInfoSet) if (!empty($theInfoSet)) {
			$w .= '<tr valign="bottom"><th colspan="3" align="left"><br />Extracted Information by '.$theInfoByWhom.'</th></tr>';
			foreach($theInfoSet as $theInfoLabel => $theInfoValue) if (!empty($theInfoValue)) {
				$w .= '<tr valign="center">';
				$w .= '<td nowrap>'.$theInfoLabel.'</td>';
				$w .= '<td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_ad_text',$theData['poprox']['ad_text']).'&nbsp;</td>';
				$w .= '<td>'.$v->safeTextify($theInfoValue,true).'</td>';
				$w .= '</tr>';
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
	$w .= 'Nothing found.';
}
$w .= str_repeat('<br />',8);

print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->result,'<br>'))));
$recite->includeMyFooter();

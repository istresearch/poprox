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
$w .= '<h2>PopRox: '.$v->site_display_name.' Ad</h2>'."<br />\n";

$theAction = $v->getMyURL('jumptoad/'.$v->site_id);
$theJumpForm = '<form method="post" action="'.$theAction.'">'."\n";
$theJumpForm .= 'Roxy ID: '.Widgets::createInputBox('jumpto_roxy_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_id', 'Jump To Roxy ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
$theJumpForm .= "<br />\n";
$theJumpForm .= 'Ad ID: '.Widgets::createInputBox('jumpto_ad_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_ad', 'Jump To Ad ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
$theJumpForm .= '</form>';

$w .= $v->renderMyUserMsgsAsString();

$theData = $v->result;
if (!empty($theData)) {
	$theAction = $v->getSiteURL('/poprox/'.$v->site_id.'/score');
	$w .= '<form method="post" action="'.$theAction.'">'."\n";
	$w .= '<input type="hidden" name="ad_id" value="'.$theData['id'].'" />'."\n";
	$w .= '<input type="hidden" name="redirect" value="'.$v->getSiteURL('/poprox/'.$v->site_id.'/ads#poprox_score').'" />'."\n";
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
	$theData['ad_text'] = $v->safeTextify($theData['ad_text'],true);
	
	$w .= '<table class="rb-ad" width="95%">';
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
	$w .= '<td class="rb-ad-title">'.$theData['title'].'<br></td>';
	$w .= '<td align="right">Ad ID: <a href="'.$theData['url'].'" target="_blank">'.$theData['adid'].'</a></td>';
	$w .= "</tr>\n";
	$w .= '<tr>';
	$w .= '<td>on '.$theData['post_time'].'</td>';
	$w .= '<td align="right" width="25%">Roxy ID: <a href="';
	$w .= $v->getSiteURL('poprox',$v->site_id,'ads',$theData['id']);
	$w .= '">'.$theData['id'].'</a></td>';
	$w .= "</tr>\n";
	$w .= '</table>';
	//$w .= "<br />\n";
	$w .= '<hr />';
	
	$w .= '<table cellspacing="0" cellpadding="0">';
	//NEW POPROX SITE DATA ========================================================================================================
	//NOTE:  TYPICALLY, THIS IS ALL YOU NEED TO CHANGE (to show all the data on the page). REST OF PAGE CAN USUALLY STAY THE SAME
	$w .= '<tr valign="top"><td nowrap>City</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'city',$theData['poprox']['city']).'&nbsp;</td><td>'.$theData['city'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>User</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'user',$theData['poprox']['user']).'&nbsp;</td><td>'.$theData['user'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Category</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'category',$theData['poprox']['category']).'&nbsp;</td><td>'.$theData['category'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Email</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'email',$theData['poprox']['email']).'&nbsp;</td><td>'.$theData['email'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Phone</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'phone',$theData['poprox']['phone']).'&nbsp;</td><td>'.$theData['phone'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Website</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'website',$theData['poprox']['website']).'&nbsp;</td><td>'.$theData['website'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Available</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'available',$theData['poprox']['available']).'&nbsp;</td><td>'.$theData['available'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Hair</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'hair',$theData['poprox']['hair']).'&nbsp;</td><td>'.$theData['hair'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Eyes</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'eyes',$theData['poprox']['eyes']).'&nbsp;</td><td>'.$theData['eyes'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Height</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'height',$theData['poprox']['height']).'&nbsp;</td><td>'.$theData['height'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Weight</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'weight',$theData['poprox']['weight']).'&nbsp;</td><td>'.$theData['weight'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Cup</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'cup',$theData['poprox']['cup']).'&nbsp;</td><td>'.$theData['cup'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Bust</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'bust',$theData['poprox']['bust']).'&nbsp;</td><td>'.$theData['bust'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Waist</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'waist',$theData['poprox']['waist']).'&nbsp;</td><td>'.$theData['waist'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Hips</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'hips',$theData['poprox']['hips']).'&nbsp;</td><td>'.$theData['hips'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Build</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'build',$theData['poprox']['build']).'&nbsp;</td><td>'.$theData['build'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Reviews1</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'reviews1',$theData['poprox']['reviews1']).'&nbsp;</td><td>'.$theData['reviews1'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Reviews2</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'reviews2',$theData['poprox']['reviews2']).'&nbsp;</td><td>'.$theData['reviews2'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Reviews3</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'reviews3',$theData['poprox']['reviews3']).'&nbsp;</td><td>'.$theData['reviews3'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Reviews4</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'reviews4',$theData['poprox']['reviews4']).'&nbsp;</td><td>'.$theData['reviews4'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Datecheck</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'datecheck',$theData['poprox']['datecheck']).'&nbsp;</td><td>'.$theData['datecheck'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Preferred411</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'preferred411',$theData['poprox']['preferred411']).'&nbsp;</td><td>'.$theData['preferred411'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Twitter</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'twitter',$theData['poprox']['twitter']).'&nbsp;</td><td>'.$theData['twitter'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Youtube</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'youtube',$theData['poprox']['youtube']).'&nbsp;</td><td>'.$theData['youtube'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Latitude</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'latitude',$theData['poprox']['latitude']).'&nbsp;</td><td>'.$theData['latitude'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Longitude</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'longitude',$theData['poprox']['longitude']).'&nbsp;</td><td>'.$theData['longitude'].'</td></tr>'."\n";
 
	$w .= '<tr valign="top"><td nowrap>Text</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_ad_text',$theData['poprox']['ad_text']).'&nbsp;</td><td>'.$theData['ad_text'].'</td></tr>'."\n";
	
	if (!empty($theData['extracted_info'])) {
		//print($v->debugPrint($v->debugStr($theData['extracted_info'])));
		foreach ($theData['extracted_info'] as $theInfoByWhom => $theInfoSet) if (!empty($theInfoSet)) {
			$w .= '<tr valign="bottom"><th colspan="3" align="left"><br />Extracted Information by '.$theInfoByWhom.'</th></tr>';
			foreach($theInfoSet as $theInfoLabel => $theInfoValue) if (!empty($theInfoValue)) {
				$w .= '<tr valign="center">';
				$w .= '<td nowrap>'.$theInfoLabel.'</td>';
				//TODO: change these checkboxes to be real
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
	$w .= $theJumpForm;
	$w .= "<br />\n";
	$w .= "<br />\n";
	$w .= 'Nothing found.';
}
$w .= str_repeat('<br />',8);

print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->result,'<br>'))));
$recite->includeMyFooter();

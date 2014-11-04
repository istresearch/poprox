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
	$w .= '<tr valign="top"><td nowrap>URL</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'url',$theData['poprox']['url']).'&nbsp;</td><td>'.$theData['url'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Email</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'email',$theData['poprox']['email']).'&nbsp;</td><td>'.$theData['email'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>License</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'license',$theData['poprox']['license']).'&nbsp;</td><td>'.$theData['license'].'</td></tr>'."\n";

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

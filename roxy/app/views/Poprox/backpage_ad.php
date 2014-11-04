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
$w .= '<h2>PopRox: Backpage Ad</h2>'."<br />\n";

$theAction = $v->getMyURL('jumptoad/backpage');
$theJumpForm = '<form method="post" action="'.$theAction.'">'."\n";
$theJumpForm .= 'Roxy ID: '.Widgets::createInputBox('jumpto_roxy_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_id', 'Jump To Roxy ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
$theJumpForm .= "<br />\n";
$theJumpForm .= 'Region: '.Widgets::createInputBox('jumpto_ad_region','text','',false,20).'.backpage.com';
$theJumpForm .= "<br />\n";
$theJumpForm .= 'Backpage ID: '.Widgets::createInputBox('jumpto_ad_id','text','',false,10);
$theJumpForm .= ' '.Widgets::createSubmitButton('poprox_jumpto_ad', 'Jump To Ad ID', 'poprox-jumpto');
$theJumpForm .= "<br />\n";
$theJumpForm .= '</form>';

$w .= $v->renderMyUserMsgsAsString();

$theData = $v->result;
if (!empty($theData)) {
	$theAction = $v->getSiteURL('/poprox/backpage/score');
	$w .= '<form method="post" action="'.$theAction.'">'."\n";
	$w .= '<input type="hidden" name="ad_id" value="'.$theData['id'].'" />'."\n";
	$w .= '<input type="hidden" name="redirect" value="'.$v->getSiteURL('/poprox/backpage/ads#poprox_score').'" />'."\n";
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
	$theData['location'] = $v->safeTextify($theData['location']);
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
	$w .= $v->getSiteURL('poprox','backpage','ads',$theData['id']);
	$w .= '">'.$theData['id'].'</a></td>';
	$w .= "</tr>\n";
	$w .= '</table>';
	//$w .= "<br />\n";
	$w .= '<hr />';
	
	$w .= '<table cellspacing="0" cellpadding="0">';
	$w .= '<tr valign="top"><td nowrap>Location</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_location',$theData['poprox']['location']).'&nbsp;</td><td>'.$theData['location'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Region</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_region',$theData['poprox']['region']).'&nbsp;</td><td>'.$theData['region'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Age</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_age',$theData['poprox']['age']).'&nbsp;</td><td>'.$theData['age'].'</td></tr>'."\n";
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
	$w .= $theJumpForm;
	$w .= "<br />\n";
	$w .= "<br />\n";
	$w .= 'Nothing found.';
}
$w .= str_repeat('<br />',8);

print($w);
//print($v->debugPrint(str_replace(' ','.&nbsp;.',$v->debugStr($v->result,'<br>'))));
$recite->includeMyFooter();

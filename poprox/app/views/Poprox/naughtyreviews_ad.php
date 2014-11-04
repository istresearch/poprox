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
	
	$theData['post_time'] = $v->safeTextify($theData['updated']);
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
	$w .= '<tr valign="top"><td nowrap>City2</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'city2',$theData['poprox']['city2']).'&nbsp;</td><td>'.$theData['city2'].'</td></tr>'."\n";
    $w .= '<tr valign="top"><td nowrap>Region</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'region',$theData['poprox']['region']).'&nbsp;</td><td>'.$theData['region'].'</td></tr>'."\n";
    $w .= '<tr valign="top"><td nowrap>Region2</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'region2',$theData['poprox']['region2']).'&nbsp;</td><td>'.$theData['region2'].'</td></tr>'."\n";
    $w .= '<tr valign="top"><td nowrap>Street</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'street',$theData['poprox']['street']).'&nbsp;</td><td>'.$theData['street'].'</td></tr>'."\n";
    $w .= '<tr valign="top"><td nowrap>Zip</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'zip',$theData['poprox']['zip']).'&nbsp;</td><td>'.$theData['zip'].'</td></tr>'."\n";
    $w .= '<tr valign="top"><td nowrap>Country</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'country',$theData['poprox']['country']).'&nbsp;</td><td>'.$theData['country'].'</td></tr>'."\n";
    $w .= '<tr valign="top"><td nowrap>URL</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'url',$theData['poprox']['url']).'&nbsp;</td><td>'.$theData['url'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Overall Rating</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'overallrating',$theData['poprox']['overallrating']).'&nbsp;</td><td>'.$theData['overallrating'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Performance Rating</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'performancerating',$theData['poprox']['performancerating']).'&nbsp;</td><td>'.$theData['performancerating'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Appearance Rating</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'appearancerating',$theData['poprox']['appearancerating']).'&nbsp;</td><td>'.$theData['appearancerating'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Atmosphere Rating</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'atmosphererating',$theData['poprox']['atmosphererating']).'&nbsp;</td><td>'.$theData['atmosphererating'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Daytime</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'daytime',$theData['poprox']['daytime']).'&nbsp;</td><td>'.$theData['daytime'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Nighttime</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'nighttime',$theData['poprox']['nighttime']).'&nbsp;</td><td>'.$theData['nighttime'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Sunday</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'sunday',$theData['poprox']['sunday']).'&nbsp;</td><td>'.$theData['sunday'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Monday</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'monday',$theData['poprox']['monday']).'&nbsp;</td><td>'.$theData['monday'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Tuesday</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'tuesday',$theData['poprox']['tuesday']).'&nbsp;</td><td>'.$theData['tuesday'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Wednesday</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'wednesday',$theData['poprox']['wednesday']).'&nbsp;</td><td>'.$theData['wednesday'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Thursday</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'thursday',$theData['poprox']['thursday']).'&nbsp;</td><td>'.$theData['thursday'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Friday</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'friday',$theData['poprox']['friday']).'&nbsp;</td><td>'.$theData['friday'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>30 min Rate</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'30minuterate',$theData['poprox']['30minuterate']).'&nbsp;</td><td>'.$theData['30minuterate'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Hour Rate</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'hourrate',$theData['poprox']['hourrate']).'&nbsp;</td><td>'.$theData['hourrate'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Credit Cards</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'creditcards',$theData['poprox']['creditcards']).'&nbsp;</td><td>'.$theData['creditcards'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>ID Verification</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'idverification',$theData['poprox']['idverification']).'&nbsp;</td><td>'.$theData['idverification'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Age</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'age',$theData['poprox']['age']).'&nbsp;</td><td>'.$theData['age'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Gender</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'gender',$theData['poprox']['gender']).'&nbsp;</td><td>'.$theData['gender'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Ethnicity</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'ethnicity',$theData['poprox']['ethnicity']).'&nbsp;</td><td>'.$theData['ethnicity'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Email</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'email',$theData['poprox']['email']).'&nbsp;</td><td>'.$theData['email'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Phone</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'phone',$theData['poprox']['phone']).'&nbsp;</td><td>'.$theData['phone'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Phone2</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'phone2',$theData['poprox']['phone2']).'&nbsp;</td><td>'.$theData['phone2'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Website</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'website',$theData['poprox']['website']).'&nbsp;</td><td>'.$theData['website'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Ad Website</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'adwebsite',$theData['poprox']['adwebsite']).'&nbsp;</td><td>'.$theData['adwebsite'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Ad Website2</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'adwebsite2',$theData['poprox']['adwebsite2']).'&nbsp;</td><td>'.$theData['adwebsite2'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Eyes</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'eyes',$theData['poprox']['eyes']).'&nbsp;</td><td>'.$theData['eyes'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Height</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'height',$theData['poprox']['height']).'&nbsp;</td><td>'.$theData['height'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Build</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'build',$theData['poprox']['build']).'&nbsp;</td><td>'.$theData['build'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Hair Type</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'hairtype',$theData['poprox']['hairtype']).'&nbsp;</td><td>'.$theData['hairtype'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Hair Color</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'haircolor',$theData['poprox']['haircolor']).'&nbsp;</td><td>'.$theData['haircolor'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Breast Size</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'breastsize',$theData['poprox']['breastsize']).'&nbsp;</td><td>'.$theData['breastsize'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Breast Cup</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'breastcup',$theData['poprox']['breastcup']).'&nbsp;</td><td>'.$theData['breastcup'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Implants</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'implants',$theData['poprox']['implants']).'&nbsp;</td><td>'.$theData['implants'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Smokes</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'smokes',$theData['poprox']['smokes']).'&nbsp;</td><td>'.$theData['smokes'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Piercings</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'piercings',$theData['poprox']['piercings']).'&nbsp;</td><td>'.$theData['piercings'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Tattoos</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'tattoos',$theData['poprox']['tattoos']).'&nbsp;</td><td>'.$theData['tattoos'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Travel</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'travel',$theData['poprox']['travel']).'&nbsp;</td><td>'.$theData['travel'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Languages</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'languages',$theData['poprox']['languages']).'&nbsp;</td><td>'.$theData['languages'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Grooming</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'grooming',$theData['poprox']['grooming']).'&nbsp;</td><td>'.$theData['grooming'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>DateCheck</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'datecheck',$theData['poprox']['datecheck']).'&nbsp;</td><td>'.$theData['datecheck'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Preferred411</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'preferred411',$theData['poprox']['preferred411']).'&nbsp;</td><td>'.$theData['preferred411'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Latitude</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'latitude',$theData['poprox']['latitude']).'&nbsp;</td><td>'.$theData['latitude'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Longitude</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'longitude',$theData['poprox']['longitude']).'&nbsp;</td><td>'.$theData['longitude'].'</td></tr>'."\n";
	$w .= '<tr valign="top"><td nowrap>Alias</td><td nowrap>:&nbsp;'.Widgets::createCheckBox('poprox_'.'alias',$theData['poprox']['alias']).'&nbsp;</td><td>'.$theData['alias'].'</td></tr>'."\n";

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

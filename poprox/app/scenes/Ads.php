<?php

namespace ISTResearch_Roxy\scenes;
use ISTResearch_Roxy\scenes\Poprox as BaseScene;
use com\blackmoonit\Arrays;
use com\blackmoonit\Strings;
use com\blackmoonit\Widgets;
{//namespace begin

class Ads extends BaseScene {

	protected function setupDefaults() {
		parent::setupDefaults();
	}
	
	/**
	 * Safe text and Diff'd with previous ad, if exists.
	 * @param string $aFieldName - the field name to get from the ad data.
	 * @param string $aDelimiter - explode the parameters based on this delimiter, defaults to "\n".
	 * @return string Returns the safe text run through Diff, maybe, and resulting HTML output.
	 */
	public function getSafeDiffField($aFieldName, $aDelimiter=' ') {
		if (empty($this->prev_ad_info))
			return $this->safeTextify($this->ad_info[$aFieldName],true);
		else
			return Widgets::diffLines($this->safeTextify($this->prev_ad_info[$aFieldName],true),
					$this->safeTextify($this->ad_info[$aFieldName],true),$aDelimiter);
	}
	
	/**
	 * Sometimes word changes are too much to render nicely, so treat lots of changes as one big change.
	 * @param string $aTextBlockOld - text to compare
	 * @param string $aTextBlockNew - text to compare against
	 * @return string Returns the HTML with appropriate diff tags (<del> and <ins>).
	 */
	public function diffTextBlock($aTextBlockOld, $aTextBlockNew) {
		if (empty($aTextBlockOld))
			return $aTextBlockNew;
		$theDiffSeparator = '';
		$theDiff = Widgets::computeDiff($aTextBlockOld,$aTextBlockNew,' ',' ');
		$numDel = 0;
		$numIns = 0;
		$numSame = 0;
		foreach ($theDiff['diff'] as $theDiffValue) {
			switch ($theDiffValue) {
				case -1:
					$numDel += 1;
					break;
				case 1:
					$numIns += 1;
					break;
				default:
					$numSame += 1;
			}//switch
		}
		if (($numSame*2)<($numDel+$numIns)) {
			//too many word diff, try diff as lines
			$theDiff = Widgets::computeDiff($aTextBlockOld, $aTextBlockNew, '<br />', '<br /');
			$theDiffSeparator = '<br />';
		}
		
		return Widgets::diffToHtml($theDiff, $theDiffSeparator);
	}
	
	/**
	 * Compare the Attribute with previous Ad, if exists.
	 * @param string $aAttrName - name of attribute.
	 * @param string $aAttrValue - value of said attribute.
	 */
	public function diffAdAttribute($aAttrName, $aAttrValue) {
		$theAttrValue = $this->safeTextify($aAttrValue,true);
		if (empty($this->prev_ad_info))
			return $theAttrValue;
		else {
			if (empty($this->prev_ad_info['attributes_diff']))
				$this->prev_ad_info['attributes_diff'] = array(); //cache of attributes used in diff
			if (!empty($this->prev_ad_info['attributes'])) {
				foreach ($this->prev_ad_info['attributes'] as $theAttrKey => $theAttrRow) {
					if ($theAttrRow['name']==$aAttrName && empty($this->prev_ad_info['attributes_diff'][$theAttrKey])) {
						$this->prev_ad_info['attributes_diff'][$theAttrKey] = 1;
						$theOldValue = $this->safeTextify($theAttrRow['value'],true);
						if ($aAttrValue!=$theOldValue) {
							$theDiff = Arrays::computeDiff(explode(' ',$theOldValue), explode(' ',$theAttrValue));
							return Widgets::diffToHtml($theDiff);
						} else {
							return $theAttrValue;
						}
					}//if
				}//foreach
				return '<ins>'.$theAttrValue.'</ins>';
			} else {
				return '<ins>'.$theAttrValue.'</ins>';
			}
		}//else
	}
	
	/**
	 * Create the revision links for an ad.
	 * @param array $aAdRevisionList - the list of ad revision to "page" through
	 * @param $aCurrRevisionIndex - the currently displayed ad index into the list
	 * @return string Returns the HTML used for the pager.
	 */
	public function getAdRevisionPagerHtml(array $aAdRevisionList, $aCurrRevisionIndex) {
		//NOTE: this method's logic copied from Pager ancestor method and is 1 based, the list is 0 based.
		$theAction = 'view/';
		$thePagerCount = 10; // The number of pages to display numerically
		$thePagerPageSize = 1;
		$theCurrPage = max($aCurrRevisionIndex+1,1);
		$theTotalCount = count($aAdRevisionList);
		$theTotalPages = ceil($theTotalCount/$thePagerPageSize);
		
		//print($aAction.' tc='.$theTotalCount.' cp='.$theCurrPage.' tp='.$theTotalPages.' pps='.$thePagerPageSize);
		
		if ($theTotalPages>1) {
			$theLabelSpacer = $this->getRes('pager/label_spacer');
			$thePager = '<span class="pager"> ';
			
			$thePager .= '<a href="'.$this->getMyUrl($theAction.$aAdRevisionList[0]).'"';
			if ($theCurrPage<=1) //hide, but still take up space
				$thePager .= ' class="invisible"';
			$thePager .= '>';
			if ($theSrc = $this->getRes('pager/imgsrc/pager_first')) {
				$thePager .= '<img src="'.$theSrc.'" class="pager" alt="'.$this->getRes('pager/label_first').'">';
			} else {
				$thePager .= $this->getRes('pager/label_first');
			}
			$thePager .= '</a>';

			$thePager .= $theLabelSpacer;
			
			$thePager .= '<a href="'.$this->getMyUrl($theAction.$aAdRevisionList[max($theCurrPage-2,0)]).'"';
			if ($theCurrPage<=1) //hide, but still take up space
				$thePager .= ' class="invisible"';
			$thePager .= '>';
			if ($theSrc = $this->getRes('pager/imgsrc/pager_previous')) {
				$thePager .= '<img src="'.$theSrc.'" class="pager" alt="'.$this->getRes('pager/label_previous').'">';
			} else {
				$thePager .= $this->getRes('pager/label_previous');
			}
			$thePager .= '</a>';

			if ($theCurrPage <= ($thePagerCount/2)) {
				$i_start = 1;
			} else {
				$i_start = $theCurrPage-($thePagerCount/2)+1;
			}
			$i_final = min($i_start+$thePagerCount-1,$theTotalPages);
			$i_start = max(1,$i_final-$thePagerCount+1);
			for ($i=$i_start; ($i <= $i_final); $i++) {
				//$thePager .= ($i==$i_start && $i_start>1)?' … ':$theLabelSpacer;
				$thePager .= $theLabelSpacer;
				if ($i != $theCurrPage) {
					//page logic is 1 based, array is 0 based.
					$thePager .= '<a href="'.$this->getMyUrl($theAction.$aAdRevisionList[$i-1]).'">&nbsp;'.($i-1).'&nbsp;</a>';
				} else {
					$thePager .= '<span class="current-page">('.($i-1).')</span>';
				}
				$final_i = $i;
			}
			//if ($final_i < $theTotalPages) {
			//	$thePager .= $theLabelSpacer.'… ';
			//}
			
			$thePager .= $theLabelSpacer;

			$thePager .= '<a href="'.$this->getMyUrl($theAction.$aAdRevisionList[min($theCurrPage,$theTotalPages-1)]).'"';
			if ($theCurrPage >= $theTotalPages) //hide, but still take up space
				$thePager .= ' class="invisible"';
			$thePager .= '>';
			if ($theSrc = $this->getRes('pager/imgsrc/pager_next')) {
				$thePager .= '<img src="'.$theSrc.'" class="pager" alt="'.$this->getRes('pager/label_next').'">';
			} else {
				$thePager .= $this->getRes('pager/label_next');
			}
			$thePager .= '</a>';
					
			$thePager .= $theLabelSpacer;
				
			$thePager .= '<a href="'.$this->getMyUrl($theAction.$aAdRevisionList[$theTotalPages-1]).'"';
			if ($theCurrPage >= $theTotalPages) //hide, but still take up space
				$thePager .= ' class="invisible"';
			$thePager .= '>';
			if ($theSrc = $this->getRes('pager/imgsrc/pager_last')) {
				$thePager .= '<img src="'.$theSrc.'" class="pager" alt="'.$this->getRes('pager/label_last').'">';
			} else {
				$thePager .= $this->getRes('pager/label_last');
			}
			$thePager .= '</a>';
				
			$thePager .= "</span>";
		} else {
			$thePager = '(this ad has no revisions)';
		}
	
		return $thePager;
	}
	
	/**
	 * External tool API for scoring may be employed.
	 * @param string $aText - the ad text to score.
	 * @return string Returns the score to display.
	 */
	public function getHTscore($aText) {
		if ($this->_config && !empty($this->_config['poprox/ht_classifier_score_enabled'])
				&& !empty($this->_config['poprox/ht_classifier_score_url']) && !empty($aText)) {
			$theCurl = curl_init($this->_config['poprox/ht_classifier_score_url']);
			curl_setopt($theCurl, CURLOPT_RETURNTRANSFER, true); //capture the response
			curl_setopt($theCurl, CURLOPT_HEADER, 0); //do not include headers in response
			curl_setopt($theCurl, CURLOPT_POSTFIELDS, $aText); //data to POST
			curl_setopt($theCurl, CURLOPT_HTTPHEADER, array( //additional headers to send
					'Content-Type: text',  //API requires content type of "text" only, not text/plain like traditional MIME
					'Content-Length: '.strlen($aText),  //byte count, not character count, so use strlen() here
			));
			$theResponse = curl_exec($theCurl);
			$theError = curl_error($theCurl);
			if (!empty($theError)) {
				$this->debugLog(__METHOD__.' url='.$this->_config['poprox/ht_classifier_score_url'].' err='.$this->debugStr($theError));
			}
			curl_close($theCurl);
			return $theResponse;
		}
	}

}//end class

}//end namespace

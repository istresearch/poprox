<?php
/*
 * Copyright (C) 2012 Blackmoon Info Tech Services
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ISTResearch_Roxy\scenes;
use BitsTheater\Scene;
{//namespace begin

class Poprox extends Scene {

	protected function setupDefaults() {
		parent::setupDefaults();
	}
	
	/**
	 * Convert all kinds of line breaks with a standard string.
	 * @param string $aText - the haystack text.
	 * @param string $aReplaceWith - replace a kinds of line endings with this, defaults to "\n".
	 * @return string Returns the $aText with all line breaks replaced with $aReplaceWith.
	 */
	public function replaceHtmlLineBreak($aText, $aReplaceWith="\n") {
		return preg_replace("/<br[^>]*>\s*\r*\n*/is", $aReplaceWith, $aText);
	}
	
	/**
	 * De-fang <a> tags by stripping them, but keeping the innerHtml text.
	 * @param string $aTextData - an html string.
	 * @param boolean $bKeepLineBreaks - removes all kinds of line breaks if FALSE (optional, default: false).
	 * @return string Returns the string with <a> tags stripped and optionally line breaks, too.
	 */
	public function safeTextify($aTextData, $bKeepLineBreaks=false) {
		$theResult = preg_replace('#<a.*?>([^<]*)</a>#i', '$1', $aTextData);
		if (!$bKeepLineBreaks) {
			$theResult = $this->replaceHtmlLineBreak($theResult,'');
		}
		return $theResult;
	}
	
	/**
	 * De-fang all harmful tags by using htmlentities() to convert them to plain text.
	 * @param string $aTextData - an html string.
	 * @param boolean $bKeepLineBreaks - if true, restores all kinds of line breaks with "&lt;br /&gt;" (optional, default: false).
	 * @return string Returns the string with many tags converted to plain text.
	 */
	public function safeTextifyAll($aTextData, $bKeepLineBreaks=false) {
		$theResult = htmlentities($this->replaceHtmlLineBreak($aTextData,'<br />'), ENT_QUOTES|ENT_SUBSTITUTE, "UTF-8");
		if ($bKeepLineBreaks) {
			//$theResult = str_ireplace(htmlentities('<br>', ENT_QUOTES, "UTF-8"),'<br />',$theResult);
			//$theResult = str_ireplace(htmlentities('<br/>', ENT_QUOTES, "UTF-8"),'<br />',$theResult);
			//previous 2 lines not needed since we changed all <br> tags to be exactly "<br />".
			$theResult = str_ireplace(htmlentities('<br />', ENT_QUOTES, "UTF-8"),'<br />',$theResult);
		}
		return $theResult;
	}
	
}//end class

}//end namespace


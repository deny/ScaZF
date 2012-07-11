<?php

/**
 * Trims text using UTF-8 encoding
 *
 * @author	Daniel Kózka
 */
class Sca_View_Helper_TrimText extends Zend_View_Helper_Abstract
{
	/**
	 * Helper function
	 *
	 * @param	string	$sText		text to trim
	 * @param	int		$iLength	result max lengthd
	 * @return	string
	 */
	public function trimText($sText, $iLength)
	{
		if($iLength <= 0)
		{
			return '';
		}

		$sText = html_entity_decode(strip_tags($sText), ENT_QUOTES, 'UTF-8');

		if($iLength >= mb_strlen($sText, 'UTF-8'))
		{
			return htmlentities($sText, ENT_QUOTES, 'UTF-8');
		}

		$sText = mb_substr($sText, 0, $iLength, 'UTF-8');
		return htmlentities($sText . '...', ENT_QUOTES, 'UTF-8');
	}
}
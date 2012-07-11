<?php

/**
 * Display form field errors
 *
 * @author	Daniel Kózka
 */
class Sca_View_Helper_FormFieldError extends Zend_View_Helper_Abstract
{
	/**
	 * Helper function
	 *
	 * @param	string	$sName	field name
	 * @return	string
	 */
	public function formFieldError($sName)
	{
		$sResult = '';

		if(isset($this->view->aErrors) && !empty($this->view->aErrors[$sName]))
		{
			$sResult .= '<ul class="error-list">';

			foreach($this->view->aErrors[$sName] as $sError)
			{
				$sResult .= '<li>'. $sError . '</li>';
			}
			
			$sResult .= '</ul>';
		}

		return $sResult;
	}
}
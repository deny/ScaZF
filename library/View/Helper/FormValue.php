<?php

/**
 * Display form field with errors and other options
 *
 * @author	Daniel Kózka
 */
class Sca_View_Helper_FormValue extends Zend_View_Helper_Abstract
{
	/**
	 * Helper function
	 *
	 * @param	string	$sName		field name
	 * @param	string	$sDefault	default value
	 * @return	string
	 */
	public function formValue($sName, $sDefault = '')
	{
		if(isset($this->view->aValues) && isset($this->view->aValues[$sName]))
		{
			return $this->view->aValues[$sName];
		}

		return $sDefault;
	}
}
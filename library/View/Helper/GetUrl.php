<?php

/**
 * Helper for Sca urls
 *
 *
 */
class Sca_View_Helper_GetUrl extends Zend_View_Helper_Abstract
{
	/**
	 * Returns current controller action
	 *
	 * @param	array		$aParams		parameters
	 * @param	string		$sAction		action
	 * @param	string		$sController	controller
	 * @return	string
	 */
	public function getUrl(array $aParams = null, $sAction = null, $sController = null)
	{
	// controller
		$sController = isset($sController) ? $sController : $this->view->sController;

	// action
		if(!isset($sAction))
		{
			$sAction = $this->view->sAction;
		}
		$sAction = '/'. $sAction;

	// params
		if(!isset($aParams))
		{
			$aParams = $this->view->aParams;
		}

		$sParams = '';
		foreach($aParams as $sKey => $sValue)
		{
			if($sValue !== null)
			{
				$sParams .= '/'. $sKey .'/'. $sValue;
			}
		}

	// url
		return '/'. $sController . $sAction . $sParams;
	}
}
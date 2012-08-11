<?php

/**
 * Display form field with errors and other options
 *
 * @author	Daniel Kózka
 */
class Sca_View_Helper_FormField extends Zend_View_Helper_Abstract
{
	protected $aDefault = [
		'fieldAttr' => [
			'class' => 'field'
		],
		'flipLabel' => false,
		'labelSep'	=> '<br />'
	];

	/**
	 * Helper function
	 *
	 * @param	string	$sLabel		field label
	 * @param	string	$sName		field name
	 * @param	string	$sField		form field
	 * @param	array	$aOptions	field options
	 * @return	string
	 */
	public function formField($sLabel, $sName, $sField, array $aOptions = [])
	{
		$aOpt = $this->getOptions($aOptions);

		$sResult = '<div'. $this->getAttrs($aOpt['fieldAttr']) . '>';

			$sHtmlLabel = '<label for="'. $sName .'">'. $sLabel . '</label>';

			if($aOpt['flipLabel'])
			{
				$sResult .= $sField . $sHtmlLabel;
			}
			else
			{
				$sResult .= $sHtmlLabel . $aOpt['labelSep']. $sField;
			}

			$sResult .= $this->view->formFieldError($sName);


		$sResult .= '</div>';

		return $sResult;
	}

	/**
	 * Merge default options with received
	 *
	 * @param	array	$aOptions	received options
	 * @return	array
	 */
	protected function getOptions($aOptions)
	{
		$aResult = [];
		foreach($this->aDefault as $sKey => $sValue)
		{
			$aResult[$sKey] = $this->aDefault[$sKey];

			$bArray = is_array($this->aDefault[$sKey]);
			if(array_key_exists($sKey, $aOptions))
			{
				if($bArray && is_array($aOptions[$sKey]))
				{
					foreach($sValue as $sSubKey => $sSubVal)
					{
						if(array_key_exists($sSubKey, $aOptions[$sKey]))
						{
							$aResult[$sKey][$sSubKey] = $aOptions[$sKey][$sSubKey];
						}
					}
				}
				elseif(!$bArray)
				{
					$aResult[$sKey] = $aOptions[$sKey];
				}
			}
		}

		return $aResult;
	}

	/**
	 * Return html attribs
	 *
	 * @param	array	$aAttr	array with attribs
	 * @return	string
	 */
	protected function getAttrs(array $aAttr)
	{
		$sResult = '';

		foreach($aAttr as $sAttr => $sValues)
		{
			if(!empty($sValues))
			{
				$sResult .= ' '. $sAttr . '="'. $sValues . '"';
			}
		}

		return $sResult;
	}
}
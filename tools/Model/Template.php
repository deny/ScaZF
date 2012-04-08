<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Model;

/**
 * Class for model tempalte parse and process
 *
 * @author	Daniel Kózka
 */
class Template
{
	/**
	 * Subtemplates
	 *
	 * @var	array
	 */
	protected $aSubTemplates = array();

	/**
	 * Constructor
	 *
	 * @param	string	$sFile	path to template file
	 * @return	\ScaZF\Tool\Model\Template
	 */
	public function __construct($sFile)
	{
		if(!file_exists($sFile))
		{
			throw new Exception('Template "'. $sFile . '" doesn\'t exists');
		}

		$sTemplate = file_get_contents($sFile);


		$iEnd = 0;
		while(($iStart = strpos($sTemplate, '{@', $iEnd)) !== false)
		{
			$iTmp = strpos($sTemplate, '@}', $iStart);
			$sTag = substr($sTemplate, $iStart + 8, $iTmp - $iStart - 8);
			$iEnd = strpos($sTemplate, '{@end='. $sTag .'@}', $iStart);

			$this->aSubTemplates[$sTag] =  ltrim(substr($sTemplate, $iTmp + 2, $iEnd - $iTmp - 2), "\n");

			$iEnd += 5;
		}
	}

	public function getSubTemplate($sName, array $aParams = [])
	{
		if(!isset($this->aSubTemplates[$sName]))
		{
			throw new Exception('Unknown template "'. $sName . '"');
		}

		$sTmp = $this->aSubTemplates[$sName];

		foreach($aParams as $sParam => $sValue)
		{
			$sTmp = str_replace('{*'. $sParam .'*}', $sValue, $sTmp);
		}

		return $sTmp;
	}
}
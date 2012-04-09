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
	 * Path to templates
	 *
	 * @var	string
	 */
	protected static $sTemplatePath = null;

	/**
	 * Loaded templates
	 *
	 * @var	array
	 */
	protected static $aTemplates = [];

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

		// template parsing
		$iEnd = 0;
		while(($iStart = strpos($sTemplate, '{@begin', $iEnd)) !== false) // find section begin - start
		{
			$iTmp = strpos($sTemplate, '@}', $iStart); // find section begin - end
			$sTag = substr($sTemplate, $iStart + 8, $iTmp - $iStart - 8); // get section name
			$iEnd = strpos($sTemplate, '{@end='. $sTag .'@}', $iStart); // find section end

			$this->aSubTemplates[$sTag] =  ltrim(substr($sTemplate, $iTmp + 2, $iEnd - $iTmp - 2), "\n");
			$iEnd += 5;
		}
	}

	/**
	 * Return subtemplate filled with values
	 *
	 * @param	string	$sName		template name
	 * @param	array	$aParams	params for template
	 * @return	string
	 */
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

// STATIC

	/**
	 * Set templates path
	 *
	 * @param	string	$sPath	tempaltes path
	 * @return	void
	 */
	public static function setTemplatesPath($sPath)
	{
		self::$sTemplatePath = $sPath;
	}

	/**
	 * Return template
	 *
	 * @param	string	$sName	template name
	 * @return	\ScaZF\Tool\Model\Template
	 */
	public static function getTemplate($sName)
	{
		if(!isset(self::$aTemplates[$sName]))
		{
			self::$aTemplates[$sName] = new self(
				self::$sTemplatePath . '/'. $sName . '.tpl'
			);
		}

		return self::$aTemplates[$sName];
	}
}
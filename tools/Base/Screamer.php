<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Base\Screamer;

/**
 * Screamer Trait
 *
 * @author Daniel Kózka
 */
class Screamer
{
	use \ScaZF\Tool\Base\Singleton;

	/**
	 * Screams text
	 *
	 * @param	string		$sText	scream text
	 * @param	int			$iLevel	scream level
	 * @return	void
	 */
	public function scream($sText, $iLevel = 0)
	{
		echo str_repeat("\t", $iLevel) . $sText . "\n";
	}

	/**
	 * Screams validation errors
	 *
	 * @param	\ScaZF\Tool\Validator\Schema 	$oValidator		schema validator
	 * @return	void
	 */
	public function screamErrors(\ScaZF\Tool\Validator\Schema $oValidator)
	{
		$aErrors = $oValidator->getMessages();

		echo "\n\nVALIDATION ERROR\n";

		foreach($aErrors as $sModel => $aErr)
		{
			foreach($aErr as $sField => $aTmp)
			{
				foreach($aTmp as $sErr)
				{
					echo '['. $sModel . ($sField == 'general' ? '' : ':'.$sField). "]\t". $sErr. "\n";
				}
			}
		}
	}
}
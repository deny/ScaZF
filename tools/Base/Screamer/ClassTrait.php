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
trait ClassTrait
{
	/**
	 * Screams text
	 *
	 * @param	string		$sText	scream text
	 * @param	int			$iLevel	scream level
	 * @return	void
	 */
	protected function scream($sText, $iLevel = 0)
	{
		Screamer::getInstance()->scream($sText, $iLevel);
	}
}
<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Base;

/**
 * Screamer Trait
 *
 * @author Daniel Kózka
 */
trait Screamer
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
		echo str_repeat("\t", $iLevel) . $sText . "\n";
	}
}
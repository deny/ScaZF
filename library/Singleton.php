<?php

/**
 * @namespace
 */
namespace Sca;

/**
 * Singleton
 *
 * @license	New BSD License
 * @author Daniel Kózka
 */
trait Singleton
{
	/**
	 * Singleton instance
	 */
	private static $oInstance = null;

	/**
	 * Singleton factory method
	 */
	public static function getInstance()
	{
		if(self::$oInstance === null)
		{
			self::$oInstance = new self();
		}

		return self::$oInstance;
	}
}

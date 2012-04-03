<?php

/**
 * @namespace
 */
namespace Sca\DataObject;

/**
 * Singleton for factory class
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
			self::$oInstance = new static();
			self::$oInstance->init();
		}

		return self::$oInstance;
	}
}

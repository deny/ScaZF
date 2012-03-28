<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Base;

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
	 *
	 * @var \ScaZF\Tool\Base\Singleton
	 */
	private static $oInstance = null;

	/**
	 * Singleton factory method
	 *
	 * @return	\ScaZF\Tool\Base\Singleton
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

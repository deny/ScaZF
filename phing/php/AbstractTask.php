<?php

require_once 'phing/Task.php';
require_once './tools/Generator.php';

/**
 * CRUD generate
 */
abstract class AbstractTask extends Task
{
	/**
	 * Generator object
	 *
	 * @var \ScaZF\Tool\Generator
	 */
	protected static $oGen = null;

	/**
	 * Package
	 *
	 * @var string
	 */
	protected static $sPackage = null;

	/**
	 * Model
	 *
	 * @var string|null
	 */
	protected static $sModel = null;
}

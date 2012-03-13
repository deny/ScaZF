<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Schema;

use ScaZF\Tool\Xml\Reader;

/**
 * Schema manager - parses model descriptions, creates Packages and Model information objects
 *
 * @author	Daniel Kózka
 */
class Manager
{
	/**
	 * Array for loaded packages objects
	 *
	 * @return	array
	 */
	protected $aPackages = array();

	/**
	 * Path to packages description files
	 *
	 * @var	string
	 */
	protected static $sSchemaPath = '.';

	/**
	 * Path to XML Schema
	 *
	 * @var	string
	 */
	protected static $sXsdPath = '.';

	/**
	 * Load information about package
	 *
	 * @param	string	$sName		package name
	 * @return	ScaZF\Tool\Schema\Package
	 */
	public function loadPackage($sName)
	{
		if(isset($this->aPackages[$sName])) // package already loaded
		{
			return $this->aPackages[$sName];
		}

	// check file
		$sFilePath = self::sSchemaPath .'/'. basename($sName) .'.xml';
		if(!file_exists($sFilePath))
		{
			throw new Exception('Description for package '. $sName . "doesn't exists");
		}

	// check XML Schema
		if(!Reader::schemaValidate($sFilePath, self::$sXsdPath))
		{
			throw new Exception('Wrong description for package '. $sName);
		}

	// parse schema and create objects
		$oReader = Reader();
		$oReader->open($sFilePath);

		$oPackage = $this->parsePackage($oReader);

		$oReader->close();

	// load other packages
		if($oPackage->hasConnections())
		{
			foreach($oPackage->getConnections() as $sPackage)
			{
				$this->loadPackage($sPackage);
			}
		}

		$this->aPackages[$sName] = $oPackage;
		return $oPackage;
	}


	protected function parsePackage(&$oReader)
	{

	}

	protected function parseModel(&$oReader)
	{

	}
}
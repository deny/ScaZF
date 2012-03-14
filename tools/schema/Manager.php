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
	protected $sSchemaPath = '.';

	/**
	 * Path to XML Schema
	 *
	 * @var	string
	 */
	protected $sXsdPath = '.';

	/**
	 * Constructor
	 *
	 * @param	string	$sSchemaPath	path to model description
	 * @param	string	$sXsdPath		path to XSD file
	 * @return	ScaZF\Tool\Schema\Manager
	 */
	public function __construct($sSchemaPath, $sXsdPath)
	{
		$this->sSchemaPath = $sSchemaPath;
		$this->sXsdPath = $sXsdPath;
	}

	/**
	 * Return loaded package
	 *
	 * @param	string	$sName	package name
	 * @return	ScaZF\Tool\Schema\Package
	 */
	public function getPackage($sName)
	{
		if(isset($this->aPackages[$sName])) // package already loaded
		{
			throw new Exception('Package "'. $sName .'"doesn\'t exists. Check USE attributes.');
		}

		return $this->aPackages[$sName];
	}

	/**
	 * Return loaded model
	 *
	 * @param	string	$sName	full model name (Package\Model)
	 * @return	ScaZF\Tool\Schema\Model
	 */
	public function getModel($sName)
	{
		list($sPackage, $sModel) = explode(':', $sName);

		if(isset($this->aPackages[$sPackage])) // package already loaded
		{
			throw new Exception('Package "'. $sPackage .'" doesn\'t exists. Check USE attributes.');
		}

		return $this->aPackages[$sPackage]->getModel($sModel);
	}

	/**
	 * Load information about package
	 *
	 * @param	string	$sName		package name
	 * @return	void
	 */
	public function loadPackage($sName)
	{
		if(isset($this->aPackages[$sName])) // package already loaded
		{
			return ;
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
	}

	/**
	 * Parse XML description and create Package object
	 *
	 * @param	ScaZF\Tool\Xml\Reader	$oReader	XMLReader object
	 * @return	ScaZF\Tool\Schema\Package
	 */
	protected function parsePackage($sPackage, &$oReader)
	{
		if(!$oReader->goToNode($sPackage))
		{
			throw new Exception('There is no package node in package "'. $sPackage . '" description');
		}

		$sName = $oReader->name;
		$sUses = $oReader->getAttribute('uses', '');

		$aModels = array();
		while($oReader->readNode())
		{
			$aModels[] = $this->parseModel($sPackage, $oReader);
		}

		return 	new Package($sName, $aModels, $sUses);
	}

	/**
	 * Parse XML description and create Model object
	 *
	 * @parma	string					$sPackage	model package
	 * @param	ScaZF\Tool\Xml\Reader	$oReader	XMLReader object
	 * @return	ScaZF\Tool\Schema\Model
	 */
	protected function parseModel($sPackage, &$oReader)
	{
		return new Model(
			$sPackage,
			$oReader->name,
			$oReader->getAttribute('extends'),
			$this->parseFields($oReader)
		);
	}

	/**
	 * Parse XML description and create Field objects
	 *
	 * @param	ScaZF\Tool\Xml\Reader	$oReader	XMLReader object
	 * @return	array
	 */
	protected function parseFields(XMLReader &$oReader)
	{
		$aFields = array();
		while(true)
		{
			$oReader->readNode();

			if($oReader->name != 'field')
			{
				break;
			}

			$aFields[] = new Field(
				$oReader->getAttribute('name'),
				$oReader->getAttribute('type'),
				$oReader->getAttribute('access'),
				$oReader->getAttribute('default'),
				$oReader->getAttribute('options'),
				$oReader->getAttribute('validate')
			);

		}

		return $aFields;
	}
}
<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Schema;

use \ScaZF\Tool\Xml\Reader;

/**
 * Schema manager - parses model descriptions, creates Packages and Model information objects
 *
 * @author	Daniel Kózka
 */
class Manager
{
	use \ScaZF\Tool\Base\Singleton;
	use \ScaZF\Tool\Base\Screamer\ClassTrait;

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
	 * Default Package
	 *
	 * @var	string
	 */
	protected $sDefaultPackage = '';

	/**
	 * Protected constructor
	 *
	 * @return	\ScaZF\Tool\Schema\Manager
	 */
	protected function __construct() {}

	/**
	 * Load schema from file (check XML and creates model descriptions)
	 *
	 * @param	string	$sSchemaPath	path to model description
	 * @param	string	$sXsdPath		path to XSD file
	 * @return	void
	 */
	public function init($sSchemaPath, $sXsdPath)
	{
		$this->sSchemaPath = $sSchemaPath;
		$this->sXsdPath = $sXsdPath;
	}

	/**
	 * Return loaded package
	 *
	 * @param	string	$sName	package name
	 * @return	\ScaZF\Tool\Schema\Package
	 */
	public function getPackage($sName)
	{
		if(!isset($this->aPackages[$sName])) // package already loaded
		{
			throw new Exception('Package "'. $sName .'" doesn\'t exists. Check USE attributes.');
		}

		return $this->aPackages[$sName];
	}

	/**
	 * Return loaded model
	 *
	 * @param	string	$sName	full model name (Package\Model)
	 * @return	\ScaZF\Tool\Schema\Model
	 */
	public function getModel($sName)
	{
		if(strpos($sName, ':') === false)
		{
			$sName = $this->sDefaultPackage .':'. $sName;
		}

		list($sPackage, $sModel) = explode(':', $sName);

		if(!isset($this->aPackages[$sPackage])) // package already loaded
		{
			throw new Exception('Package "'. $sPackage .'" doesn\'t exists. Check USE attributes.');
		}

		return $this->aPackages[$sPackage]->getModel($sModel);
	}

	/**
	 * Set default package name
	 *
	 * @param	string	$sName	pacakge name
	 * @return	void
	 */
	public function setDefaultPackage($sName)
	{
		$this->sDefaultPackage = $sName;
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

		$this->scream('Load "'. $sName .'" package');

		// check file
		$this->scream('Looking for file: '. basename($sName) .'.xml', 1);
			$sFilePath = $this->sSchemaPath .'/'. basename($sName) .'.xml';
			if(!file_exists($sFilePath))
			{
				throw new Exception('Description for package "'. $sName . '" doesn\'t exists');
			}

		// check XML Schema
		$this->scream('Validate XML structure', 1);
			//if(($sError = Reader::schemaValidate($sFilePath, $this->sXsdPath)) !== false)
			//{
			//	throw new Exception('Wrong description for package '. $sName . ': ' . $sError);
			//}

		// parse schema and create objects
			$oReader = new Reader();
			$oReader->open($sFilePath);

			$oPackage = $this->parsePackage($sName, $oReader);

			$oReader->close();

			$this->aPackages[$sName] = $oPackage;

		// load other packages
			if($oPackage->hasConnections())
			{
				foreach($oPackage->getConnections() as $sPackage)
				{
					$this->loadPackage($sPackage);
				}
			}
	}

	/**
	 * Parse XML description and create Package object
	 *
	 * @param	ScaZF\Tool\Xml\Reader	$oReader	XMLReader object
	 * @return	ScaZF\Tool\Schema\Package
	 */
	protected function parsePackage($sPackage, &$oReader)
	{
		$this->scream('Start parsing', 1);

		if(!$oReader->goToNode($sPackage))
		{
			throw new Exception('There is no package node in package "'. $sPackage . '" description');
		}


		$sName = $oReader->name;
		$sUses = $oReader->getAttribute('use', '');

		$aModels = array();
		if($oReader->readNode())
		{
			while($oReader->nodeType != \XMLReader::NONE)
			{
				$aModels[] = $this->parseModel($sPackage, $oReader);
			}
		}

		$this->scream('End parsing', 1);

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
		$this->scream('Parse model: '. $oReader->name, 2);
		return new Model(
			$sPackage,
			$oReader->name,
			$oReader->getAttribute('extends'),
			$oReader->getAttribute('alias', null),
			$oReader->getAttribute('componentof', null),
			$this->parseFields($oReader)
		);
	}

	/**
	 * Parse XML description and create Field objects
	 *
	 * @param	\ScaZF\Tool\Xml\Reader	$oReader	XMLReader object
	 * @return	array
	 */
	protected function parseFields(&$oReader)
	{
		$aFields = array();
		while(true)
		{
			$oReader->readNode();

			if($oReader->name != 'field')
			{
				break;
			}

			$this->scream('Parse field: '. $oReader->getAttribute('name', 'name not found'), 3);

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
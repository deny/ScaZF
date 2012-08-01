<?php

/**
 * @namespace
 */
namespace ScaZF\Tool;

require_once 'Base/Singleton.php';
require_once 'Base/Screamer.php';
require_once 'Base/Screamer/ClassTrait.php';
require_once 'Base/Exception.php';
require_once 'Base/Template.php';

require_once 'Schema/Exception.php';
require_once 'Schema/Field.php';
require_once 'Schema/Model.php';
require_once 'Schema/Package.php';
require_once 'Schema/Manager.php';

require_once 'Xml/Reader.php';

require_once 'Validator/ValidatorAbstract.php';
require_once 'Validator/Field/TypeInt.php';
require_once 'Validator/Field/TypeString.php';
require_once 'Validator/Field/TypeModel.php';
require_once 'Validator/Field/TypeEnum.php';
require_once 'Validator/Model.php';
require_once 'Validator/Schema.php';
require_once 'Validator/Field.php';

require_once 'Wrapper/Field.php';
require_once 'Wrapper/Model.php';

require_once 'Db/Generator.php';

require_once 'Model/Exception.php';
require_once 'Model/Generator.php';

require_once 'Crud/Generator.php';

class Generator
{
	use \ScaZF\Tool\Base\Singleton;
	use \ScaZF\Tool\Base\Screamer\ClassTrait;

// PATHS
	/**
	 * path to schema declaration
	 *
	 * @var string
	 */
	protected $sSchemaPath;

	/**
	 * Schema manager
	 *
	 * @var \ScaZF\Tool\Schema\Manager
	 */
	protected $oManager;

	/**
	 * Path to controllers/modules
	 *
	 * @var string
	 */
	protected $sCrudPath;

	/**
	 * Path to models
	 *
	 * @var string
	 */
	protected $sModelPath;

// FLAGS

	/**
	 * Is Validate Run
	 *
	 * @var bool
	 */
	protected $bValidate = false;

	/**
	 * Constructor
	 *
	 * @param	string	$sSchemaPath	path to XML schema
	 * @param	string	$sCrudPath		path to crud
	 * @param	string	$sModelPath		path to models
	 * @return 	Generator
	 */
	public function __construct($sSchemaPath, $sCrudPath, $sModelPath)
	{
		$this->sSchemaPath = $sSchemaPath;
		$this->sCrudPath = $sCrudPath;
		$this->sModelPath = $sModelPath;
	}

	/**
	 * Prepare data for generate
	 *
	 * @param	string	$sPackage	package to generate
	 * @return 	void
	 */
	protected function prepare($sPackage)
	{
		$this->oManager = \ScaZF\Tool\Schema\Manager::getInstance();
		$this->oManager->init($this->sSchemaPath);

		$this->oManager->setDefaultPackage($sPackage);
		$this->oManager->loadPackage($sPackage);
	}

	/**
	 * Validate package
	 *
	 * @param	string	$sPackage package name
	 * @return	bool
	 */
	public function validate($sPackage)
	{
		if($this->bValidate)
		{
			return true;
		}

		$oValidator = new \ScaZF\Tool\Validator\Schema();
		if(!$oValidator->isValid($oManager->getPackage($sPackage)))
		{
			\ScaZF\Tool\Base\Screamer\Screamer::getInstance()->screamErrors($oValidator);
			return false;
		}

		$this->bValidate = true;
		return true;
	}

	/**
	 * Generate CRUD
	 *
	 * @param	string	$sPackage	package name
	 * @param	string	$sModel		model name
	 * @return	void
	 */
	public function crud($sPackage, $sModel = null)
	{
		if($this->validate($sPackage))
		{
			$oGen = new \ScaZF\Tool\Crud\Generator();

			if(isset($sModel))
			{
				$aModels = array(
					$this->oManager->getPackage($sPackage)->getModel($sPackage)
				);
			}
			else
			{
				$aModels = $this->oManager->getPackage($sPackage)->getModels();
			}

			foreach($aModels as $oModel)
			{
				$oGen->getController($oModel, $Controller);
				$oGen->getViewList($oModel, $sController);
				$oGen->getViewForm($oModel, $sController);
			}
		}
	}
}
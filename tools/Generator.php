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
	 * Path to sql files
	 *
	 * @var string
	 */
	protected $sSqlPath;

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
	 * Is prepare run
	 *
	 * @var	bool
	 */
	protected $bPrepare = false;

	/**
	 * Is Validate Run
	 *
	 * @var bool
	 */
	protected $bValidate = false;

	/**
	 * Is schema valid
	 *
	 * @var	bool
	 */
	protected $bValid = false;

	/**
	 * Constructor
	 *
	 * @param	string	$sSchemaPath	path to XML schema
	 * @param	string	$sSqlPath		path to sql files
	 * @param	string	$sModelPath		path to models
	 * @param	string	$sCrudPath		path to crud
	 * @return 	Generator
	 */
	public function __construct($sSchemaPath, $sSqlPath, $sModelPath, $sCrudPath)
	{
		$this->sSchemaPath = $sSchemaPath;
		$this->sSqlPath = $sSqlPath;
		$this->sModelPath = $sModelPath;
		$this->sCrudPath = $sCrudPath;

		\ScaZF\Tool\Base\Template::setTemplatesPath(realpath(dirname(__FILE__) . '/../templates'));
	}

	/**
	 * Prepare data for generate
	 *
	 * @param	string	$sPackage	package to generate
	 * @return 	void
	 */
	protected function prepare($sPackage)
	{
		if(!$this->bPrepare)
		{
			$this->scream('Prepare data' . "\n");

			$this->oManager = \ScaZF\Tool\Schema\Manager::getInstance();
			$this->oManager->init($this->sSchemaPath);
			$this->oManager->setDefaultPackage($sPackage);
			$this->oManager->loadPackage($sPackage);
			$this->bPrepare = true;
		}
	}

	/**
	 * Validate package
	 *
	 * @param	string	$sPackage package name
	 * @return	bool
	 */
	public function validate($sPackage)
	{
		$this->prepare($sPackage);
		if($this->bValidate)
		{
			return $this->bValid;
		}

		$this->scream('Validate data' . "\n");

		$oValidator = new \ScaZF\Tool\Validator\Schema();
		if($oValidator->isValid($this->oManager->getPackage($sPackage)))
		{
			$this->bValid = true;
		}
		else
		{
			\ScaZF\Tool\Base\Screamer\Screamer::getInstance()->screamErrors($oValidator);
			$this->bValid = false;
		}

		$this->bValidate = true;
		return $this->bValid;
	}

	/**
	 * Generate SQL
	 *
	 * @param	string	$sPackage	package name
	 * @return	void
	 */
	public function sql($sPackage)
	{
		$this->prepare($sPackage);
		if($this->validate($sPackage))
		{
			$this->scream('SQL Generate' . "\n");

			try
			{
				$this->checkDir($this->sSqlPath);
				$sFile = $this->sSqlPath .'/'. $sPackage .'.sql';

				$oGen = new \ScaZF\Tool\Db\Generator();
				file_put_contents($sFile, $oGen->getSql($this->oManager->getPackage($sPackage)));
			}
			catch(\ScaZF\Tool\Schema\Exception $oExc)
			{
				echo "\n\nERROR:\n". $oExc->getMessage() ."\n\n";
				echo $oExc->getTraceAsString() . "\n\n";
			}
		}
	}

	/**
	 * Generate Model Base
	 *
	 * @param	string	$sPackage	package name
	 * @param	string	$sModel		model name
	 * @return	void
	 */
	public function modelBase($sPackage, $sModel = null)
	{
		$this->prepare($sPackage);
		if($this->validate($sPackage))
		{
			$this->scream('Model Base generate' . "\n");

			try
			{
				$sPath = $this->sModelPath .'/'. $sPackage .'/Base';
				$this->checkDir($sPath);

				$oGen = new \ScaZF\Tool\Model\Generator();

				if(isset($sModel))
				{
					$aModels = array(
						$this->oManager->getPackage($sPackage)->getModel($sModel)
					);
				}
				else
				{
					$aModels = $this->oManager->getPackage($sPackage)->getModels();
				}

				foreach($aModels as $oModel)
				{

					// BASE model
						file_put_contents($sPath .'/'. $oModel->getName() . '.php', $oGen->getModelBase($oModel));

					// BASE factory
						file_put_contents($sPath .'/'. $oModel->getName() . 'Factory.php', $oGen->getFactoryBase($oModel));
				}
			}
			catch(\ScaZF\Tool\Schema\Exception $oExc)
			{
				echo "\n\nERROR:\n". $oExc->getMessage() ."\n\n";
				echo $oExc->getTraceAsString() . "\n\n";
			}
		}
	}

	/**
	 * Generate Model
	 *
	 * @param	string	$sPackage	package name
	 * @param	string	$sModel		model name
	 * @return	void
	 */
	public function model($sPackage, $sModel = null)
	{
		$this->prepare($sPackage);
		if($this->validate($sPackage))
		{
			try
			{
				$this->scream('Model generate' . "\n");

				$sPath = $this->sModelPath .'/'. $sPackage;
				$this->checkDir($sPath);

				$oGen = new \ScaZF\Tool\Model\Generator();

				if(isset($sModel))
				{
					$aModels = array(
						$this->oManager->getPackage($sPackage)->getModel($sModel)
					);
				}
				else
				{
					$aModels = $this->oManager->getPackage($sPackage)->getModels();
				}

				foreach($aModels as $oModel)
				{
					// model
						file_put_contents($sPath .'/'. $oModel->getName() . '.php', $oGen->getModel($oModel));

					// factory
						file_put_contents($sPath .'/'. $oModel->getName() . 'Factory.php', $oGen->getFactory($oModel));
				}
			}
			catch(\ScaZF\Tool\Schema\Exception $oExc)
			{
				echo "\n\nERROR:\n". $oExc->getMessage() ."\n\n";
				echo $oExc->getTraceAsString() . "\n\n";
			}
		}
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
		$this->prepare($sPackage);
		if($this->validate($sPackage))
		{
			$this->scream('Crud generate' . "\n");

			try
			{
				$oGen = new \ScaZF\Tool\Crud\Generator();

				if(isset($sModel))
				{
					$aModels = array(
						$this->oManager->getPackage($sPackage)->getModel($sModel)
					);
				}
				else
				{
					$aModels = $this->oManager->getPackage($sPackage)->getModels();
				}

				$fHandle = fopen ("php://stdin","r");
				$sPath = $this->sCrudPath;

				echo  'Module (empty): ';

				$sModule = trim(fgets($fHandle));

				if(!empty($sModule))
				{
					$sPath .= '/'. $sModule;
				}
				$this->checkDir($sPath);

				$sPathC = $sPath . '/controllers';
				$this->checkDir($sPathC);

				$sPathV = $sPath . '/views/scripts';
				$this->checkDir($sPathV);

				foreach($aModels as $oModel)
				{
					echo 'Controller name for '. $oModel->getName() . ': ';
					$sController = ucfirst(trim(fgets($fHandle)));

					$sFileC = $sPathC .'/'. $sController . 'Controller.php';
					$sFileV = $sPathV . '/'. strtolower($sController);

					$this->checkDir($sFileV);

					file_put_contents($sFileC, $oGen->getController($oModel, $sController, $sModule));
					file_put_contents($sFileV .'/list.phtml', $oGen->getViewList($oModel, $sController, $sModule));
					file_put_contents($sFileV . '/form.phtml', $oGen->getViewForm($oModel, $sController, $sModule));
				}
			}
			catch(\ScaZF\Tool\Schema\Exception $oExc)
			{
				echo "\n\nERROR:\n". $oExc->getMessage() ."\n\n";
				echo $oExc->getTraceAsString() . "\n\n";
			}
		}
	}

	/**
	 * Check path and create folders if needed
	 *
	 * @param	string	$sPath path to test
	 * @return	void
	 */
	protected function checkDir(&$sPath)
	{
		if(!file_exists($sPath))
		{
			mkdir($sPath, 0755, true);
		}

		$sPath = realpath($sPath);
	}
}
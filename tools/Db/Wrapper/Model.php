<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Db\Wrapper;

/**
 * Model wrapper
 *
 * @author Daniel Kózka
 */
class Model
{
	/**
	 * Model definition
	 *
	 * @var	\ScaZF\Tool\Schema\Model
	 */
	protected $oModel;

	/**
	 * Model alias
	 *
	 * @var	string
	 */
	protected $sAlias = null;

	/**
	 * Sql table name
	 *
	 * @var	string
	 */
	protected $sTableName = null;

	/**
	 * Key definition
	 *
	 * @var string
	 */
	protected $sKey = null;

	/**
	 * Constructor
	 *
	 * @param	\ScaZF\Tool\Schema\Model	$oModel		model definition
	 * @return	\ScaZF\Tool\Db\Wrapper\Model
	 */
	public function __construct(\ScaZF\Tool\Schema\Model $oModel)
	{
		$this->oModel = $oModel;
	}

	/**
	 * Return table anme
	 *
	 * @return	string
	 */
	public function getTableName()
	{
		if($this->sTableName === null)
		{
			if(!$this->oModel->hasExtends() && !$this->oModel->isComponent())
			{
				$this->sTableName = $this->oModel->getName();
			}
			elseif($this->oModel->hasExtends())
			{
				$oExtend = new self(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->oModel->getExtends())
				);

				$this->sTableName = $oExtend->getTableName() . '_e_'. $this->oModel->getName();
			}
			else
			{
				$oComponent = new self(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->oModel->getComponent())
				);
				$this->sTableName = $oComponent->getTableName() . '_c_'. $this->oModel->getName();
			}

			$this->sTableName = strtolower($this->sTableName);
		}

		return $this->sTableName;
	}

	/**
	 * Return model alias
	 *
	 * @return	string
	 */
	public function getAlias()
	{
		if($this->sAlias === null)
		{
			if($this->oModel->getAlias() === null)
			{
				$aTmp = explode('_', $this->getTableName());

				$this->sAlias = '';
				foreach($aTmp as $sPart)
				{
					$this->sAlias .= $sPart[0];
				}
			}
			else
			{
				$this->sAlias = $this->oModel->getAlias();
			}

			$this->sAlias = strtolower($this->sAlias);
		}


		return $this->sAlias;
	}

	/**
	 * Return table key
	 *
	 * @return	string
	 */
	public function getKey()
	{
		if($this->sKey === null)
		{
			if($this->oModel->hasExtends())
			{
				$oExtend = new self(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->oModel->getExtends())
				);
				$this->sKey = $oExtend->getKey();
			}
			else
			{
				$this->sKey = $this->getAlias() .'_id';
			}

		}

		return $this->sKey;
	}

	/**
	 * Check if model has primary key
	 *
	 * @return	bool
	 */
	public function hasPrimaryKey()
	{
		return !$this->oModel->hasExtends() && !$this->oModel->isComponent();
	}

	/**
	* Model methods call
	*
	* @param	string	$sName	method name
	* @param 	array	$aArgs 	method arguments
	* @return 	mixed
	*/
	public function __call($sName, $aArgs)
	{
		return call_user_func_array(
			array($this->oModel, $sName),
			$aArgs
		);
	}
}
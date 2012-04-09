<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Wrapper;

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
	 * @return	\ScaZF\Tool\Wrapper\Model
	 */
	public function __construct(\ScaZF\Tool\Schema\Model $oModel)
	{
		$this->oModel = $oModel;
	}

	/**
	 * Return description for SQL generator
	 *
	 * @return	array
	 */
	public function getDescription()
	{
		// prepare result
		$aResult = [
			'name'			=> $this->getTableName(),
			'fields'		=> [],
			'other'			=> [],
			'techTables'	=> [],
			'foreignKeys'	=> []
		];

		// add key field
		$aResult['fields'][] = [
			'name' 		=> $this->getKey(),
			'type'		=> 'INT(10)',
			'other'		=> 'UNSIGNED' . ($this->hasPrimaryKey() ? ' NOT NULL AUTO_INCREMENT' : '')
		];

		if($this->hasPrimaryKey()) // if main table with autoincrement primary
		{
			$aResult['other'][] = 'PRIMARY KEY(`'. $this->getKey() .'`)';
		}
		elseif($this->hasExtends()) // if model extend other model
		{
			$oTmp = new self(
				\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->getExtends())
			);

			$aResult['foreignKeys'][$this->getTableName()] = [
				$this->getKey()	=> ['table' => $oTmp->getTableName(), 'field' => $oTmp->getKey()],
			];

			$aResult['other'][] = 'PRIMARY KEY(`'. $oTmp->getKey() .'`)';
		}
		elseif($this->isComponent()) // if model is component
		{
			$oTmp = new \ScaZF\Tool\Wrapper\Model(
				\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->getComponent())
			);

			$aResult['foreignKeys'][$this->getTableName()] = [
				$this->getKey()	=> ['table' => $oTmp->getTableName(), 'field' => $oTmp->getKey()]
			];

			$aResult['other'][] = 'PRIMARY KEY(`'. $this->getKey() .'`)';
		}

		// get description for all fields
		foreach($this->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($this, $oField);
			$aDesc = $oField->getDescription();

		// if description for field extists
			if(isset($aDesc['field']))
			{
				$aResult['fields'][] = $aDesc['field'];
			}

		// if field need technical table
			if(isset($aDesc['techTable']))
			{
				$aResult['techTables'][] = $aDesc['techTable'];
			}

		// if field need FK
			if(isset($aDesc['foreignKeys']))
			{
				// merge  current FK description
				foreach($aDesc['foreignKeys'] as $sTable => $aKeys)
				{
					//var_dump($aKeys);
					if(!isset($aResult['foreignKeys'][$sTable]))
					{
						$aResult['foreignKeys'][$sTable] = [];
					}

					foreach($aKeys as $sField => $aInfo)
					{
						$aResult['foreignKeys'][$sTable][$sField] = $aInfo;
					}
				}
			}
		}

		return $aResult;
	}

	/**
	 * Calcualte table name
	 *
	 * @return	string
	 */
	public function getTableName()
	{
		if($this->sTableName === null)
		{
			if(!$this->oModel->hasExtends() && !$this->oModel->isComponent()) // simple model
			{
				$this->sTableName = $this->oModel->getName();
			}
			elseif($this->oModel->hasExtends()) // model extends something
			{
				$oExtend = new self(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->oModel->getExtends())
				);

				$this->sTableName = $oExtend->getTableName() . '_e_'. $this->oModel->getName();
			}
			else // model is component
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
	 * Return fields ids
	 *
	 * @return	array
	 */
	public function getFieldsIds()
	{
		$aResult = [];
		foreach($this->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($this, $oField);
			$aResult[] = $oField->getId();
		}
		return $aResult;
	}

	/**
	 * Return all fields (first from parent, then additional field)
	 *
	 * @return	array
	 */
	public function getAllFields()
	{
		$aResult = [];
		if($this->hasExtends())
		{
			$oParent = new self(
				\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->getExtends())
			);

			$aResult = $oParent->getAllFields();
		}

		$aResult = array_merge($aResult, $this->getFields());

		return $aResult;
	}

	/**
	 * Calcualte model alias
	 *
	 * @return	string
	 */
	public function getAlias()
	{
		if($this->sAlias === null)
		{
			if($this->oModel->getAlias() === null)
			{
				// get first letters of table name parts
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
	 * Calculate table primary key
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
	 * Check if model has component field
	 *
	 * @return	bool
	 */
	public function hasComponentField()
	{
		$bResult = false;
		foreach($this->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($this, $oField);

			if($oField->isModelType() && $oField->isComponent())
			{
				$bResult = true;
				break;
			}
		}

		return $bResult;
	}

	/**
	 * Check if model has one-to-many field
	 *
	 * @return	bool
	 */
	public function hasOneToManyField()
	{
		$bResult = false;
		foreach($this->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($this, $oField);

			if($oField->isModelType() && $oField->isOneToMany())
			{
				$bResult = true;
				break;
			}
		}

		return $bResult;
	}

	/**
	 * Check if model has object field
	 *
	 * @return	bool
	 */
	public function hasObjectField()
	{
		$bResult = false;
		foreach($this->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($this, $oField);

			if($oField->isModelType() && !$oField->isComponent() && !$oField->isOneToMany())
			{
				$bResult = true;
				break;
			}
		}

		return $bResult;
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
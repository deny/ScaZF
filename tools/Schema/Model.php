<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Schema;

/**
 * Model description object
 *
 * @author	Daniel Kózka
 */
class Model
{
	/**
	 * Model name
	 *
	 * @var	string
	 */
	protected $sName;

	/**
	 * Model package name
	 *
	 * @var	string
	 */
	protected $sPackage;

	/**
	 * Extended model name
	 *
	 * @var	string
	 */
	protected $sExtends = null;

	/**
	 * Is component of class
	 *
	 * @var	string
	 */
	protected $sComponentOf = null;

	/**
	 * Model fields
	 *
	 * @var	array
	 */
	protected $aFields;

	/**
	 * Model alias
	 *
	 * @var	string
	 */
	protected $sAlias;

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
	 * @param	string		$sPackage	package name
	 * @param	string		$sName		model name
	 * @param	string		$sExtends	extended model
	 * @param	array		$aFields	model fields
	 * @return	\ScaZF\Tool\Schema\Model
	 */
	public function __construct($sPackage, $sName, $sExtends, $sAlias, $sComponent, array $aFields)
	{
		$this->sName = $sName;
		$this->sPackage = $sPackage;
		$this->sExtends = $sExtends;
		$this->sAlias = $sAlias;
		$this->sComponentOf = $sComponent;
		$this->aFields = $aFields;
	}

// GETTERS

	/**
	 * Return model full name (with package name)
	 *
	 * @return	string
	 */
	public function getFullName()
	{
		return $this->sPackage .':'. $this->sName;
	}

	/**
	 * Return model name
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->sName;
	}

	/**
	 * Return component info
	 *
	 * @return	string
	 */
	public function getComponent()
	{
		return $this->sComponentOf;
	}

	/**
	 * Return model fields
	 *
	 * @return	array
	 */
	public function getFields()
	{
		return $this->aFields;
	}

	/**
	 * Return extended model name
	 *
	 * @return	string
	 */
	public function getExtends()
	{
		return $this->sExtends;
	}

	/**
	 * Check if has extended model name
	 *
	 * @return	bool
	 */
	public function hasExtends()
	{
		return !empty($this->sExtends);
	}

// SQL

	/**
	 * Return table anme
	 *
	 * @return	string
	 */
	public function getTableName()
	{
		if($this->sTableName === null)
		{
			if(empty($this->sExtends) && empty($this->sComponentOf))
			{
				$this->sTableName = $this->sName;
			}
			elseif(!empty($this->sExtends))
			{
				$oExtend = Manager::getInstance()->getModel($this->sExtends);
				$this->sTableName = $oExtend->getTableName() . '_e_'. $this->sName;
			}
			else
			{
				$oComponent = Manager::getInstance()->getModel($this->sComponentOf);
				$this->sTableName = $oComponent->getTableName() . '_c_'. $this->sName;
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
			$aTmp = explode('_', $this->getTableName());
			$this->sAlias = '';
			foreach($aTmp as $sPart)
			{
				$this->sAlias .= $sPart[0];
			}
		}

		return strtolower($this->sAlias);
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
			if(empty($this->sExtends))
			{
				$this->sKey = $this->getAlias() .'_id';
			}
			else
			{
				$oExtend = Manager::getInstance()->getModel($this->sExtends);
				$this->sKey = $oExtend->getKey();
			}
		}

		return $this->sKey;
	}

	public function isPrimary()
	{
		return empty($this->sExtends) && empty($this->sComponentOf);
	}
}
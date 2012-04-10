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
	 * Return model alias
	 *
	 * @return	string
	 */
	public function getAlias()
	{
		return $this->sAlias;
	}

	/**
	 * Return package name
	 *
	 * @return	string
	 */
	public function getPackage()
	{
		return $this->sPackage;
	}

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
	public function getExtends($bAddPackage = false)
	{
		if($bAddPackage && strpos($this->sExtends, ':') === false)
		{
			return $this->sPackage .':'. $this->sExtends;
		}

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

	/**
	 * Check if model is component
	 *
	 * @return	bool
	 */
	public function isComponent()
	{
		return !empty($this->sComponentOf);
	}
}
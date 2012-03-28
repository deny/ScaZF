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
	protected $sExtends;

	/**
	 * Model fields
	 *
	 * @var	array
	 */
	protected $aFields;

	/**
	 * Constructor
	 *
	 * @param	string		$sPackage	package name
	 * @param	string		$sName		model name
	 * @param	string		$sExtends	extended model
	 * @param	array		$aFields	model fields
	 * @return	\ScaZF\Tool\Schema\Model
	 */
	public function __construct($sPackage, $sName, $sExtends, array $aFields)
	{
		$this->sName = $sName;
		$this->sPackage = $sPackage;
		$this->sExtends = $sExtends;
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
}
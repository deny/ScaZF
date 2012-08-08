<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Schema;

/**
 * Field description object
 *
 * @author	Daniel Kózka
 */
class Field
{
	/**
	 * Field name
	 *
	 * @var	string
	 */
	protected $sName;

	/**
	 * Field type
	 *
	 * @var	string
	 */
	protected $sType;

	/**
	 * Field type attributes
	 *
	 * @var	array
	 */
	protected $aTypeAttr;

	/**
	 * Field access definition
	 *
	 * @var	array
	 */
	protected $aAccess;

	/**
	 * Defaul value
	 *
	 * @var	string
	 */
	protected $sDefault;

	/**
	 * Constructor
	 *
	 * @param	string		$sName			field name
	 * @param	string		$sType			field type
	 * @param	string		$sAccess		access definition
	 * @param	string		$sDefault		default value
	 * @return	\ScaZF\Tool\Schema\Field
	 */
	public function __construct($sName, $sType, $sAccess, $sDefault)
	{
		$aMatches = null;
		preg_match('/^([a-zA-Z:]+)(\([0-9a-zA-Z, \*]+\))?/', $sType, $aMatches);

		$this->sName = $sName;
		$this->sPackage = $aMatches[0];
		$this->sType = $aMatches[1];
		$this->aTypeAttr = empty($aMatches[2]) ? array() : explode(',', trim($aMatches[2],'()'));

		$this->aAccess = empty($sAccess) ? array('get','set') : explode(',', $sAccess);
		$this->sDefault = $sDefault;
	}

// GETTERS

	/**
	 * Return field access info
	 *
	 * @return	array
	 */
	public function getAccess()
	{
		return $this->aAccess;
	}

	/**
	 * Return field Default value
	 *
	 * @return	string
	 */
	public function getDefault()
	{
		return $this->sDefault;
	}

	/**
	 * Return field name
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->sName;
	}

	/**
	 * Return field type
	 *
	 * @return	string
	 */
	public function getType($bWithPackage = true)
	{
		if(!$bWithPackage && strpos($this->sType, ':') !== false)
		{
			return explode(':', $this->sType)[1];
		}

		return $this->sType;
	}

	/**
	 * Return field type attribute
	 *
	 * @return	string
	 */
	public function getTypeAttr($iPosition = 0)
	{
		if(!isset($this->aTypeAttr[$iPosition]))
		{
			throw new Exception('There is no field type attribute: '. $iPosition);
		}

		return $this->aTypeAttr[$iPosition];
	}

	/**
	 * Return field type attributes
	 *
	 * @return	array
	 */
	public function getTypeAttribs()
	{
		return $this->aTypeAttr;
	}

	/**
	 * Return field type attributes count
	 *
	 * @return	int
	 */
	public function getTypeAttrCount()
	{
		return count($this->aTypeAttr);
	}

	/**
	 * Check if field has option
	 *
	 * @param	strign	$sName	option name
	 * @return	bool
	 */
	public function hasOption($sName)
	{
		return in_array($sName, $this->aOptions);
	}

	/**
	 * Check if field has additional options
	 *
	 * @return	bool
	 */
	public function hasOptions()
	{
		return empty($this->aOptions);
	}

	/**
	 * Check if field type is reference to other model
	 *
	 * @return	bool
	 */
	public function isModelType()
	{
		return !empty($this->sType) && $this->sType[0] == strtoupper($this->sType[0]);
	}
}
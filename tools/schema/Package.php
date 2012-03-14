<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Schema;

/**
 * Package description object
 *
 * @author	Daniel Kózka
 */
class Package
{
	/**
	 * Package models
	 *
	 * @var	array
	 */
	protected $aModels = array();

	/**
	 * Package name
	 *
	 * @var	string
	 */
	protected $sName;

	/**
	 * Other packages connected with this package
	 *
	 * @var	array
	 */
	protected $aConnections = array();

	/**
	 * Constructor
	 *
	 * @param	string	$sName		package name
	 * @param	array	$aModels	package models
	 * @param	string	$sUses		other packages
	 * @return	ScaZF\Tool\Schema\Package
	 */
	public function __construct($sName, array $aModels, $sUses)
	{
		$this->sName = $sName;
		$this->aModels = $aModels;
		$this->aConnections = empty($sUses) ? array() : explode(',', $sUses);
		$this->aConnections = array_unique($this->aConnections);

		foreach($this->aConnections as &$sPackage)
		{
			$sPackage = ucfirst(strtolower($sPackage));
		}
	}

// GETTERS

	/**
	 * Get model from package by name
	 *
	 * @return	ScaZF\Tool\Schema\Model
	 */
	public function getModel($sName)
	{
		if(!isset($this->aModels[$sName]))
		{
			throw Exception('Model '. $sName .' in package '. $this->sName .' not found');
		}

		return $this->aModels[$sName];
	}

	/**
	 * Return all models from package
	 *
	 * @return	array
	 */
	public function getModels()
	{
		return $this->aModels;
	}

	/**
	 * Return package name
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->sName;
	}

	/**
	 * Return other packages connected with this package
	 *
	 * @return	array
	 */
	public function getConnections()
	{
		return $this->aConnections;
	}

	/**
	 * Check if package has connected packages
	 *
	 * @return	bool
	 */
	public function hasConnections()
	{
		return !empty($this->aConnections);
	}
}
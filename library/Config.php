<?php

/**
 * @namespace
 */
namespace Ska;

/**
 * Configuration class for SkaZF
 *
 * @license	New BSD License
 * @author	Daniel Kózka
 */
class Config
{
	use \Ska\Singleton;

	/**
	 * Instance of db adapter
	 *
	 * @var	\Zend_Db_Adapter_Abstract
	 */
	protected $oDbAdapter = null;

// GETTERS

	/**
	 * Return db adapter
	 *
	 * @return	Zend_Db_Adapter_Abstract
	 */
	public function getDb()
	{
		return $this->oDbAdapter;
	}

// SETTERS

	/**
	 * Set db adapter
	 *
	 * @param	Zend_Db_Adapter_Abstract	$oDb	adapter for db
	 * @return	\Ska\Config
	 */
	public function setDbAdapter(\Zend_Db_Adapter_Abstract $oDb)
	{
		$this->oDbAdapter = $oDb;
		return $this;
	}
}
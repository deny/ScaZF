<?php

/**
 * @namespace
 */
namespace Sca;

/**
 * Configuration class for ScaZF
 *
 * @license	New BSD License
 * @author	Daniel Kózka
 */
class Config
{
	use \Sca\Singleton;

	/**
	 * Instance of db adapter
	 *
	 * @var	\Zend_Db_Adapter_Abstract
	 */
	protected $oDbAdapter = null;

	/**
	 * Path to models
	 */
	protected $sModelPath;

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
	 * @return	\Sca\Config
	 */
	public function setDbAdapter(\Zend_Db_Adapter_Abstract $oDb)
	{
		$this->oDbAdapter = $oDb;
		return $this;
	}

	/**
	 * Set model autoload
	 *
	 * @return	\Sca\Config
	 */
	public function setModelAutoload($sPath)
	{
		$this->sModelPath = $sPath;
		\Zend_Loader_Autoloader::getInstance()
					->pushAutoloader(array($this, 'autoload'), 'Model');
		return $this;
	}

// OTHERS

	/**
	 * Autoload function for ScaZF models
	 *
	 * @return	void
	 */
	public function autoload($sClass)
	{
		require_once $this->sModelPath .'/'. str_replace(['Model\\', '\\'], ['', '/'], $sClass) .'.php';
	}
}
<?php

namespace Sca\DataObject;

/**
 * Abstract class using to create models
 *
 * @copyright	Copyright (c) 2011, Autentika Sp. z o.o.
 * @license		New BSD License
 * @author		Mateusz Juściński, Mateusz Kohut, Daniel Kózka
 */
abstract class Element
{

// MODEL FIELDS

	/**
	 * Instance of db adapter
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $oDb;

	/**
	 * Primary Key
	 *
	 * @var	int
	 */
	private $iId;

	/**
	 * Main component info
	 *
	 * @var	string
	 */
	private $aMain;

	/**
	 * Components informations
	 *
	 * @var	array
	 */
	private $aComponents = array();

	/**
	 * Which parts of object are modified
	 *
	 * @var	array
	 */
	private $aModified = array();

	/**
	 * The list of modified fields
	 *
	 * @var array
	 */
	private $aModifiedFields = array();

	/**
	 * Is object removed
	 *
	 * @var bool
	 */
	private $bDeleted = false;

	/**
	 * Is first save
	 *
	 * @var	string
	 */
	private $bNew = false;

// PUBLIC METHODS

	/**
	 * Return element id
	 *
	 * @return	int
	 */
	final public function getId()
	{
		return $this->iId;
	}

	/**
	 * Do not allow serialization of a database object
	 *
	 * @return	array
	 */
	public function __sleep()
	{
		$oReflect = new ReflectionClass($this);
		$aResult = array();

		// drop DB field
		foreach($oReflect->getProperties() as $oProperty)
		{
			if($oProperty->getName() != 'oDb')
			{
				$aResult[] = $oProperty->getName();
			}
		}

		return $aResult;
	}

	/**
	 * Loads database object after usnserialize
	 */
	public function __wakeup()
	{
		$this->oDb = \Sca\Config::getInstance()->getDb();
	}

	/**
	 * Delete object from DB
	 *
	 * @return void
	 */
	public function delete()
	{
		$this->oDb->delete($this->aMain['table'], $this->getWhereString());
		$this->bDeleted = true;
	}

	/**
	 * Save object to DB
	 *
	 * @return	void
	 */
	public function save()
	{
		// is deleted
		if($this->bDeleted)
		{
			throw new \Sca\DataObject\Exception('Object is already deleted, you cannot save it.');
		}

		if($this->bNew) // if new object - need insert
		{
			$sTable = $this->aMain['table'];
			$this->oDb->insert($sTable, $this->aModifiedFields[$sTable]);
			$this->aModifiedFields[$sTable] = array();
			$this->aModified[$sTable] = false;
			$this->bNew = false;
		}
		elseif($this->isModified()) // if oject modified
		{
			try
			{
				// check whether any data has been modified
				$this->oDb->beginTransaction();

				foreach($this->aModified as $sTable => $bModified)
				{
					if($bModified)
					{
						$this->oDb->update(
							$sTable,
							$this->aModifiedFields[$sTable],
							$this->getWhereString($sTable)
						);

						$this->aModifiedFields[$sTable] = array();
						$this->aModified[$sTable] = false;
					}
				}

				$this->oDb->commit();
			}
			catch(Exception $oExc)
			{
				$this->oDb->rollBack();
				throw $oExc;
			}
		}
	}

// PROTECTED METHODS

	protected function initDefault(array &$aRow, array &$aComponents = [])
	{
		$this->init($aRow, $aComponents);

		$this->bNew = true;
		$this->aModifiedFields[$this->aMain['table']] = $aRow;
	}

	/**
	 * DataObject inialization
	 * Warning: In child class constructor use this function!
	 *
	 * @return	\Sca\DataObject\Element
	 */
	protected function init(array &$aRow, array &$aComponents = [])
	{
		$this->oDb = \Sca\Config::getInstance()->getDb();
		$this->aMain = end($aComponents); // last element is main element

		$this->iId = $aRow[$this->aMain['key']];
		$this->aComponents = $aComponents;

		foreach($this->aComponents as $aInfo)
		{
			$this->aModified[$aInfo['table']] = false;
			$this->aModifiedFields[$aInfo['table']]  = array();
		}

		return $this;
	}

	/**
	 * Returns true, if object was modified
	 *
	 * @return bool
	 */
	final protected function isModified()
	{
		return in_array(true, $this->aModified, true);
	}

	/**
	 * Set new DB field value
	 *
	 * @param	string	$sTable		DB table name
	 * @param	string	$sField		DB field name
	 * @param	string	$mValue		new field value
	 * @return	void
	 */
	final protected function setDataValue($sTable, $sField, $mValue)
	{
		$this->aModifiedFields[$sTable][$sField] = $mValue;
		$this->aModified[$sTable] = true;
	}

	/**
	 * Returns SQL WHERE code for Primary Key fields
	 *
	 * @param	string	$sTable	table name
	 * @return	string
	 */
	final protected function getWhereString($sTable = null)
	{
		$sTable = empty($sTable) ? $this->aMain['table'] : $sTable;
		return new Where(
			$sTable .'.'. $this->aMain['key'] .' = ?',
			$this->iId
		);
	}

// STATIC

	/**
	 * Return ifnormation about element (table, alias, key)
	 *
	 * @return	array
	 */
	public static abstract function info();
}

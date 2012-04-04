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
	 * Primary Key name/primary table
	 *
	 * @var	string
	 */
	private $sKey;

	/**
	 * Tables names
	 *
	 * @var	array
	 */
	private $aTables = array();

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

// PUBLIC METHODS

	/**
	 * Return element id
	 *
	 * @return	int
	 */
	public function getId()
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
		$this->oDb->delete($this->sKey, $this->getWhereString());
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

		if($this->isModified())
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

	/**
	 * DataObject inialization
	 * Warning: In child class constructor use this function!
	 *
	 * @return	\Sca\DataObject\Element
	 */
	protected function init(array &$aRow, array &$aTables = [])
	{
		$this->oDb = \Sca\Config::getInstance()->getDb();
		$this->sKey = end($aTables); // last table is main/key table

		$this->iId = $aRow[$this->sKey .'_id'];
		$this->aTables = $aTables;
		$this->aModified = array_fill_keys($this->aTables, false);
		$this->aModifiedFields  = array_fill_keys($this->aTables, []);
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
		$sTable = empty($sTable) ? $this->sKey : $sTable;
		return new Where(
			$sTable .'.'. $this->sKey .'_id = ?',
			$this->iId
		);
	}
}

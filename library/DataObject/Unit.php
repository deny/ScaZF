<?php

namespace Ska\DataObject;

/**
 * Abstract class using to create models
 *
 * @copyright	Copyright (c) 2011, Autentika Sp. z o.o.
 * @license		New BSD License
 * @author		Mateusz Juściński, Mateusz Kohut, Daniel Kózka
 */
abstract class Unit
{
	/**
	 * Instance of db adapter
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $oDb;

	/**
	 * Tables names
	 *
	 * @var	array
	 */
	protected $aTables;

	/**
	 * Primary Key definition
	 *
	 * @var array
	 */
	private $aPrimaryKey = array();

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
	 * Which parts of object are modified
	 *
	 * @var	array
	 */
	private $aModified = array();

	/**
	 * Constructor, sets necessary data for the data object
	 * Warning: In child class use this constructor!
	 *
	 * @param	array	$aPrimaryKey	array with primary key description (<field name> => <field value>) for each table
	 * @return	\Ska\DataObject\Unit
	 */
	public function __construct(array $aPrimaryKey)
	{
		$this->oDb = \Ska\Config::getInstance()->getDb();
		$this->aPrimaryKey = $aPrimaryKey;
		$this->aTables = array_keys($aPrimaryKey);
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
		$this->oDb = \Ska\Config::getInstance()->getDb();
	}

	/**
	 * Delete object from DB
	 *
	 * @return void
	 */
	public function delete()
	{
		foreach($this->aTables as $sTable)
		{
			$aTmp[] = $sTable .'.*';
		}
		$sQuery = 'DELETE '. implode(', ', $aTmp) .' FROM '. implode(', ', $this->aTables);
		$sQuery .= ' WHERE '. $this->getWhereString();

		$this->oDb->query($sQuery);
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
			throw new Ska_DataObject_Exception('Object is already deleted, you cannot save it.');
		}

		// check whether any data has been modified
		if($this->bModified)
		{
			$this->oDb->update(
				$this->sTableName,
				$this->aModifiedFields,
				$this->getWhereString()
			);

			$this->aModifiedFields = array();
			$this->bModified = false;
		}
	}

	/**
	 * Returns DB table name
	 *
	 * @return	string
	 */
	protected function getTableName()
	{
		return $this->sTableName;
	}

	/**
	 * Returns true, if object was modified
	 *
	 * @return bool
	 */
	final protected function isModified()
	{
		return $this->bModified;
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
		$this->aModifiedFields[$sField] = $mValue;
		$this->bModified = true;
	}

	/**
	 * Returns SQL WHERE code for Primary Key fields
	 *
	 * @return string
	 */
	private function getWhereString()
	{
		$oWhere = new Ska_DataObject_Where();

		foreach($this->aPrimaryValue as $sTable => $aKeys)
		{
			foreach($aKeys as $sField => $sValue)
			{
				$oWhere->addAnd($sTable .'.'. $sField . ' = ?', $sValue);
			}
		}

		return $oWhere->getWhere();
	}
}

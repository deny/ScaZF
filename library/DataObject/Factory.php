<?php

namespace Ska\DataObject;

/**
 * Abstract class using to create factory for models
 *
 * @copyright	Copyright (c) 2011, Autentika Sp. z o.o.
 * @license		New BSD License
 * @author		Mateusz Juściński, Mateusz Kohut, Daniel Kózka
 */
abstract class Factory
{
	use \Ska\Singleton;
	
	/**
	 * Instance of db adapter
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $oDb = null;

	/**
	 * Primary Key definition
	 *
	 * @var array
	 */
	private $aPrimaryKey = null;

	/**
	 * Select object for paginator
	 *
	 * @var Zend_Db_Select
	 */
	private $oPaginatorSelect = null;

	/**
	 * Select object (will be cloned)
	 *
	 * @var Zend_Db_Select
	 */
	private $oSelect = null;

	/**
	 * DB table name
	 *
	 * @var string
	 */
	private $sTableName = null;

	/**
	 * Constructor, sets necessary data for the data object
	 * Warning: In child class use this constructor!
	 *
	 * @param	string	$sTableName		name of DB table connected with model
	 * @param	array	$aPrimaryKey	array with primay key fields
	 * @return	Ska\DataObject\Factory
	 */
	public function __construct($sTableName, array $aPrimaryKey)
	{
		$this->oDb = \Ska\Config::getInstance()->getDb();

		$this->sTableName = $sTableName;
		$this->aPrimaryKey = $aPrimaryKey;
	}

// Factory method

	/**
	 * Returns an array of objects with specified ID
	 *
	 * @param	array	$aIds		array with ID/IDs
	 * @param	array	$aOrder		array with order definition
	 * @param	array	$aOptions	array with additional options
	 * @return	array
	 */
	public function getFromIds(array $aIds, array $aOrder = array(), array $aOptions = [])
	{
		if(empty($aIds))
		{
			return array();
		}

		$oSelect = $this->getSelect('*', $aOptions)
				->where($this->getWhereString($aIds));

		if(!empty($aOrder))
		{
			$oSelect->order($aOrder);
		}

		$aDbRes = $oSelect->query()->fetchAll();

		return $this->createList($aDbRes, $aOptions);
	}

	/**
	 * Returns an array of object that matches the given condition
	 *
	 * @param	string|Ska\DataObject\Where	$oWhere		where string or Where object
	 * @param	array						$aOrder		array with order definition
	 * @param	array						$aOptions	array with additional options
	 * @return	array
	 */
	public function getFromWhere($mWhere, array $aOrder = array(), array $aOptions = [])
	{
		if($mWhere instanceof Where)
		{
			$mWhere = $mWhere->getWhere();
		}

		$oSelect = $this->getSelect('*', $aOptions)->where($mWhere);

		if(!empty($aOrder))
		{
			$oSelect->order($aOrder);
		}

		$aDbRes = $oSelect->query()->fetchAll();

		return $this->createList($aDbRes, $aOptions);
	}

	/**
	 * Returns a single object with the specified ID
	 *
	 * @param	mixed	$mId		specific key value or an array (<field> => <value>)
	 * @param	array	$aOptions	array with additional options
	 * @return	Ska\DataObject\Unit
	 */
	public function getOne($mId, array $aOptions = [])
	{
		$aDbRes = $this->getSelect('*', $aOptions)
				->where($this->getWhereString($mId))
				->limit(1)->query()->fetchAll();

		if(empty($aDbRes))
		{
			throw new Ska\DataObject\Exception('The object with the specified ID does not exist');
		}

		return $this->createObject($aDbRes[0], $aOptions);
	}

	/**
	 * Returns one page for paginator
	 *
	 * @param	int								$iPage		page number
	 * @param	int								$iCount		number of results per page
	 * @param	array							$aOrder		array with order definition
	 * @param	string|Ska\DataObject\Where		$oWhere		where string or Where object
	 * @param	array							$aOptions	array with additional options
	 * @return	array
	 */
	public function getPage($iPage, $iCount, array $aOrder = array(), $mWhere = null, array $aOptions = [])
	{
		$oSelect = $this->getSelect('*', $aOptions);
		$oSelect->limitPage($iPage, $iCount);

		// adds order
		foreach($aOrder as $sOrder)
		{
			$oSelect->order($sOrder);
		}

		// adds where
		if($mWhere !== null)
		{
			if($mWhere instanceof Ska\DataObject\Where)
			{
				$mWhere = $mWhere->getWhere();
			}

			$oSelect->where($mWhere);
		}

		$aResult = $oSelect->query()->fetchAll();

		return $this->createList($aResult, $aOptions);
	}

	/**
	 * Returns a paginator set on a particular page
	 *
	 * @param	int								$iPage		page number
	 * @param	int								$iCount		number of results per page
	 * @param	array							$aOrder		array with order definition
	 * @param	string|Ska\DataObject\Where		$oWhere		where string or Where object
	 * @param	array							$aOptions	array with additional options
	 * @return	array
	 */
	public function getPaginator($iPage, $iCount, array $aOrder = array(), $mWhere = null, array $aOptions = [])
	{
		$oSelect = $this->getCountSelect($aOptions);

		if($mWhere !== null)
		{
			if($mWhere instanceof Ska\DataObject\Where)
			{
				$mWhere = $mWhere->getWhere();
			}

			$oSelect->where($mWhere);
		}

		$oInterface = new Paginator\Interface($this, $oSelect, $aOptions);
		$oInterface->setOrder($aOrder);

		if($mWhere !== null)
		{
			$oInterface->setWhere($mWhere);
		}

		$oPaginator = new Zend_Paginator($oInterface);
		$oPaginator->setCurrentPageNumber($iPage)
					->setItemCountPerPage($iCount);

		return $oPaginator;
	}

	/**
	 * Create object from DB row
	 *
	 * @param	array	$aRow	one row from database
	 * @param	array	$aOptions	array with additional options
	 * @return	Ska\DataObject\Unit
	 */
	abstract public function createObject(array $aRow, array $aOptions = []);

// additional methods

	/**
	 * Creates an array of objects from the results returned by the database
	 *
	 * @param	array	$aDbResult	results returned by the database
	 * @param	array	$aOptions	array with additional options
	 * @return	array
	 */
	protected function createList(array &$aDbResult, array $aOptions = [])
	{
		$aResult = array();

		foreach($aDbResult as $aRow)
		{
			$aResult[] = $this->createObject($aRow, $aOptions);
		}

		return $aResult;
	}

	/**
	 * Returns DB table name
	 *
	 * @return	string
	 */
	final protected function getTableName()
	{
		return $this->sTableName;
	}

	/**
	 * Returns an array with describe Primary Key
	 *
	 * @return	array
	 */
	final protected function getPrimaryKey()
	{
		return $this->aPrimaryKey;
	}

	/**
	 * Returns a Select object
	 *
	 * @param	mixed	$mFields	fields to select
	 * @param	array	$aOptions	array with additional options
	 * @return	Zend_Db_Select
	 */
	protected function getSelect($mFields = '*', array $aOptions = [])
	{
		if($this->oSelect === null)
		{
			$oSelect = new Zend_Db_Select($this->oDb);
		}
		else
		{
			$oSelect = clone $this->oSelect;
		}

		$oSelect->from($this->sTableName, $mFields);

		return $oSelect;
	}

	/**
	 * Returns a Select object for Paginator Count
	 *
	 * @param	array	$aOptions	array with additional options
	 * @return	Zend_Db_Select
	 */
	protected function getCountSelect(array $aOptions = [])
	{
		return $this->getSelect()
						->reset(Zend_Db_Select::COLUMNS)
						->columns(new Zend_Db_Expr('COUNT(*)'));
	}


	/**
	 * Returns SQL WHERE string created for the specified key fields
	 *
	 * @param	mixed	$mId	primary key values
	 * @return	string
	 */
	protected function getWhereString($mId)
	{
		$oWhere = new Ska\DataObject\Where();

		if(count($this->aPrimaryKey) > 1)
		{
			// many fields in key
			foreach($mId as $aKeys)
			{
				$oWhere2 = new Ska\DataObject\Where();

				foreach($this->aPrimaryKey as $sField)
				{
					if(!isset($aKeys[$sField]))
					{
						throw new Ska\DataObject\Exception('No value for key part: ' . $sField);
					}

					// where for a single field
					$sTmp = $this->getTableName() .'.'. $sField;
					$sTmp .= is_array($aKeys[$sField]) ? ' IN (?)' : ' = ?';

					$oWhere2->addAnd($sTmp, $aKeys[$sField]);
				}

				$oWhere->addOr($oWhere2);
				unset($oWhere2);
			}
		}
		else
		{
			// only one column is table key
			$sTmp = $this->getTableName() .'.'. $this->aPrimaryKey[0];
			$sTmp .= is_array($mId) ? ' IN (?)' : ' = ?';

			$oWhere->addAnd($sTmp, $mId);
		}

		return $oWhere->getWhere();
	}
}

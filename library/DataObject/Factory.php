<?php

namespace Sca\DataObject;

/**
 * Abstract class using to create factory for models
 *
 * @copyright	Copyright (c) 2011, Autentika Sp. z o.o.
 * @license		New BSD License
 * @author		Mateusz Juściński, Mateusz Kohut, Daniel Kózka
 */
abstract class Factory
{
	/**
	 * Instance of db adapter
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $oDb = null;

	/**
	 * Main component info
	 *
	 * @var	string
	 */
	private $aMain;

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
	 * Components
	 *
	 * @var	array
	 */
	private $aComponents = array();

	protected function init(array &$aComponents = [])
	{
		$this->oDb = \Sca\Config::getInstance()->getDb();
		$this->aMain = array_pop($aComponents); // last component is main component
		$this->aComponents = $aComponents;
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

		return $this->buildList($aDbRes, $aOptions);
	}

	/**
	 * Returns an array of object that matches the given condition
	 *
	 * @param	string|Sca\DataObject\Where	$oWhere		where string or Where object
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

		return $this->buildList($aDbRes, $aOptions);
	}

	/**
	 * Returns a single object with the specified ID
	 *
	 * @param	mixed	$mId		specific key value or an array (<field> => <value>)
	 * @param	array	$aOptions	array with additional options
	 * @return	Sca\DataObject\Unit
	 */
	public function getOne($iId, array $aOptions = [])
	{
		$aDbRes = $this->getSelect('*', $aOptions)
					->where($this->getWhereString($iId))
					->limit(1)->query()->fetchAll();

		if(empty($aDbRes))
		{
			throw new \Sca\DataObject\Exception('The object with the specified ID does not exist');
		}

		return $this->buildObject($aDbRes[0], $aOptions);
	}

	/**
	 * Returns one page for paginator
	 *
	 * @param	int								$iPage		page number
	 * @param	int								$iCount		number of results per page
	 * @param	array							$aOrder		array with order definition
	 * @param	string|Sca\DataObject\Where		$oWhere		where string or Where object
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
			if($mWhere instanceof Sca\DataObject\Where)
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
	 * @param	string|Sca\DataObject\Where		$oWhere		where string or Where object
	 * @param	array							$aOptions	array with additional options
	 * @return	array
	 */
	public function getPaginator($iPage, $iCount, array $aOrder = array(), $mWhere = null, array $aOptions = [])
	{
		$oSelect = $this->getCountSelect($aOptions);

		if($mWhere !== null)
		{
			if($mWhere instanceof Sca\DataObject\Where)
			{
				$mWhere = $mWhere->getWhere();
			}

			$oSelect->where($mWhere);
		}

		$oInterface = new Paginator\PaginatorInterface($this, $oSelect, $aOptions);
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

// NEW ELEMENT CREATE

	/**
	 * Create new element
	 *
	 * @return	\Sca\DataObject\Element
	 */
	protected function createNewElement(array $aData)
	{
		try
		{
			$this->oDb->beginTransaction();

			$aId = $aTmp = [];
			foreach($aData as $sTable => $aFields)
			{
				$this->oDb->insert($sTable, $aFields + $aId);

				if($sTable == $this->aMain['table'])
				{
					$aId[$this->aMain['key']] = $this->oDb->lastInsertId($this->aMain['table'], $this->aMain['key']);
				}
				$aTmp = array_merge($aTmp, $aFields);
			}

			$this->oDb->commit();
		}
		catch(Exception $oExc)
		{
			$this->oDb->rollBack();
			throw $oExc;
		}

		$aTmp += $aId;

		return $this->buildObject($aTmp);
	}

	abstract protected function prepareToCreate(array $aData);


// NEW ELEMENT BUILD

	/**
	 * Create object from DB Data
	 *
	 * @param	array	$aRow	one row from database
	 * @param	array	$aOptions	array with additional options
	 * @return	\Sca\DataObject\Element
	 */
	final public function buildObject(array $aRow, array $aOptions = [])
	{
		$this->prepareToBuild($aRow, $aOptions);
		return $this->buildElement()->init($aRow);
	}

	/**
	 * Create element
	 *
	 * @return	\Sca\DataObject\Element
	 */
	abstract protected function buildElement();

	/**
	 * Prapare DB Data to object create
	 *
	 * @param	array	$aRow	one row from database
	 * @param	array	$aOptions	array with additional options
	 * @return	array
	 */
	protected function prepareToBuild(array &$aRow, array $aOptions = [])
	{
	}

	/**
	 * Creates an array of objects from the results returned by the database
	 *
	 * @param	array	$aDbResult	results returned by the database
	 * @param	array	$aOptions	array with additional options
	 * @return	array
	 */
	protected function buildList(array &$aDbResult, array $aOptions = [])
	{
		$aResult = array();

		foreach($aDbResult as $aRow)
		{
			$aResult[] = $this->buildObject($aRow, $aOptions);
		}

		return $aResult;
	}

// ADDITIONAL METHODS

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
			$this->oSelect = new \Zend_Db_Select($this->oDb);
			$this->oSelect->from($this->aMain['table'] .' AS '. $this->aMain['alias']);
			$sLast = $this->aMain['alias'];
			$sKey = $this->aMain['key'];

			foreach($this->aComponents as $aInfo)
			{
				$this->oSelect->join(
					$aInfo['table'] .' AS '. $aInfo['alias'],
					$aInfo['alias'] .'.'. $sKey .' = '. $sLast .'.'. $sKey
				);
				$sLast = $aInfo['alias'];
			}
		}

		$oSelect = clone $this->oSelect;

		if($mFields != '*' && is_array($mFields))
		{
			$oSelect->reset(\Zend_Db_Select::COLUMNS)->columns($mFields);
		}

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
						->reset(\Zend_Db_Select::COLUMNS)
						->columns(new \Zend_Db_Expr('COUNT(*)'));
	}


	/**
	 * Returns SQL WHERE string created for the specified key fields
	 *
	 * @param	mixed	$mIds	primary key value/values
	 * @return	string
	 */
	protected function getWhereString($mId)
	{
		return new Where(
			$this->aMain['alias'] .'.'. $this->aMain['key'] . (is_array($mId) ? ' IN(?)' : ' = ?'),
			$mId
		);
	}
}

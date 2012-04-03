<?php

namespace Sca\DataObject;

/**
 * Class that automates creating SQL conditions
 *
 * @copyright	Copyright (c) 2011, Autentika Sp. z o.o.
 * @license		New BSD License
 * @author		Mateusz Juściński, Mateusz Kohut, Daniel Kózka
 */
class Where
{
	/**
	 * Created SQL condition
	 *
	 * @var string
	 */
	private $sWhere = '';

	/**
	 * Instance of db adapter
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $oDb;

	/**
	 * Constructor
	 *
	 * @param	string|\Sca\DataObject\Where		$mWhere		where string or Where object
	 * @param	string|null						$mValue		value for where string
	 * @return	\Sca\DataObject\Where
	 */
	public function __construct($mWhere = null, $mValue = null)
	{
		$this->oDb = \Sca\Config::getInstance()->getDb();

		if($mWhere !== null)
		{
			$this->addAnd($mWhere, $mValue);
		}
	}

	/**
	 * It adds next element of the condition, preceded by a logical AND
	 *
	 * @param	string|\Sca\DataObject\Where		$mWhere		where string or Where object
	 * @param	string|null						$mValue		value for where string
	 * @return	\Sca\DataObject\Where
	 */
	public function addAnd($mWhere, $mValue = null)
	{
		if(!empty($this->sWhere))
		{
			$this->sWhere .= ' AND ';
		}

		$this->sWhere .= $this->parse($mWhere, $mValue);

		return $this;
	}

	/**
	 * It adds next element of the condition, preceded by a logical OR
	 *
	 * @param	string|\Sca\DataObject\Where		$mWhere		where string or Where object
	 * @param	string|null						$mValue		value for where string
	 * @return	\Sca\DataObject\Where
	 */
	public function addOr($mWhere, $mValue = null)
	{
		if(!empty($this->sWhere))
		{
			$this->sWhere .= ' OR ';
		}

		$this->sWhere .= $this->parse($mWhere, $mValue);

		return $this;
	}

	/**
	 * Returns created SQL condition
	 *
	 * @return string
	 */
	public function getWhere()
	{
		if(empty($this->sWhere))
		{
			return 'TRUE';
		}
		else
		{
			return $this->sWhere;
		}
	}

	/**
	 * Negates the actual condition
	 *
	 * @return	void
	 */
	public function negate()
	{
		if($this->sWhere == '')
		{
			$this->sWhere = 'FALSE';
		}
		else
		{
			$this->sWhere = 'NOT (' . $this->sWhere . ')';
		}
	}

	/**
	 * Returns created SQL condition
	 *
	 * @return	string
	 */
	public function __toString()
	{
		return $this->getWhere();
	}

	/**
	 * Parse the pased value to the part of SQL command
	 *
	 * @param	string|\Sca\DataObject\Where		$mWhere		where string or Where object
	 * @param	string|null						$mValue		value for where string
	 * @return	string
	 */
	private function parse($mWhere, $mValue)
	{
		$sResult = '';

		if($mWhere instanceof Sca_DataObject_Where)
		{
			$sResult .= '('. $mWhere->getWhere() .')';
		}
		else
		{
			$sResult = $this->oDb->quoteInto($mWhere, $mValue);
		}

		return $sResult;
	}
}

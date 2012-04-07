<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Db;

/**
 * Field Sql generator
 *
 * @author	Daniel Kózka
 */
class Field
{
	/**
	 * Model definition
	 *
	 * @var	\ScaZF\Tool\Schema\Model
	 */
	protected $oModel;

	/**
	 * Constructor
	 *
	 * @param	\ScaZF\Tool\Schema\Model	$oModel	model definition
	 * @return  \ScaZF\Tool\Db\Field
	 */
	public function __construct(\ScaZF\Tool\Db\Wrapper\Model $oModel)
	{
		$this->oModel = $oModel;
	}

	/**
	 * Generate SQL from field definition
	 *
	 * @param	\ScaZF\Tool\Schema\Field	$oField	field definition
	 * @return	string
	 */
	public function toSql(\ScaZF\Tool\Schema\Field $oField)
	{
		$sSql = '`'. strtolower($this->oModel->getAlias() .'_'. $oField->getName()) . '`';
		$aAttr = $oField->getTypeAttribs();

		switch($oField->getType())
		{
			case 'int': $sSql .= ' INT(11)'; break;
			case 'uint': $sSql .= ' INT(10) UNSIGNED'; break;
			case 'char': $sSql .= ' CHAR('. $aAttr[0] .')'; break;
			case 'string':
				if(empty($aAttr))
				{
					$sSql .= 'TEXT';
				}
				else
				{
					$sSql .= ' VARCHAR('. (int) $aAttr[0] . ')';
				}
				break;
			default: // model type
				$oModel = new \ScaZF\Tool\Db\Wrapper\Model(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($oField->getType())
				);

				// referenced model isn't component or one-to-many
				if($oModel->getComponent() === null && empty($aAttr))
				{
					$sSql .= ' INT(10) UNSIGNED';
				}
				else
				{
					$sSql = '';
				}
		}

		return $sSql;
	}
}
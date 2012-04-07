<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Db;

/**
 * Table Sql generator
 *
 * @author	Daniel Kózka
 */
class Table
{
	/**
	 * Generate SQL from model definition
	 *
	 * @param	\ScaZF\Tool\Schema\Model	$oModel	model definition
	 * @return	string
	 */
	public function toSql(\ScaZF\Tool\Schema\Model $oModel)
	{
		$oModel = new \ScaZF\Tool\Db\Wrapper\Model($oModel);

		$aResult = [
			'table' 		=> '',
			'join'			=> '',
			'foreignKeys'	=> ''
		];

		//
		$sSql = 'CREATE TABLE `'. $oModel->getTableName() . "` (\n";

		$aFields = [
			"\t`". $oModel->getKey() . '` INT(10) UNSIGNED'. ($oModel->hasPrimaryKey() ? ' NOT NULL AUTO_INCREMENT' : '')
		];

		$oFieldSql = new Field($oModel);

		foreach($oModel->getFields() as $oField)
		{
			$sTmp = $oFieldSql->toSql($oField);

			if(!empty($sTmp))
			{
				$aFields[] = "\t". $sTmp;
			}
		}

		$sSql .= implode(",\n", $aFields);

		if($oModel->hasPrimaryKey())
		{
			$sSql .= ",\n\t". 'PRIMARY KEY('. $oModel->getKey() .')';
		}

		$sSql .= "\n".') ENGINE=InnoDB;';

		return $sSql;
	}
}
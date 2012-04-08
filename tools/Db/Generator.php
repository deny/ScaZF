<?php

/**
 * @namespace
 */
namespace ScaZf\Tool\Db;

/**
 * Sql generator for Schema package
 *
 * @author	Daniel Kózka
 */
class Generator
{
	/**
	 * Get pacakge SQL
	 *
	 * @param	\ScaZF\Tool\Schema\Package	$oPackage	package definition
	 * @return	string
	 */
	public function getSql(\ScaZF\Tool\Schema\Package $oPackage)
	{
		$sTables = '';
		$sTechTabs = '';
		$sFKeys = '';

		foreach($oPackage->getModels() as $oModel)
		{
			$oModel = new \ScaZF\Tool\Wrapper\Model($oModel);

			$aDesc= $oModel->getDescription();

			if(!empty($aDesc['fields']))
			{
				$sTables .= $this->createTable($aDesc['name'], $aDesc['fields'], $aDesc['other']);
			}

			foreach($aDesc['techTables'] as $aTable)
			{
				$sTechTabs .= $this->createTable($aTable['name'], $aTable['fields'], $aTable['other']);
			}

			foreach($aDesc['foreignKeys'] as $sTable => $aKeys)
			{
				$sFKeys .= $this->createFK($sTable, $aKeys);
			}
		}

		return $sTables ."\n". $sTechTabs ."\n". $sFKeys;
	}

	/**
	 * Return SQL with CREATE TABLE
	 *
	 * @param	string	$sName		table name
	 * @param	array	$aFields	fields definition
	 * @param	array	$aOther		other table options
	 * @return	string
	 */
	protected function createTable($sName, array $aFields, array $aOther = [])
	{
		foreach($aFields as &$aInfo)
		{
			$aInfo = "\t" . $this->createField($aInfo['name'], $aInfo['type'], $aInfo['other']);
		}

		foreach($aOther as &$sInfo)
		{
			$sInfo = "\t" . $sInfo;
		}

		$sSql = 'CREATE TABLE `'. $sName .'` (' . "\n";
		$sSql .= implode(",\n", array_merge($aFields, $aOther));
		$sSql .= "\n".') ENGINE=InnoDB;';

		return $sSql . "\n";
	}

	/**
	 * Create SQL field definition
	 *
	 * @param	string	$sField		field name
	 * @param	string	$sType		field type
	 * @param	string	$sOther		other options
	 * @return	string
	 */
	protected function createField($sName, $sType, $sOther = '')
	{
		return trim('`'. $sName . '` '. $sType . ' '. $sOther) ;
	}

	/**
	 * Create SQL foreing key definition
	 *
	 * @param	string	$sTable		table name
	 * @param	array	$aFields	key fields deifinition
	 * @return	string
	 */
	protected function createFK($sTable, array $aFields)
	{
		$aKeys = [];
		foreach($aFields as $sField => $aForeign)
		{
			$sTmp = 'ADD CONSTRAINT `'. $sTable. '_ibfk_'. $sField .'` ';
			$sTmp .= 'FOREIGN KEY (`'. $sField .'`) ';
			$sTmp .= 'REFERENCES `'. $aForeign['table'] . '` (`'. $aForeign['field'] .'`) ';
			$sTmp .= 'ON DELETE CASCADE ON UPDATE CASCADE';

			$aKeys[] = "\t". $sTmp;
		}

		return 'ALTER TABLE `'. $sTable . "`\n". implode(",\n", $aKeys) . ";\n";
	}
}
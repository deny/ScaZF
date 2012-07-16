<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Crud;

/**
 * Crud generator
 *
 * @author	Daniel Kózka
 */
class Generator
{
	/**
	 * Global namespace for models
	 *
	 * @var	string
	 */
	protected $sGlobalNamespace = '\\Model';

	/**
	 * Information about one-to-many connections
	 *
	 * @var	array
	 */
	protected $aMultiInfo = [];

	/**
	 * Return PHP base model
	 *
	 * @param	\ScaZF\Tool\Wrapper\Model $oModel	model description
	 * @return	string
	 */
	public function getController(\ScaZF\Tool\Schema\Model $oModel, $sController)
	{
		$oTpl = \ScaZF\Tool\Base\Template::getTemplate('Controller');
		$oModel = new \ScaZF\Tool\Wrapper\Model($oModel);
		$aModelDesc = $oModel->getDescription();

		$aMain = [
			'controller'		=> $sController,
			'sort-types'		=> '',
			'model'				=> '',
			'create-list'		=> '',
			'set-list'			=> '',
			'init-values'		=> '',
			'validators-edit'	=> '',
			'validators-add'	=> ''
		];

	// allow sort
		$aTmp = array();
		foreach($aModelDesc['fields'] as $aField)
		{
			if($aField['type'] != 'TEXT')
			{
				$aTmp [] = $oTpl->getSubTemplate('sort-types', [
					'name' 		=> $aField['orig-name'],
					'db-name'	=> $aField['name']
				]);
			}
		}
		$aMain['sort-types'] = implode("", $aTmp);

	// model factory
		$aMain['model']	= $this->sGlobalNamespace .'\\'. $oModel->getPackage() . '\\'. $oModel->getName();

	// field list - create function
		$aTmp = array();
		foreach($aModelDesc['fields'] as $aField)
		{
			$aTmp[] = rtrim($oTpl->getSubTemplate('create-list', [
				'name' 		=> $aField['orig-name']
			]), "\n") . ',';
		}
		$aMain['create-list'] = rtrim(implode("\n", $aTmp), ',');

	// field list - edit action
		$aTmp = array();
		foreach($oModel->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($oModel, $oField);
			$aAccess = $oField->getAccess();
			$aField = $oField->getDescription();

			if(in_array('set', $aAccess) && !$oField->isModelType())
			{
				if($oField->isModelType())
				{
					if(!$oField->isComponent() && !$oField->isOneToMany())
					{
						$aTmp[] = rtrim($oTpl->getSubTemplate('set-list', [
							'method'	=> 'set'. $this->getFieldName($oField) . 'Id',
							'name' 		=> $aField['field']['orig-name']
						]), "\n") . ',';
					}
				}
				else
				{
					$aTmp[] = rtrim($oTpl->getSubTemplate('set-list', [
						'method'	=> 'set'. $this->getFieldName($oField),
						'name' 		=> $aField['field']['orig-name']
					]), "\n") . ',';
				}
			}
		}
		$aMain['set-list'] = rtrim(implode("\n", $aTmp), ',');


	// złożenie ostacznej wersji
		return $oTpl->getSubTemplate('main', $aMain);
	}

// OTHER METHODS

	/**
	 * Calculate field name
	 *
	 * @param	\ScaZF\Tool\Wrapper\Field $oField	field description
	 * @return	string
	 */
	protected function getFieldName(\ScaZF\Tool\Wrapper\Field $oField)
	{
		$aTmp = explode('_', $oField->getName());

		foreach($aTmp as &$sPart)
		{
			$sPart = ucfirst($sPart);
		}
		return implode('', $aTmp);
	}

	/**
	 * Calculate field type
	 *
	 * @param	\ScaZF\Tool\Wrapper\Field $oField	field description
	 * @return	string
	 */
	protected function getFieldType(\ScaZF\Tool\Wrapper\Field $oField)
	{
		if($oField->isModelType())
		{
			return $this->sGlobalNamespace . $oField->getModelType();
		}

		switch($oField->getType())
		{
			case 'char': return 'string';
			case 'enum': return 'string';
			case 'uint': return 'int';
			default:
				return $oField->getType();
		}
	}

	/**
	 * Calculate model full type
	 *
	 * @param	\ScaZF\Tool\Wrapper\Model	$oModel	model description
	 * @return	string
	 */
	protected function getModelType(\ScaZF\Tool\Wrapper\Model $oModel)
	{
		return $this->sGlobalNamespace . '\\'. $oModel->getPackage() . '\\'. $oModel->getName();
	}

	/**
	 * Calculate field prefixed-name
	 *
	 * @param	\ScaZF\Tool\Wrapper\Field 	$oField		field description
	 * @param	string						$sPrefix	optional field prefix
	 * @return	string
	 */
	protected function getFieldPName(\ScaZF\Tool\Wrapper\Field $oField, $sPrefix = null)
	{
		$sName = $this->getFieldName($oField);

		if(isset($sPrefix))
		{
			return $sPrefix. $sName;
		}

		if($oField->isModelType())
		{
			return 'o'. $sName;
		}

		switch($oField->getType())
		{
			case 'string':
			case 'char':
			case 'enum':
				return 's'. $sName;
			case 'int':
			case 'uint':
				return 'i'. $sName;
		}

		return '';
	}

	/**
	 * Load information about models connected one-to-many with package
	 *
	 * @param	strign	$sPackage	package name
	 * @return	array
	 */
	protected function loadOneToMany($sPackage)
	{
		$oPackage = \ScaZF\Tool\Schema\Manager::getInstance()->getPackage($sPackage);

		foreach($oPackage->getModels() as $oModel)
		{
			$oModel = new \ScaZF\Tool\Wrapper\Model($oModel);
			foreach($oModel->getFields() as $oField)
			{
				$oField = new \ScaZF\Tool\Wrapper\Field($oModel, $oField);
				if($oField->isModelType() && $oField->isOneToMany())
				{
					if(!isset($this->aMultiInfo[$oField->getType()]))
					{
						$this->aMultiInfo[$oField->getType()] = [];
					}

					$this->aMultiInfo[$oField->getType()][] = [
						'model'	=> $oModel->getName(),
						'field'	=> $oField->getName()
					];
				}
			}
		}
	}
}
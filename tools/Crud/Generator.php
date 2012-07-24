<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Crud;


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
		$aMain['model']	= $this->getModelType($oModel);

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

			if(in_array('set', $aAccess) && in_array('get', $aAccess))
			{
				if($oField->isModelType())
				{
					if(!$oField->isComponent() && !$oField->isOneToMany())
					{
						$aTmp[] = rtrim($oTpl->getSubTemplate('set-list', [
							'method'	=> 'set'. $this->getFieldName($oField) . 'Id',
							'name' 		=> $aField['field']['orig-name']
						]), "\n");
					}
				}
				else
				{
					$aTmp[] = rtrim($oTpl->getSubTemplate('set-list', [
						'method'	=> 'set'. $this->getFieldName($oField),
						'name' 		=> $aField['field']['orig-name']
					]), "\n");
				}
			}
		}
		$aMain['set-list'] = implode("\n", $aTmp);

	// init values
		$aTmp = array();
		foreach($oModel->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($oModel, $oField);
			$aAccess = $oField->getAccess();
			$aField = $oField->getDescription();

			if(in_array('set', $aAccess) && in_array('get', $aAccess))
			{
				if($oField->isModelType())
				{
					if(!$oField->isComponent() && !$oField->isOneToMany())
					{
						$aTmp[] = rtrim($oTpl->getSubTemplate('init-values', [
							'method'	=> 'get'. $this->getFieldName($oField) . 'Id',
							'name' 		=> $aField['field']['orig-name']
						]), "\n") . ',';
					}
				}
				else
				{
					$aTmp[] = rtrim($oTpl->getSubTemplate('init-values', [
						'method'	=> 'get'. $this->getFieldName($oField),
						'name' 		=> $aField['field']['orig-name']
					]), "\n") . ',';
				}
			}
		}
		$aMain['init-values'] = rtrim(implode("\n", $aTmp), ',');

	// validators-edit
		$aTmp = $aEdit = array();
		foreach($oModel->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($oModel, $oField);
			$aAccess = $oField->getAccess();
			$aField = $oField->getDescription();

			if(in_array('set', $aAccess) && in_array('get', $aAccess))
			{
				if($oField->isModelType())
				{
					if(!$oField->isComponent() && !$oField->isOneToMany())
					{
						$aTmp[] = rtrim($oTpl->getSubTemplate('validators-edit', [
							'validators'=> '',
							'name' 		=> $aField['field']['orig-name']
						]), "\n") . ',';
						$aEdit[] = $aField['field']['orig-name'];
					}
				}
				else
				{
					$aEdit[] = $aField['field']['orig-name'];
					$aTmp[] = rtrim($oTpl->getSubTemplate('validators-edit', [
							'validators'=> '',
							'name' 		=> $aField['field']['orig-name']
						]), "\n") . ',';
				}
			}
		}
		$aMain['validators-edit'] = rtrim(implode("\n", $aTmp), ',');

	// validators-add
		$aTmp = array();
		foreach($aModelDesc['fields'] as $aField)
		{
			$sName = $aField['orig-name'];

			if(!in_array($sName, $aEdit) && $sName != 'id')
			{
				$aTmp[] = $oTpl->getSubTemplate('validators-add', [
					'validators'=> '',
					'name' 		=> $sName
				]);
			}
		}
		$aMain['validators-add'] = implode("", $aTmp);

	// result
		return $oTpl->getSubTemplate('main', $aMain);
	}

	/**
	 * Return list view
	 *
	 * @param	\ScaZF\Tool\Schema\Model	$oModel
	 * @param	string						$sController
	 * @return	string
	 */
	public function getViewList(\ScaZF\Tool\Schema\Model $oModel, $sController)
	{
		$oTpl = \ScaZF\Tool\Base\Template::getTemplate('ViewList');
		$oModel = new \ScaZF\Tool\Wrapper\Model($oModel);
		$aModelDesc = $oModel->getDescription();

		$aMain = [
			'header'	=> '',
			'content'	=> ''
		];

	// header
		$aTmp = array();
		foreach($aModelDesc['fields'] as $aField)
		{
			if($aField['orig-name'] == 'id')
			{
				continue;
			}

			if($aField['type'] == 'TEXT')
			{
				$aTmp[] = rtrim($oTpl->getSubTemplate('header-simple', [
					'name' 		=> $aField['orig-name']
				]), "\n");
			}
			else
			{
				$aTmp[] = rtrim($oTpl->getSubTemplate('header-sort', [
					'name' 		=> $aField['orig-name']
				]), "\n");
			}
		}
		$aMain['header'] = implode("\n", $aTmp);

	// content
		$aTmp = array();
		foreach($oModel->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($oModel, $oField);
			$aField = $oField->getDescription();

			if($aField['orig-name'] == 'id')
			{
				continue;
			}

			if($oField->isModelType())
			{
				if(!$oField->isComponent() && !$oField->isOneToMany())
				{
					$aTmp[] = rtrim($oTpl->getSubTemplate('content-simple', [
						'method' 		=> 'get'. $this->getFieldName($oField). 'Id'
					]), "\n");
				}
			}
			elseif($aField['field']['type'] == 'TEXT')
			{
				$aTmp[] = rtrim($oTpl->getSubTemplate('content-trim', [
					'method' 		=> 'get'. $this->getFieldName($oField)
				]), "\n");
			}
			else
			{
				$aTmp[] = rtrim($oTpl->getSubTemplate('content-simple', [
					'method' 		=> 'get'. $this->getFieldName($oField)
				]), "\n");
			}
		}
		$aMain['content'] = implode("\n", $aTmp);

		// result
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
	 * Calculate model full type
	 *
	 * @param	\ScaZF\Tool\Wrapper\Model	$oModel	model description
	 * @return	string
	 */
	protected function getModelType(\ScaZF\Tool\Wrapper\Model $oModel)
	{
		return $this->sGlobalNamespace . '\\'. $oModel->getPackage() . '\\'. $oModel->getName();
	}
}
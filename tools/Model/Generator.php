<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Model;

/**
 * Model generator
 *
 * @author	Daniel Kózka
 */
class Generator
{
	protected $sGlobalNamespace = '\\Model';

	/**
	 * Return PHP base model
	 *
	 * @param	\ScaZF\Tool\Wrapper\Model $oModel	model description
	 * @return	string
	 */
	public function getModelBase(\ScaZF\Tool\Schema\Model $oModel)
	{
		$oTpl = \ScaZF\Tool\Model\Template::getTemplate('ModelBase');
		$oModel = new \ScaZF\Tool\Wrapper\Model($oModel);

	// prepare main definition
		$aModel = [
			'namespace'		=> $this->sGlobalNamespace .'\\'. $oModel->getPackage(),
			'model-name'	=> $oModel->getName(),
			'consts'		=> '',
			'fields'		=> '',
			'initialization'=> '',
			'getters'		=> '',
			'setters'		=> '',
			'db-table'		=> $oModel->getTableName(),
			'db-alias'		=> $oModel->getAlias(),
			'db-key'		=> $oModel->getKey()
		];

	// prepare getters and setters
		foreach($oModel->getFields() as $oField)
		{
			$oField = new \ScaZF\Tool\Wrapper\Field($oModel, $oField);
			$aAccess = $oField->getAccess();

		// field
			if($oField->isModelType())
			{
				if($oField->isComponent())
				{
					$aModel['fields'] .= $oTpl->getSubTemplate('field', [
										'field-type'	=> $this->getFieldType($oField),
										'p-field-name' 	=> $this->getFieldPName($oField),
										'field-default'	=> 'null'
									]);
				}
				elseif($oField->isOneToMany())
				{
					$aModel['fields'] .= $oTpl->getSubTemplate('field', [
						'field-type'	=> 'array',
						'p-field-name' 	=> $this->getFieldPName($oField, 'a'),
						'field-default'	=> 'null'
					]);
				}
				else
				{
					$aModel['fields'] .= $oTpl->getSubTemplate('field', [
						'field-type'	=> 'int',
						'p-field-name' 	=> $this->getFieldPName($oField, 'i') . 'Id',
						'field-default'	=> 'null'
					]);

					$aModel['fields'] .= $oTpl->getSubTemplate('field', [
						'field-type'	=> $this->getFieldType($oField),
						'p-field-name' 	=> $this->getFieldPName($oField),
						'field-default'	=> $oField->getDefault() === null ? 'null' : $oField->getDefault()
					]);
				}
			}
			else
			{
				$aModel['fields'] .= $oTpl->getSubTemplate('field', [
					'field-type'	=> $this->getFieldType($oField),
					'p-field-name' 	=> $this->getFieldPName($oField),
					'field-default'	=> $oField->getDefault() === null ? 'null' : $oField->getDefault()
				]);
			}

		// getter
			if(in_array('get', $aAccess) || in_array('_get', $aAccess))
			{
				$sAccess = in_array('get', $aAccess) ? 'public' : 'protected';

				if($oField->isModelType())
				{
					if($oField->isComponent())
					{
						$aModel['getters'] .= $oTpl->getSubTemplate('component-getter', [
							'access'		=> $sAccess,
							'field-name'	=> $this->getFieldName($oField),
							'p-field-name'	=> $this->getFieldPName($oField),
							'field-type'	=> $this->getFieldType($oField),
						]);
					}
					elseif($oField->isOneToMany())
					{
						$aModel['getters'] .= $oTpl->getSubTemplate('many-getter', [
							'access'		=> $sAccess,
							'model-name'	=> $oModel->getName(),
							'field-name'	=> $this->getFieldName($oField),
							'p-field-name'	=> $this->getFieldPName($oField),
							'field-type'	=> $this->getFieldType($oField),
						]);
					}
					else
					{
						$aModel['getters'] .= $oTpl->getSubTemplate('simple-getter', [
							'access'		=> $sAccess,
							'field-name'	=> $this->getFieldName($oField). 'Id',
							'p-field-name'	=> $this->getFieldPName($oField, 'i'). 'Id',
							'field-type'	=> $this->getFieldType($oField),
						]);

						$aModel['getters'] .= $oTpl->getSubTemplate('model-getter', [
							'access'		=> $sAccess,
							'field-name'	=> $this->getFieldName($oField),
							'p-field-name'	=> $this->getFieldPName($oField),
							'p-field-key'	=> $this->getFieldPName($oField, 'i'). 'Id',
							'field-type'	=> $this->getFieldType($oField),
						]);
					}
				}
				else
				{
					$aModel['getters'] .= $oTpl->getSubTemplate('simple-getter', [
						'access'		=> $sAccess,
						'field-name'	=> $this->getFieldName($oField),
						'p-field-name'	=> $this->getFieldPName($oField),
						'field-type'	=> $this->getFieldType($oField),
					]);
				}
			}

		// setter
			if(in_array('set', $aAccess) || in_array('_set', $aAccess))
			{
				$sAccess = in_array('set', $aAccess) ? 'public' : 'protected';

				if($oField->isModelType())
				{
					if(!$oField->isComponent() && !$oField->isOneToMany())
					{
						$aModel['setters'] .= $oTpl->getSubTemplate('obj-setter', [
							'access'		=> $sAccess,
							'field-name'	=> $this->getFieldName($oField). 'Id',
							'p-field-name'	=> $this->getFieldPName($oField, 'i'). 'Id',
							'o-field-name'	=> $this->getFieldPName($oField),
							'db-field-name'	=> strtolower($oModel->getAlias() .'_'. $oField->getName())
						]);
					}
				}
				else
				{
					$aModel['setters'] .= $oTpl->getSubTemplate('simple-setter', [
						'access'		=> $sAccess,
						'field-name'	=> $this->getFieldName($oField),
						'p-field-name'	=> $this->getFieldPName($oField),
						'field-type'	=> $this->getFieldType($oField),
						'db-field-name'	=> strtolower($oModel->getAlias() .'_'. $oField->getName())
					]);
				}
			}

		}

		echo $oTpl->getSubTemplate('main', $aModel);
		//var_dump($aModel);
	}

	protected function getFieldName(\ScaZF\Tool\Wrapper\Field $oField)
	{
		$aTmp = explode('_', $oField->getName());

		foreach($aTmp as &$sPart)
		{
			$sPart = ucfirst($sPart);
		}
		return implode('', $aTmp);
	}

	protected function getFieldType(\ScaZF\Tool\Wrapper\Field $oField)
	{
		if($oField->isModelType())
		{
			return $this->sGlobalNamespace . $oField->getModelType();
		}

		switch($oField->getType())
		{
			case 'char': return 'string';
			case 'uint': return 'int';
			default:
				return $oField->getType();
		}
	}

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
				return 's'. $sName;
			case 'int':
			case 'uint':
				return 'i'. $sName;
		}

		return '';
	}
}
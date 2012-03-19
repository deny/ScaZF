<?php

/**
 * @namespace
 */
namespace ScaZF\Tools\Validator;

/**
 * Schema validator
 *
 * @author	Daniel Kózka
 */
class Schema
{
	/**
	 * Validation messages
	 *
	 * @var	array
	 */
	protected $aErrors = array();

// PUBLIC METHODS

	/**
	 * Validate schema. Return true if success
	 *
	 * @return	bool
	 */
	public function isValid(Package $oPackage)
	{
		$this->aErrors = array();
		$this->checkPackage($oPackage);

		return empty($this->aMessages);
	}

	/**
	 * Return validation messages
	 *
	 * @var	array
	 */
	public function getMessages()
	{
		return $this->aMessages;
	}

// OTHER

	/**
	 * Check all package definition
	 *
	 * @param	Package	$oPackage	package definition
	 * @return	void
	 */
	protected function checkPackage(Package $oPackage)
	{
		$oTmp = new Model\Model();
		foreach($oPackage->getModels() as $oModel)
		{
			if(!$oTmp->isValid(array($oField)))
			{
				$this->addMsg(
					$oPackage->getName(),
					'general',
					$oTmp->getErrors()
				);
			}

			$this->checkModel($oModel);
		}
	}

	/**
	 * Check model definition
	 *
	 * @param	Model	$oModel	model definition
	 * @return	void
	 */
	protected function checkModel(Model $oModel)
	{
		$oTmp = new Field\Field();
		foreach($oModel->getFields() as $oField)
		{
			if(!$oTmp->isValid(array($oField)))
			{
				$this->addMsg(
					$oModel->getName(),
					$oField->getName(),
					$oTmp->getErrors()
				);
			}
		}
	}

	/**
	 * Add validation message for model
	 *
	 * @return	void
	 */
	protected function addMsg($sModel, $sType, $sMessage)
	{
		if(!isset($this->aMessages[$sModel]))
		{
			$this->aMessages[$sModel] = array();
		}

		if(!isset($this->aMessages[$sModel][$sType))
		{
			$this->aMessages[$sModel][$sType] = array();
		}

		$this->aMessages[$sModel][$sType][] = $sMessage;
	}
}

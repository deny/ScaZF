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
		$oVal = new Model();

		foreach($oPackage->getModels() as $oModel)
		{
			if(!$oVal->isValid(array($oField)))
			{
				$this->addMsg(
					$oPackage->getName(),
					'general',
					$oVal->getErrors()
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
		$oVal = new Field();

		foreach($oModel->getFields() as $oField)
		{
			if(!$oVal->isValid(array($oField)))
			{
				$this->addMsg(
					$oModel->getName(),
					$oField->getName(),
					$oVal->getErrors()
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
		if(!isset($this->aErrors[$sModel]))
		{
			$this->aErrors[$sModel] = array();
		}

		if(!isset($this->aErrors[$sModel][$sType))
		{
			$this->aErrors[$sModel][$sType] = array();
		}

		$this->aErrors[$sModel][$sType][] = $sMessage;
	}
}

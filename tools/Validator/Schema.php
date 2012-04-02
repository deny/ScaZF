<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator;

/**
 * Schema validator
 *
 * @author	Daniel Kózka
 */
class Schema
{
	use \ScaZF\Tool\Base\Screamer\ClassTrait;

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
	public function isValid(\ScaZF\Tool\Schema\Package $oPackage)
	{
		$this->scream('Start package "'. $oPackage->getName() .'" validate');
		$this->aErrors = array();
		$this->checkPackage($oPackage);
		$this->scream('End package "'. $oPackage->getName() .'" validate');

		return empty($this->aErrors);
	}

	/**
	 * Return validation messages
	 *
	 * @var	array
	 */
	public function getMessages()
	{
		return $this->aErrors;
	}

// OTHER

	/**
	 * Check all package definition
	 *
	 * @param	\ScaZF\Tool\Schema\Package	$oPackage	package definition
	 * @return	void
	 */
	protected function checkPackage(\ScaZF\Tool\Schema\Package $oPackage)
	{
		$oVal = new Model();

		foreach($oPackage->getModels() as $oModel)
		{
			$this->scream('Validate model: '. $oModel->getName(), 1);

			if(!$oVal->isValid(array($oModel)))
			{
				$this->addMsg(
					$oModel->getName(),
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
	 * @param	\ScaZF\Tool\Schema\Model	$oModel	model definition
	 * @return	void
	 */
	protected function checkModel(\ScaZF\Tool\Schema\Model $oModel)
	{
		$oVal = new Field();

		foreach($oModel->getFields() as $oField)
		{
			$this->scream('Validate field: '. $oField->getName(), 2);

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
	protected function addMsg($sModel, $sType, $mMessage)
	{
		$mMessage = is_array($mMessage) ? $mMessage : array($mMessage);

		if(!isset($this->aErrors[$sModel]))
		{
			$this->aErrors[$sModel] = array();
		}

		if(!isset($this->aErrors[$sModel][$sType]))
		{
			$this->aErrors[$sModel][$sType] = array();
		}

		$this->aErrors[$sModel][$sType] = array_merge($this->aErrors[$sModel][$sType], $mMessage);
	}
}

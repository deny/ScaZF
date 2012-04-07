<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator;

use ScaZF\Tool\Validator\ValidatorAbstract;
use ScaZF\Tool\Schema\Manager;

/**
 * Model validator
 *
 * @author	Daniel Kózka
 */
class Model extends ValidatorAbstract
{
	const WRONG_EXTEND = 'wrong-extend';
	const WRONG_COMPONENT = 'wrong-component';

	/**
	 * Constructor
	 *
	 * @return	ScaZF\Tool\Validator\Model
	 */
	public function __construct()
	{
		parent::__construct(1, array(
			self::WRONG_EXTEND 		=> 'Unrecognized extended model: {0}',
			self::WRONG_COMPONENT	=> 'Cannot find component class: {0}'
		));
	}

	/**
	 * Validation function
	 *
	 * @param	array	$aValues	validated values
	 * @return	void
	 */
	protected function validate(array $aValues)
	{
		$oModel = $aValues[0];
		if(!$oModel instanceof \ScaZF\Tool\Schema\Model)
		{
			throw \Exception('Validate value must be instance of Schema\Model');
		}

		try
		{
			if($oModel->hasExtends())
			{
				$oExtends = Manager::getInstance()->getModel($oModel->getExtends());
			}
		}
		catch(\ScaZF\Tool\Schema\Exception $oExc) // if ref model doesn't exist
		{
			$this->error(self::WRONG_EXTEND, $oModel->getExtends());
		}

		try
		{
			if($oModel->getComponent() != null)
			{
				$oComponent = Manager::getInstance()->getModel($oModel->getComponent());
			}
		}
		catch(\ScaZF\Tool\Schema\Exception $oExc) // if ref model doesn't exist
		{
			$this->error(self::WRONG_COMPONENT, $oModel->getComponent());
		}
	}
}
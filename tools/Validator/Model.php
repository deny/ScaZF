<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator;

use ScaZF\Tool\Validator\ValidatorAbstract;

/**
 * Model validator
 *
 * @author	Daniel Kózka
 */
class Model extends ValidatorAbstract
{
	const WRONG_EXTEND = 'wrong-extend';

	/**
	 * Constructor
	 *
	 * @return	ScaZF\Tool\Validator\Model
	 */
	public function __construct()
	{
		parent::__construct(1, array(
			self::WRONG_EXTEND => 'Unrecognized extended model: {0}'
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
			$oExtended = Manage::getInstance()->getModel($aValues[0]);
		}
		catch(Exception $oExc) // if ref model doesn't exist
		{
			$this->error(self::WRONG_EXTEND, $aValues[0]);
		}
	}
}
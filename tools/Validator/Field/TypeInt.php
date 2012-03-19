<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator\Field;

use ScaZF\Tool\Validator\ValidatorAbstract;

/**
 * Field type validator
 *
 * @author	Daniel Kózka
 */
class TypeInt extends ValidatorAbstract
{
	// error types
	const WRONG_MIN = 'wrong-min';
	const WRONG_MAX = 'wrong-max';
	const WRONG_MINMAX = 'wrong-minmax';

	/**
	 * Constructor
	 *
	 * @return	ScaZF\Tool\Validator\Field\TypeModel
	 */
	public function __construct()
	{
		parent::__construct(null, array(
			self::WRONG_MIN		=> 'Wrong int minimum definition: {0}',
			self::WRONG_MAX		=> 'Wrong int maximum definition: {0}',
			self::WRONG_MINMAX	=> 'Wrong int minimum and maximum definition: {0} < {1}'
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
		if(isset($aValues[0]) && !is_numeric($aValues[0]))
		{
			$this->error(self::WRONG_MIN, $aValues[0]);
		}

		if(isset($aValues[1]) && !is_numeric($aValues[1]))
		{
			$this->error(self::WRONG_MAX, $aValues[1]);
		}

		if(!$this->hasErrors() &&
		   !isset($aValues[0]) && !isset($aValues[0]) &&
			((int) $aValues[0] >= (int) $aValues[1])
		)
		{
			$this->error(self::WRONG_MINMAX, array($aValues[0], $aValues[1]));
		}
	}
}
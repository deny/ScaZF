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
	 * @return	\ScaZF\Tool\Validator\Field\TypeModel
	 */
	public function __construct()
	{
		parent::__construct(1, array(
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
		$oField = $aValues[0];
		if(!$oField instanceof \ScaZF\Tool\Schema\Field)
		{
			throw \Exception('Validate value must be instance of Schema\Field');
		}

		$aAttribs = $oField->getTypeAttribs();

		if(isset($aAttribs[0]) && !is_numeric($aAttribs[0]))
		{
			$this->error(self::WRONG_MIN, $aAttribs[0]);
		}

		if(isset($aAttribs[1]) && !is_numeric($aAttribs[1]))
		{
			$this->error(self::WRONG_MAX, $aAttribs[1]);
		}

		if(!$this->hasErrors() &&
		   !isset($aAttribs[0]) && !isset($aAttribs[0]) &&
			((int) $aAttribs[0] >= (int) $aAttribs[1])
		)
		{
			$this->error(self::WRONG_MINMAX, array($aAttribs[0], $aAttribs[1]));
		}
	}
}
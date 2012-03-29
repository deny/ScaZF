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
class TypeEnum extends ValidatorAbstract
{
	// error types
	const NO_VALUES = 'no-values';
	const MULTI_VALUES = 'multi-values';
	const WRONG_DEFAULT = 'wrong-default';

	/**
	 * Constructor
	 *
	 * @return	\ScaZF\Tool\Validator\Field\TypeModel
	 */
	public function __construct()
	{
		parent::__construct(1, array(
			self::NO_VALUES 	=> 'Enum values not found',
			self::MULTI_VALUES	=> 'Multiply enum values find',
			self::WRONG_DEFAULT	=> 'Unrecognized default value'
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

		if(empty($aAttribs))
		{
			$this->error(self::NO_VALUES);
		}

		if(count($aAttribs) != count(array_unique($aAttribs)))
		{
			$this->error(self::MULTI_VALUES);
		}

		$sDefault = $oField->getDefault();
		if(!empty($sDefault) && !in_array($sDefault, $aAttribs))
		{
			$this->error(self::WRONG_DEFAULT);
		}
	}
}
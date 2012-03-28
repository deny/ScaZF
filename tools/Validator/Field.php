<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator;

use ScaZF\Tool\Validator\ValidatorAbstract;

/**
 * Field validator
 *
 * @author	Daniel Kózka
 */
class Field extends ValidatorAbstract
{
	// error types
	const WRONG_TYPE 	= 'wrong-type';
	const WRONG_ACCESS	= 'wrong-access';
	const WRONG_OPTIONS	= 'wrong-options';

	/**
	 * Constructor
	 *
	 * @return	ScaZF\Tool\Validator\Field
	 */
	public function __construct()
	{
		parent::__construct(1, array(
			self::WRONG_TYPE	=> 'Unrecognized field type: {0}',
			self::WRONG_ACCESS	=> 'Unrecognized field access: {0}',
			self::WRONG_OPTIONS	=> 'Unrecognized field option: {0}'
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

	// type
		switch($oField->getType())
		{
			case 'serialize':
			case 'json':
				break;
			case 'int':
				$this->subValiadte($oField->getOptions(), new TypeInt()));
				break;
			case 'string':
				$this->subValiadte($oField->getOptions(), new TypeString()));
				break;
			default: // check if is model definition
				if(!oField->isModelType()) // if no model type
				{
					$this->error(self::WRONG_TYPE, $oField->getType());
				}

				$this->subValiadte(array($oField->getType(), $oField->getOptions()), new TypeModel()));
		}

	// access
		foreach($oField->getAccess() as $sAccess)
		{
			if(!in_array($sAccess, array('get', 'set'))
			{
				$this->error(self::WRONG_ACCESS, $sAccess);
			}
		}

	// options
		foreach($oField->getOptions() as $sOption)
		{
			if(!in_array($sAccess, array('unsigned', 'unique', 'index', 'null'))
			{
				$this->error(self::WRONG_OPTIONS, $sOption);
			}
		}
	}
}
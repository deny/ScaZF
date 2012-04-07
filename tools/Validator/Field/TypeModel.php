<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator\Field;

use ScaZF\Tool\Validator\ValidatorAbstract;
use ScaZF\Tool\Schema\Manager;

/**
 * Field type validator
 *
 * @author	Daniel Kózka
 */
class TypeModel extends ValidatorAbstract
{
	// error types
	const NO_MODEL 	= 'no-model';
	const WRONG_OPT = 'wrong-opt';
	const OPT_COUNT	= 'opt-count';

	/**
	 * Constructor
	 *
	 * @return	ScaZF\Tool\Validator\Field\TypeModel
	 */
	public function __construct()
	{
		parent::__construct(1, array(
			self::NO_MODEL		=> 'Can\'t find "{0}" model. Check package "use" attribute.',
			self::WRONG_OPT		=> 'Wrong type options "{0}". use (0), (1) or (*).',
			self::OPT_COUNT 	=> 'Wrong type options count ({0}) - only one option allowed'
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

		try
		{
			$oModel = Manager::getInstance()->getModel($oField->getType());
			$aAttribs = $oField->getTypeAttribs();

			if(count($aAttribs) > 1)
			{
				$this->error(self::OPT_COUNT, count($aAttribs));
			}
			else if(!empty($aAttribs) && !in_array($aAttribs[0], array('*')))
			{
				$this->error(self::WRONG_OPT, $aAttribs[0]);
			}
		}
		catch(\ScaZF\Tool\Schema\Exception $oExc) // if ref model doesn't exist
		{
			$this->error(self::NO_MODEL, $oField->getType());
		}
	}
}
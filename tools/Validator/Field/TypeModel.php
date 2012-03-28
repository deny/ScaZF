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
	const NO_OPT	= 'no-opt';
	const WRONG_OPT = 'wrong-opt';
	const OPT_COUNT	= 'opt-count';

	/**
	 * Constructor
	 *
	 * @return	ScaZF\Tool\Validator\Field\TypeModel
	 */
	public function __construct()
	{
		parent::__construct(2, array(
			self::NO_MODEL		=> 'Can\'t find "{0}" model. Check package "use" attribute.',
			self::NO_OPT 		=> 'Can\'t find type options - use (0), (1) or (*).',
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
		try
		{
			$oModel = Manager::getInstance()->getModel($aValues[0]);

			if(empty($aValues[1]))
			{
				$this->error(self::NO_OPT);
			}
			else if(count($aValues[1]) > 1)
			{
				$this->error(self::OPT_COUNT, count($aValues[1]));
			}
			else if(!in_array($aValues[1][0], array('0', '1', '*')))
			{
				$this->error(self::WRONG_OPT, $aValues[1][0]);
			}
		}
		catch(\ScaZF\Tool\Schema\Exception $oExc) // if ref model doesn't exist
		{
			$this->error(self::NO_MODEL, $aValues[0]);
		}
	}
}
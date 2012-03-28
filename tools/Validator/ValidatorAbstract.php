<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Validator;

/**
 * Abstract validator
 *
 * @author	Daniel Kózka
 */
abstract class ValidatorAbstract
{
	/**
	 * Input values count
	 *
	 * @var	int
	 */
	private $iValCount = 1;

	/**
	 * Message templates
	 *
	 * @var	array
	 */
	private $aMessages = array();

	/**
	 * Validation messages
	 *
	 * @var	array
	 */
	private $aErrors = array();

	/**
	 * Constructor
	 *
	 * @param	int		$iValCount	input values count
	 * @param	array	$aMessages	message templates
	 * @return	ScaZF\Tool\Validator\ValidatorAbstract
	 */
	public function __construct($iValCount, $aMessages)
	{
		$this->iValCount = $iValCount;
		$this->aMessages = $aMessages;
	}

	/**
	 * Check if value valid
	 *
	 * @param	array	$aValues	validated values
	 * @return	bool
	 */
	public function isValid(array $aValues)
	{
		if(isset($this->iValCount) && count($aValues) != $this->iValCount)
		{
			throw new \Exception('Wrong validate values count');
		}

		$this->aErrors = array();
		$this->validate($aValues);

		return $this->hasErrors();
	}

	/**
	 * Return validation messages
	 *
	 * @var	array
	 */
	public function getErrors()
	{
		return $this->aErrors;
	}

	/**
	 * Is validator has error messages
	 *
	 * @var	bool
	 */
	public function hasErrors()
	{
		return empty($this->aErrors);
	}

// OTHER

	/**
	 * Validation function
	 *
	 * @param	array	$aValues	validated values
	 * @return	void
	 */
	protected abstract function validate(array $aValues);

	/**
	 * Use validator and add messages from it
	 *
	 * @param	array				$aValues	validated values
	 * @param	ValidatorAbstract	$oValidator	validator object
	 * @return	void
	 */
	protected function subValiadte($mValues, $oValidator)
	{
		$mValues = is_array($mValues) ? $mValues : array($mValues);

		if(!$oValidator->isValid($mValues))
		{
			$this->appendError($oValidator->getErrors());
		}
	}

	/**
	 * Add error message
	 *
	 * @param	string	$sType		error message type
	 * @param	mixed	$mParams	error parameters
	 */
	protected function error($sType, $mParams = '')
	{
	// error template
		if(!isset($this->aMessages[$sType]))
		{
			throw new \Exception('No error message for "'. $sType .'" error');
		}
		$sMsg = $this->aMessages[$sType];

	// additional parameters
		if(!empty($mParams))
		{
			$mParams = is_array($mParams) ? array_values($mParams) : array($mParams);
			foreach($mParams as $iKey => $sValue)
			{
				$sMsg = str_replace('{'. $iKey .'}', $sValue, $sMsg);
			}
		}

		$this->aErrors[] = $sMsg;
	}

	/**
	 * Append errors
	 *
	 * @param	array	$aErrors	errors array
	 * @return	void
	 */
	protected function appendError(array $aErrors)
	{
		$this->aErrors = array_merge($this->aErrors, $aErrors);
	}
}
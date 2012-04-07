<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Db\Wrapper;

/**
 * Field wrapper
 *
 * @author Daniel Kózka
 */
class Field
{
	/**
	 * Field definition
	 *
	 * @var	\ScaZF\Tool\Schema\Field
	 */
	protected $oField;

	protected $bOneToMany = null;
	protected $bComponent = null;

	/**
	 * Constructor
	 *
	 * @param	\ScaZF\Tool\Schema\Field	$oField		field definition
	 * @return	\ScaZF\Tool\Db\Wrapper\Field
	 */
	public function __construct(\ScaZF\Tool\Schema\Field $oField)
	{
		$this->oField = $oField;
	}

	public function isOneToMany()
	{
		$this->processType();
		return $this->bOneToMany;
	}

	public function isComponent()
	{
		$this->processType();
		return $this->bComponent;
	}

	/**
	* Field methods call
	*
	* @param	string	$sName	method name
	* @param 	array	$aArgs 	method arguments
	* @return 	mixed
	*/
	public function __call($sName, $aArgs)
	{
		return call_user_func_array(
			array($this->oField, $sName),
			$aArgs
		);
	}

	/**
	 * Check field type (if is one-to-meny or component)
	 *
	 * @return	void
	 */
	protected function processType()
	{
		if($this->bOneToMany === null)
		{
			if($this->oField->isModelType()) // model typ field
			{
				$oModel = new \ScaZF\Tool\Db\Wrapper\Model(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($oField->getType())
				);

				$this->bComponent = $oModel->getComponent() !== null;

				$aAttr = $this->oField->getTypeAttr();
				$this->bOneToMany = !empty($aAttr) && $aAttr[0] == '*';
			}
			else // this is simple field
			{
				$this->bOneToMany = false;
				$this->bComponent = false;
			}
		}
	}
}
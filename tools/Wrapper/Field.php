<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Wrapper;

/**
 * Field wrapper
 *
 * @author Daniel Kózka
 */
class Field
{

	/**
	 * calcualated field id
	 */
	protected $iId = null;

	/**
	 * Model definition
	 *
	 * @var	\ScaZF\Tool\Schema\Model
	 */
	protected $oModel;

	/**
	 * Field definition
	 *
	 * @var	\ScaZF\Tool\Schema\Field
	 */
	protected $oField;

	/**
	 * Is one-to-many field
	 *
	 * @var	bool
	 */
	protected $bOneToMany = null;

	/**
	 * Is component field
	 *
	 * @var	bool
	 */
	protected $bComponent = null;

	/**
	 * Constructor
	 *
	 * @param	\ScaZF\Tool\Wrapper\Model	$oModel		model definition
	 * @param	\ScaZF\Tool\Schema\Field	$oField		field definition
	 * @return	\ScaZF\Tool\Wrapper\Field
	 */
	public function __construct(\ScaZF\Tool\Wrapper\Model $oModel, \ScaZF\Tool\Schema\Field $oField)
	{
		$this->oModel = $oModel;
		$this->oField = $oField;
	}

	/**
	 * Return field identificator
	 *
	 * @return	string
	 */
	public function getId()
	{
		if($this->iId === null)
		{
			$this->iId = sha1($this->oModel->getAlias() .'-'. $this->getName());
		}

		return $this->iId;
	}

	/**
	 * Return table with field description for SQL generator
	 *
	 * @return	array
	 */
	public function getDescription()
	{
		$aResult = [];

		if($this->isOneToMany()) // if current field is one-to-many field
		{
		// technical table needed
			$sTable = $this->oModel->getTableName() .'_j_'. strtolower($this->getName());

			$aTmp = explode('_', $sTable);

			$sAlias = '';
			foreach($aTmp as $sPart)
			{
				$sAlias .= $sPart[0];
			}

		// define technical table
			$aResult['techTable'] = [
				'name' 		=> $sTable,
				'fields'	=> [
					['name' => $this->oModel->getKey(), 'type' => 'INT(10)', 'other' => 'UNSIGNED'],
					['name' => $sAlias . '_id',			'type' => 'INT(10)', 'other' => 'UNSIGNED']
				],
				'other' => [
					'UNIQUE KEY `'. $this->oModel->getKey() .'` (`'. $this->oModel->getKey() .'`,`'. $sAlias . '_id' .'`)',
					'KEY `'. $sAlias . '_id' .'` (`'. $sAlias . '_id' .'`)'
				]
			];

		// define FK
			$oTmp = new \ScaZF\Tool\Wrapper\Model(
				\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->getType())
			);

			$aResult['foreignKeys'][$sTable] = [
				$this->oModel->getKey()	=> ['table' => $this->oModel->getTableName(), 'field' => $this->oModel->getKey()],
				$sAlias . '_id'			=> ['table' => $oTmp->getTableName(), 'field' => $oTmp->getKey()]
			];
		}
		elseif(!$this->isComponent()) // standard field
		{
			$sName = strtolower($this->oModel->getAlias() .'_'. $this->getName());
			$aAttr = $this->getTypeAttribs();

			$aResult['field'] = ['name' => $sName];
			switch($this->getType())
			{
				case 'int':
					$aResult['field']['type'] = 'INT(11)';
					$aResult['field']['other'] = '';
					break;
				case 'uint':
					$aResult['field']['type'] = 'INT(10)';
					$aResult['field']['other'] = 'UNSIGNED';
					break;
				case 'char':
					$aResult['field']['type'] = 'CHAR('. $aAttr[0] .')';
					$aResult['field']['other'] = '';
					break;
				case 'string':
					$aResult['field']['type'] = (empty($aAttr) ? 'TEXT' : 'VARCHAR('. (int) $aAttr[0] . ')');
					$aResult['field']['other'] = '';
					break;
				case 'enum':
					$aResult['field']['type'] = 'ENUM("'. implode('","', $aAttr) .'")';
					$aResult['field']['other'] = '';
					break;
				default: // model type
					$oTmp = new \ScaZF\Tool\Wrapper\Model(
						\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->getType())
					);

					// referenced model isn't component or one-to-many
					if($oTmp->getComponent() === null && empty($aAttr))
					{
						$aResult['field']['type'] = 'INT(10)';
						$aResult['field']['other'] = 'UNSIGNED';

						$aResult['foreignKeys'][$this->oModel->getTableName()] = [
							$sName	=> ['table' => $oTmp->getTableName(), 'field' => $oTmp->getKey()]
						];
					}
					else
					{
						unset($aResult['field']);
					}
			}
		}

		return $aResult;
	}

	/**
	 * Check if field is one-to-many field
	 */
	public function isOneToMany()
	{
		$this->processType();
		return $this->bOneToMany;
	}

	/**
	 * Check if field is component field
	 *
	 * @var	bool
	 */
	public function isComponent()
	{
		$this->processType();
		return $this->bComponent;
	}

	public function getModelType()
	{
		return '\\'. $this->oModel->getPackage() .'\\'. $this->getType();
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
	 * Check field type (if is one-to-many or component)
	 *
	 * @return	void
	 */
	protected function processType()
	{
		if($this->bOneToMany === null)
		{
			if($this->oField->isModelType()) // model typ field
			{
				$oModel = new \ScaZF\Tool\Wrapper\Model(
					\ScaZF\Tool\Schema\Manager::getInstance()->getModel($this->oField->getType())
				);

				$this->bComponent = $oModel->getComponent() !== null;

				$aAttr = $this->oField->getTypeAttribs();
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
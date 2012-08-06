<?php

require_once 'AbstractTask.php';

/**
 * Init generate tasks
 */
class Generator extends AbstractTask
{
	protected $sAction = null;

	/**
	 * (non-PHPdoc)
	 * @see Task::main()
	 */
	public function main()
	{
		switch($this->sAction)
		{
			case 'sql':
				self::$oGen->sql(self::$sPackage, self::$sModel);
				break;
			case 'model-base':
				self::$oGen->modelBase(self::$sPackage, self::$sModel);
				break;
			case 'model':
				self::$oGen->model(self::$sPackage, self::$sModel);
				break;
			case 'crud':
				self::$oGen->crud(self::$sPackage, self::$sModel);
				break;
			case 'all':
				self::$oGen->sql(self::$sPackage, self::$sModel);
				self::$oGen->modelBase(self::$sPackage, self::$sModel);
				self::$oGen->model(self::$sPackage, self::$sModel);
				self::$oGen->crud(self::$sPackage, self::$sModel);
				break;
		}
	}

	public function setAction($sVal)
	{
		$this->sAction = $sVal;
	}
}
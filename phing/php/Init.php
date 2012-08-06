<?php

require_once 'AbstractTask.php';

/**
 * Init generate tasks
 */
class Init extends AbstractTask
{
	protected $sSchemaPath = null;
	protected $sControllersPath = null;
	protected $sModelsPath = null;
	protected $sSqlPath = null;

	/**
	 * (non-PHPdoc)
	 * @see Task::main()
	 */
	public function main()
	{
		self::$oGen = new \ScaZF\Tool\Generator(
			$this->sSchemaPath,
			$this->sSqlPath,
			$this->sModelsPath,
			$this->sControllersPath
		);
	}

	/**
	 * Set schema path
	 *
	 * @return	void
	 */
	public function setSchemaPath($sVal)
	{
		$this->sSchemaPath = $sVal;
	}

	/**
	 * Set controllers path
	 *
	 * @return	void
	 */
	public function setControllersPath($sVal)
	{
		$this->sControllersPath = $sVal;
	}

	/**
	 * Set models path
	 *
	 * @return	void
	 */
	public function setModelsPath($sVal)
	{
		$this->sModelsPath = $sVal;
	}

	/**
	 * Set SQL path
	 *
	 * @return	void
	 */
	public function setSqlPath($sVal)
	{
		$this->sSqlPath = $sVal;
	}

	/**
	 * Set package
	 *
	 * @return	void
	 */
	public function setPackage($sVal)
	{
		self::$sPackage = $sVal;
	}

	/**
	 * Set model
	 *
	 * @return	void
	 */
	public function setModel($sVal)
	{
		self::$sModel = empty($sVal) ? null : $sVal;
	}
}
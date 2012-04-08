{@begin=main@}
<?php

/**
 * @namespace
 */
namespace {*namespace*};

/**
 * Base trait
 */
trait {*model-name*}
{
	{*consts*}

// INITIALIZATION

	{*initialization*}

	public function init(array &$aRow, array &$aComponents = [])
	{
		$this->sName = $aRow['u_name'];
		$this->sSurname = $aRow['u_surname'];

		$aComponents[] = self::info();
		parent::init($aRow, $aComponents);


		if(isset($aRow['_settings']))
		{
			$this->oSettings = $aRow['_settings'] ?
									$aRow['_settings'] :
									(new \Model\Users\Settings())->initDefault($this);
		}

		if(isset($aRow['_friends']))
		{
			$this->aFriends = $aRow['_friends'];
		}

		return $this;
	}

// GETTERS

	{*getters*}

// SETTERS

	{*setters*}

// STATIC

	public static function info()
	{
		return [
			'table' => '{*db-table*}',
			'alias'	=> '{*db-alias*}',
			'key'	=> '{*db-key*}'
		];
	}
}
{@end=main@}
{@begin=const@}
	CONST {*name*} = {*value*};
{@end=const@}
{@begin=field@}
	/**
	 * @var	{*field-type*}
	 */
	private ${*p-field-name*} = {*field-default*};

{@end=field@}
{@begin=simple-getter@}
	/**
	 * @return	{*field-type*}
	 */
	public function get{*field-name*}()
	{
		return $this->{*p-field-name*};
	}

{@end=simple-getter@}
{@begin=component-getter@}
	/**
	 * @return	{*field-type*}
	 */
	public function get{*field-name*}()
	{
		if(!isset($this->{*p-field-name*}))
		{
			try
			{
				$this->{*p-field-name*} = {*field-type*}Factory::getInstance()->getOne($this->getId());
			}
			catch(\Sca\DataObject\Exception $oExc) // no settings - create default setting object
			{
				$this->{*p-field-name*} = (new {*field-type*}())->initDefault($this);
			}
		}

		return $this->{*p-field-name*};
	}

{@end=component-getter@}
{@begin=model-getter@}
	/**
	 * @return	{*field-type*}
	 */
	public function get{*field-name*}()
	{
		if(!isset($this->{*p-field-name*}))
		{
			$this->{*p-field-name*} = {*field-type*}Factory::getInstance()->getOne($this->{*p-field-key*});
		}

		return $this->{*p-field-name*};
	}

{@end=model-getter@}
{@begin=many-getter@}
	/**
	 * @return	array
	 */
	public function get{*field-name*}()
	{
		if(!isset($this->{*p-field-name*}))
		{
			$this->{*p-field-name*} = {*field-type*}Factory::getInstance()->get{*model-name*}{*field-name*}($this->getId());
		}

		return $this->{*p-field-name*};
	}

{@end=many-getter@}
{@begin=simple-setter@}
	/**
	 * @param	{*field-type*}	$mValue		new value
	 * @return	void
	 */
	public function set{*field-name*}($mValue)
	{
		$this->{*p-field-name*} = $mValue;
		$this->setDataValue(self::info()['table'], '{*db-field-name*}', $mValue);
		return $this;
	}

{@end=simple-setter@}
{@begin=main-init@}
	/**
	 * Model initialziation
	 *
	 * @param	array	$aRow			row from DB
	 * @param	array	$aComponents	components desc
	 */
	public function init(array &$aRow, array &$aComponents = [])
	{
		{*init-fields}

		$aComponents[] = self::info();
		parent::init($aRow, $aComponents);

		{*init-components*}
		{*init-many*}

		return $this;
	}

{@end=main-init@}
{@begin=extend-init@}
	/**
	 * Model initialziation
	 *
	 * @param	array	$aRow			row from DB
	 * @param	array	$aComponents	components desc
	 */
	public function init(array &$aRow, array &$aComponents = [])
	{
		$aComponents[] = self::info();
		parent::init($aRow, $aComponents);

		{*init-fields}

		{*init-components*}
		{*init-many*}

		return $this;
	}

{@end=extend-init@}
{@begin=def-init@}
	/**
	 * Model default initialziation
	 *
	 * @param	{*owner-type*}	$oOwner	owner
	 */
	public function initDefault({*owner-type*} $oOwner)
	{
		$aComponents = [self::info()];
		$aTmp = [
			{*model-key*}		=> $oOwner->getId(),
			{*init-fields*}
		];
		parent::initDefault($aTmp, $aComponents);
		return $this;
	}

{@end=def-init@}
{@begin=init-field@}
		$this->{*p-field-name*} = $aRow['{*db-field-name*}'];
{@end=init-field@}
{@begin=init-field-component@}
			'{*db-field-name*}' => $this->{*p-field-name*}
{@end=init-field-component@}
{@begin=init-component@}
		if(isset($aRow['{*preload*}']))
		{
			$this->{*p-field-name*} = $aRow['{*preload*}'] ?
									$aRow['{*preload*}'] :
									(new {*component-type*}())->initDefault($this);
		}

{@end=init-component@}
{@begin=init-many@}
		if(isset($aRow['{*preload*}']))
		{
			$this->{*p-field-name*} = $aRow['{*preload*}'];
		}

{@end=init-many@}
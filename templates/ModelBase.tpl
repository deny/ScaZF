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

// FIELDS

{*fields*}
// INITIALIZATION

{*initialization*}
// GETTERS

{*getters*}
// SETTERS

{*setters*}
// STATIC

	/**
	 * Return model DB information
	 *
	 * @return	array
	 */
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
	// CONST {*name*} = '{*value*}';
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
	{*access*} function get{*field-name*}()
	{
		return $this->{*p-field-name*};
	}

{@end=simple-getter@}
{@begin=component-getter@}
	/**
	 * @return	{*field-type*}
	 */
	{*access*} function get{*field-name*}()
	{
		if(!isset($this->{*p-field-name*}))
		{
			try
			{
				$this->{*p-field-name*} = {*field-type*}Factory::getInstance()->getOne($this->getId());
			}
			catch(\Sca\DataObject\Exception $oExc) // no data - create default object
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
	{*access*} function get{*field-name*}()
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
	{*access*} function get{*field-name*}()
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
	{*access*} function set{*field-name*}($mValue)
	{
		$this->{*p-field-name*} = $mValue;
		$this->setDataValue(self::info()['table'], '{*db-field-name*}', $mValue);
		return $this;
	}

{@end=simple-setter@}
{@begin=obj-setter@}
	/**
	 * @param	{*field-type*}	$mValue		new value
	 * @return	void
	 */
	{*access*} function set{*field-name*}($mValue)
	{
		$this->{*p-field-name*} = $mValue;
		$this->{*o-field-name*} = null;
		$this->setDataValue(self::info()['table'], '{*db-field-name*}', $mValue);
		return $this;
	}

{@end=obj-setter@}
{@begin=main-init@}
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

{*init-fields*}

{*init-components*}
{*init-preload*}

		return $this;
	}

{@end=main-init@}
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
			'{*current-key*}'	=> $oOwner->getId(),
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
			'{*db-field-name*}' => $this->{*p-field-name*},
{@end=init-field-component@}
{@begin=init-component@}
		if(isset($aRow['_{*preload*}']))
		{
			$this->{*p-field-name*} = $aRow['_{*preload*}'] ?
									$aRow['_{*preload*}'] :
									(new {*component-type*}())->initDefault($this);
		}

{@end=init-component@}
{@begin=init-preload@}
		if(isset($aRow['_{*preload*}']))
		{
			$this->{*p-field-name*} = $aRow['_{*preload*}'];
		}

{@end=init-preload@}

{@begin=main@}
<?php

/**
 * @namespace
 */
namespace {*namespace*};

/**
 * Factory base trait
 */
trait {*model-name*}Factory
{
	use \Sca\DataObject\Singleton;

	/**
	 * Factory initialization
	 *
	* @param	array	$aComponents	components descritpion
	 * @return	void
	 */
	protected function init(array &$aComponents = [])
	{
		$aComponents[] = {*model-type*}::info();
		parent::init($aComponents);
	}

// CREATE METHODS

{*create*}
{*prepare-create*}

// FACTORY METHODS

{*factory*}

// OTHER

	/**
	 * Build new model object
	 *
	 * @return	{*model-type*}
	 */
	public function buildElement()
	{
		return new {*model-type*}();
	}

{*get-select*}
{*build-list*}
{*prepare-build*}
}
{@end=main@}
{@begin=create@}
	/**
	 * Create object
	 *
{*fields-comments*}
	 * @return	{*model-type*}
	 */
	public function create({*fields-list*})
	{
		$aData = $this->prepareToCreate([{*fields-list*}]);

		return $this->createNewElement($aData);
	}

{@end=create@}
{@begin=create-comment@}
	 * @param	{*field-type*}	{*p-field-name*}
{@end=create-comment@}
{@begin=prepare-create-field@}
				'{*db-field*}' => $aData[{*db-field-nr*}]
{@end=prepare-create-field@}
{@begin=prepare-create-simple@}
	/**
	 * Prepare data to create
	 *
	 * @param	array	$aData	model data
	 * @return	array
	 */
	protected function prepareToCreate(array $aData)
	{
		return ['{*db-table*}' => [
{*db-fields*}
		]];
	}

{@end=prepare-create-simple@}
{@begin=prepare-create-extend@}
	/**
	 * Prepare data to create
	 *
	 * @param	array	$aData	model data
	 * @return	array
	 */
	protected function prepareToCreate(array $aData)
	{
		$aParent = parent::prepareToCreate($aData);

		$aParent['{*db-table*}'] = [
{*db-fields*}
		];

		return $aParent;
	}

{@end=prepare-create-extend@}
{@begin=prepare-create-component@}
	/**
	 * Prepare data to create (empty in component)
	 *
	 * @param	array	$aData	model data
	 * @return	array
	 */
	protected function prepareToCreate(array $aData)
	{
	}

{@end=prepare-create-component@}
{@begin=get-multi@}
	/**
	 * Return data for one-to-many field
	 *
	 *@param	mixed	$mId	owner id/ids
	 * @return	array
	 */
	public function get{*model-name*}{*field-name*}($mId)
	{
		if(empty($mId))
		{
			return array();
		}

		$aInfo = {*other-type*}::info();
		$aThis = {*current-type*}::info();
		$oSelect = $this->getSelect()
							->join(
								'{*db-tech-table*} AS {*db-tech-alias*}',
								'{*db-tech-alias*}.{*db-tech-key*} = '. $aThis['alias'] .'.'. $aThis['key'],
								''
							);

		if(is_array($mId))
		{
			$oSelect->where('{*db-tech-alias*}.'. $aInfo['key'] .' IN(?)', $mId);
			$oSelect->group($aThis['alias'] .'.'. $aThis['key']);
			$oSelect->columns(new \Zend_Db_Expr('GROUP_CONCAT({*db-tech-alias*}.'. $aInfo['key'] .') AS _j'));
		}
		else
		{
			$oSelect->where('{*db-tech-alias*}.'. $aInfo['key'] .' = ?', $mId);
		}

		$aDbRes = $oSelect->query()->fetchAll();

		$aResult = null;
		if(is_array($mId))
		{
			$aResult = array_fill_keys($mId, []);
			foreach($aDbRes as $aRow)
			{
				$oTmp = $this->buildObject($aRow);
				foreach(explode(',', $aRow['_j']) as $iId)
				{
					$aResult[$iId][] = $oTmp;
				}
			}
		}
		else
		{
			$aResult = $this->buildList($aDbRes);
		}

		return $aResult;
	}

{@end=get-multi@}
{@begin=get-select@}
	/**
	 * Return select object for model
	 *
	 * @param	mixed	$mFields	fields definition
	 * @param	array	$aOptions	other options
	 * @return	\Zend_Db_Select
	 */
	protected function getSelect($mFields = '*', array $aOptions = [])
	{
		$oSelect = parent::getSelect($mFields, $aOptions);

{*field-preload*}

		return $oSelect;
	}

{@end=get-select@}
{@begin=get-select-component@}
		if(in_array('{*preload*}', $aOptions)) // component preload
		{
			$aThis = {*current-type*}::info();
			$aInfo = {*other-type*}::info();
			$oSelect->joinLeft(
				$aInfo['table'] .' AS '. $aInfo['alias'],
				$aInfo['alias'] .'.'. $aInfo['key'] .' = '. $aThis['alias'] .'.'. $aThis['key']
			);
		}

{@end=get-select-component@}
{@begin=get-select-preload@}
		if(in_array('{*preload*}', $aOptions)) // zawiera pole
		{
			$aThis = {*current-type*}::info();
			$aInfo = {*other-type*}::info();
			$oSelect->join(
				$aInfo['table'] .' AS '. $aInfo['alias'],
				$aInfo['alias'] .'.'. $aInfo['key'] .' = '. $aThis['alias'] .'.d_user'
			);
		}

{@end=get-select-preload@}
{@begin=build-list@}
	/**
	 * Build model list
	 *
	 * @param	array	$aDbResult	database result
	 * @return	array
	 */
	protected function buildList(array &$aDbResult, array $aOptions = [])
	{
		if(empty($aDbResult))
		{
			return array();
		}

{*field-preload*}

		return parent::buildList($aDbResult, $aOptions);
	}

{@end=build-list@}
{@begin=build-list-many@}
		if(in_array('{*preload*}', $aOptions))
		{
			$aIds = [];
			foreach($aDbResult as $aRow)
			{
				$aIds[] = $aRow[{*current-type*}::info()['key']];
			}

			$aTmp = {*other-type*}Factory::getInstance()->get{*model-name*}{*field-name*}($aIds);

			foreach($aDbResult as &$aRow)
			{
				$aRow['_{*preload*}'] = $aTmp[$aRow[{*current-type*}::info()['key']]];
			}
		}

{@end=build-list-many@}
{@begin=prepare-build@}
	/**
	 * Prepare data to build
	 *
	 * @param	array	$aRow		db row
	 * @param	array	$aOptions	build options
	 * @return	void
	 */
	protected function prepareToBuild(array &$aRow, array $aOptions = [])
	{
{*field-preload*}
	}

{@end=prepare-build@}
{@begin=prepare-build-component@}
		if(in_array('{*preload*}', $aOptions)) // component preload
		{
			if(isset($aRow[{*other-type*}::info()['key']]))
			{
				$aRow['_{*preload*}'] = (new {*other-type*}())->init($aRow);
			}
			else
			{
				$aRow['_{*preload*}'] = false;
			}
		}

{@end=prepare-build-component@}
{@begin=prepare-build-model@}
		if(in_array('{*preload*}', $aOptions)) // preload standard field
		{
			$aRow['_{*preload*}'] = (new {*other-type*}())->init($aRow);
		}

{@end=prepare-build-model@}

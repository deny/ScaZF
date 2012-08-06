{@begin=main@}
<?php

class {*controller*}Controller extends Sca_Controller_Action
{
	/**
	 * Allowed sort types
	 *
	 * @var	array
	 */
	protected $aAllowSort = [
{*sort-types*}
	];

	/**
	 * Init
	 */
	public function init()
	{
		$this->prepareController(
			'{*controller-url*}',
			{*model*}Factory::getInstance(),
			10
		);

		parent::init();
	}

	/**
	 * Display items list
	 */
	public function listAction()
	{
	// page number
		$iPage = $this->_request->getParam('page', 1);
		if($iPage < 1)
		{
			$this->moveTo404();
		}

	// sort
		$sSort = $this->_request->getParam('sort');
		$sDbSort = current($this->aAllowSort) . ' ASC';
		$aUsedSort = array(0,0);
		if(!empty($sSort))
		{
			$aSort = explode(':', $sSort);
			if(count($aSort) == 2 && isset($this->aAllowSort[$aSort[0]]))
			{
				$sDbSort = $this->aAllowSort[$aSort[0]] . ($aSort[1] == 'desc' ? ' DESC' : ' ASC');
				$aUsedSort = $aSort;
			}
		}

	// get paginator
		$oPaginator = $this->getPaginator($iPage, $sDbSort);

	// set view
		$this->view->assign('oPaginator', $oPaginator);
		$this->view->assign('aParams', array(
			'page'	=> $iPage == 1 ? null : $iPage,
			'sort'	=> empty($sSort) ? null : $sSort
		));
		$this->view->assign('sAction', 'list');
		$this->view->assign('aUsedSort', $aUsedSort);
	}

	/**
	 * Adds item to db
	 */
	public function addAction()
	{
		$this->_helper->viewRenderer('form');
		$this->view->assign('bEdit', false);

		if($this->_request->isPost())
		{
			$oFilter = $this->getFilter(false);

			if($oFilter->isValid())
			{
				$aData = $oFilter->getEscaped();

				$this->oFactory->create(
{*create-list*}
				);

				$this->addMessage('Item successful added', self::MSG_OK);
				$this->_redirect($this->getUrl([], 'list'));
				exit();
			}

			$this->addMessage('Correct wrong fields', self::MSG_ERROR);
			$this->showFormMessages($oFilter);
		}
	}

	/**
	 * Edit item
	 */
	public function editAction()
	{
		$this->_helper->viewRenderer('form');
		$this->view->assign('bEdit', true);

		$oItem = $this->getItem();

		if($this->_request->isPost())
		{
			$oFilter = $this->getFilter(true);

			if($oFilter->isValid())
			{
				$aData = $oFilter->getEscaped();

{*set-list*}
				$oItem->save();

				$this->addMessage('Item successful changed', self::MSG_OK);
				$this->_redirect($this->getUrl([], 'list'));
				exit();
			}

			$this->addMessage('Correct wrong fields', self::MSG_ERROR);
			$this->showFormMessages($oFilter);
		}
		else
		{
			$this->view->assign('aValues', [
{*init-values*}
			]);
		}
	}

	/**
	 * Delete item
	 */
	public function deleteAction()
	{
		$oItem = $this->getItem();
		$oItem->delete();

		$this->addMessage('Delete successful', self::MSG_OK);

		$this->_redirect($this->getUrl([], 'list'));
	}

	/**
	 * Return filter
	 *
	 * @param	bool	$bEdit
	 * @return	Zend_Filter_Input
	 */
	protected function getFilter($bEdit)
	{
		$aValues = $this->_request->getPost();

    	// validators
		$aValidators = [
{*validators-edit*}
		];

		if(!$bEdit) // if add
		{
{*validators-add*}
		}

		$aFitlers = [
			'*' => 'StringTrim'
		];

		// filter
		return new Zend_Filter_Input($aFitlers, $aValidators, $aValues);
	}
}
{@end=main@}
{@begin=sort-types@}
		'{*name*}' => '{*db-name*}',
{@end=sort-types@}
{@begin=create-list@}
					$aData['{*name*}']
{@end=create-list@}
{@begin=set-list@}
				$oItem->{*method*}($aData['{*name*}']);
{@end=set-list@}
{@begin=init-values@}
				'{*name*}' => $oItem->{*method*}()
{@end=init-values@}
{@begin=validators-edit@}
			'{*name*}' => [
				{*validators*}
			]
{@end=validators-edit@}
{@begin=validators-add@}
			$aValidators['{*name*}'] = [
				{*validators*}
			];
{@end=validators-add@}
{@begin=val-str-len@}
				new Core_Validate_StringLength(['min' => 1, 'max' => {*max*}]),
{@end=val-str-len@}

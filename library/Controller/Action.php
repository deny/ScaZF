<?php

/**
 * Extended CRUD controller
 *
 * @author	Daniel Kózka
 */
class Sca_Controller_Action extends \Zend_Controller_Action
{
	/**
	 * Stałe dla flash messenegera
	 *
	 * @var	string
	 */
	const MSG_OK 	= 'msg-ok';
	const MSG_ERROR = 'msg-error';

		/**
	 * Items per page
	 *
	 * @var	int
	 */
	private $iItemsPerPage = 1;

	/**
	 * Controller name
	 *
	 * @var	int
	 */
	private $sController = '';

	/**
	 * Factory
	 *
	 * @var	\Sca\DataObject\Factory
	 */
	protected $oFactory;

	/**
	 * Preparation of controller
	 *
	 * @param	string		$sController	nazwa kontrolera
	 * @param	int			$iListCount		ilość elementów na liście
	 * @return	void
	 */
	public function prepareController($sController, $oFactory, $iListCount)
	{
		$this->sController = strtolower($sController);
		$this->oFactory = $oFactory;
		$this->iItemsPerPage = $iListCount;
	}

	/**
	 * return url to action
	 *
	 * @param	string	$sAction	controller action
	 * @param	array	$aParams	url params
	 * @return	string
	 */
	protected function getUrl(array $aParams = array(), $sAction)
	{
		$sParams = '';
		foreach($aParams as $sKey => $sValue)
		{
			if($sValue !== null)
			{
				$sParams .= '/'. $sKey .'/'. $sValue;
			}
		}

		return '/'. $this->sController .'/'. $sAction . $sParams;
	}

	/**
	 * Inicjalizacja
	 */
	public function init()
	{
		parent::init();
		$this->view->sController = $this->sController;
	}

	/**
	 * Return paginator for list action
	 *
	 * @param	int		$iPage		page number
	 * @param	string	$sDbSort	db field sort
	 * @param	array	$aOptions	pagination options
	 * @return	Zend_Paginator
	 */
	protected function getPaginator($iPage, $sDbSort, array $aOptions = array())
	{
		$oPaginator = $this->oFactory->getPaginator(
			$iPage,
			$this->iItemsPerPage,
			[$sDbSort],
			null,
			$aOptions
		);

		if($oPaginator->count() > 0 && $iPage > $oPaginator->count())
		{
			$this->moveTo404();
			exit();
		}

		return $oPaginator;
	}

	/**
	 * Return item to edit
	 *
	 * @return	\Model\Tasks\Task
	 */
	protected function getItem()
	{
		try
		{
			$iId = $this->_request->getParam('id', 0);
			$oItem = $this->oFactory->getOne($iId);
		}
		catch(\Sca\DataObject\Exception $oExc)
		{
			$this->moveTo404();
			exit();
		}

		$this->view->assign('oItem', $oItem);
		return $oItem;
	}

	/**
	 * Przekazuje do widoku niezbędne dane z formularzy
	 *
	 * @param 	Zend_Filter_Input	$oFilter	obiekt filtra
	 * @return	void
	 */
	protected function showFormMessages(Zend_Filter_Input $oFilter = null)
	{
		$this->view->assign('aValues', $this->_request->getPost());
		$this->view->assign('aErrors', $oFilter->getMessages());
	}

	/**
	 * Dodaje komunikat do Flash Messengera
	 *
	 * @param	string	$sMessage	treść wiadomości
	 * @param	strng	$sType		typ wiadomości (stałe Core_Controller_Action::MSG_*)
	 * @param	bool	$bNow		czy wiadomośc powinna pojawiż się od razu
	 * @return	void
	 */
	protected function addMessage($sMessage, $sType = self::MSG_OK, $bNow = false)
	{
		if($bNow)
		{
			$this->_helper->flashMessenger->addCurrentMsg($sMessage, $sType);
		}
		else
		{
			$this->_helper->flashMessenger->addMsg($sMessage, $sType);
		}
	}

	/**
	 * Przenosi usera na 404
	 *
	 * @return	void
	 */
	protected function moveTo404()
	{
		throw new Zend_Controller_Action_Exception('Page not found', 404);
	}
}
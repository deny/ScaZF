<?php

/**
 * Display paginator pages
 *
 * @author	Daniel Kózka
 */
class Sca_View_Helper_Paginator extends \Zend_View_Helper_Abstract
{
	/**
	 * Helper function
	 *
	 * @param	Zend_Paginator		$oPaginator		paginator object
	 * @param	string				$sListElement	page element
	 * @param	string				$sListSep		element separator
	 * @return	string
	 */
	public function paginator(Zend_Paginator $oPaginator, $sListElement = 'span', $sSep = ' ')
	{
		$oPages = $oPaginator->getPages();

		if(count($oPages->pagesInRange) < 2)
		{
			return '';
		}

		$aParams = $this->view->aParams;
		$sResult = '<div class="paginator">';

	// first
		$aParams['page'] = $oPages->first;
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $this->view->getUrl($aParams) .'">&laquo;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

	// prev
		$aParams['page'] = isset($oPages->prev) ? $oPages->prev : $oPages->first;
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $this->view->getUrl($aParams) .'">&lt;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

	// pages
		foreach($oPages->pagesInRange as $iPage)
		{
			$sLeft = '<'. $sListElement;
			$sRight = '';

			if($oPages->current == $iPage)
			{
				$sLeft .= ' class="current">';
				$sRight .= '</'. $sListElement .'>';
			}
			else
			{
				$aParams['page'] = $iPage;
				$sLeft .= '><a href="'. $this->view->getUrl($aParams) .'">';
				$sRight .= '</a></'. $sListElement .'>';
			}

			$sResult .= $sLeft . $iPage . $sRight . $sSep;
		}

	// next
		$aParams['page'] = isset($oPages->next) ? $oPages->next : $oPages->last;
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $this->view->getUrl($aParams) .'">&gt;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

	// last
		$aParams['page'] = $oPages->last;
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $this->view->getUrl($aParams) .'">&raquo;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

		$sResult .= '</div>';

		return $sResult;
	}
}
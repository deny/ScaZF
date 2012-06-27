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
	 * @param	string				$sAddress		pagination address
	 * @param	string				$sListElement	page element
	 * @return	string
	 */
	public function paginator(Zend_Paginator $oPaginator, $sAddress, $sListElement = 'span', $sSep = ' ')
	{
		$oPages = $oPaginator->getPages();

		if(count($oPages->pagesInRange) < 2)
		{
			return '';
		}

	// first
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $sAddress .'/page/'. $oPages->first .'">&laquo;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

	// prev
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $sAddress .'/page/'. $oPages->prev .'">&lt;</a>';
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
				$sLeft .= '><a href="'. $sAddress . '/page/'. $iPage .'">';
				$sRight .= '</a></'. $sListElement .'>';
			}

			$sResult .= $sLeft . $iPage . $sRight . $sSep;
		}

	// next
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $sAddress .'/page/'. $oPages->next .'">&gt;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

	// last
		$sResult .= '<'. $sListElement. '>';
			$sResult .= '<a href="'. $sAddress .'/page/'. $oPages->last .'">&raquo;</a>';
		$sResult .= '</'. $sListElement .'>'. $sSep;

		return $sResult;
	}
}
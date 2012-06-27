<?php

/**
 * Return sort links for crud
 *
 * @author	Daniel Kózka
 */
class Sca_View_Helper_SortLink extends Zend_View_Helper_Abstract
{
	/**
	 * Helepr function
	 *
	 * @return	string
	 */
	public function sortLink($sName, $sAction)
	{
		$aUsedSort = isset($this->view->aUsedSort) ? $this->view->aUsedSort : array(0,0);
		$bCurrent = $sName == $aUsedSort[0];
		$bAsc = $bCurrent && $aUsedSort[1] == 'asc';
		$sType = $bAsc ? 'desc' : 'asc';

		$sResult = '<a '.
						'class="sca-sl '.
							($bAsc ? 'asc' : 'desc').
							($bCurrent ? ' sca-sl-current' : '') .
						'" href="'.
							$sAction .'/sort/'. $sName .':'. $sType .
					'">'.
						$sName .
					'</a>';

		return $sResult;
	}
}
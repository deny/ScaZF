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
	public function sortLink($sName)
	{
		$aParams = $this->view->aParams;
		$aUsedSort = isset($this->view->aUsedSort) ? $this->view->aUsedSort : array(0,0);
		$bCurrent = $sName == $aUsedSort[0];
		$bAsc = $bCurrent && $aUsedSort[1] == 'asc';
		$sType = $bAsc ? 'desc' : 'asc';
		$aParams['sort'] = $sName .':'. $sType;

		$sResult = '<a '.
						'class="sca-sl '.
							($bAsc ? 'asc' : 'desc').
							($bCurrent ? ' sca-sl-current' : '') .
						'" href="'. $this->view->getUrl($aParams) .'">'.
						$sName .
					'</a>';

		return $sResult;
	}
}
<?php

/**
 * Helper wyświetlający Flash Messengera
 *
 * @author	Daniel Kózka
 */
class Sca_Controller_Action_Helper_FlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger
{
    /**
     * Dodaje komunikat do wyświetlenia od razu
     *
     * @param	string	$sMessage	treść komunikatu
     * @param	string	$sType		typ komunikatu (klasa)
     * @return	Sca_Controller_Action_Helper_FlashMessenger
     */
    public function addCurrentMsg($sMessage, $sType)
    {
		if(!isset(self::$_messages[$this->_namespace]))
		{
			self::$_messages = array();
			self::$_messages[$this->_namespace] = array();
		}

		self::$_messages[$this->_namespace][] = array(
			'message'	=> $sMessage,
			'type'		=> $sType
		);

		return $this;
    }

	/**
     * Dodaje komunikat do wyświetlenia od razu
     *
     * @param	string	$sMessage	treść komunikatu
     * @param	string	$sType		typ komunikatu (klasa)
     * @return	Sca_Controller_Action_Helper_FlashMessenger
     */
	public function addMsg($sMessage, $sType)
	{
		if(!self::$_session instanceof Zend_Session_Namespace)
		{
			self::$_session = new Zend_Session_Namespace($this->getName());
		}
		parent::addMessage(array('message' => $sMessage, 'type' => $sType));

		return $this;
	}
}
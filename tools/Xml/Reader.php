<?php

/**
 * @namespace
 */
namespace ScaZF\Tool\Xml;

/**
 * XMLReader with additional functions
 *
 * @author Daniel Kózka
 */
class Reader extends \XMLReader
{
	/**
	 * Move cursor to next node
	 *
	 * @return bool
	 */
	public function readNode()
	{
		while($bSuccess = $this->read())
		{
			if($this->nodeType == self::ELEMENT)
			{
				break;
			}
		}

		return $bSuccess;
	}

	/**
	 * Move cursor to next node with given name (skips subtrees).
	 * If find $sStopNode then stops searching.
	 *
	 * @param	string	$sName		node name
	 * @param	string	$sStopNode	stop node name
	 * @return	bool
	 */
	public function nextNode($sName, $sStopNode = null)
	{
		$bSuccess = $this->next();

		if($bSuccess && $this->name != $sName) // if higher level
		{
			// searching for node or stop node
			while($bSuccess = $this->read())
			{
				if($this->name == $sName)
				{
					return true;
				}
				elseif($this->name == $sStopNode)
				{
					return false;
				}
			}
		}

		return $bSuccess;
	}

	/**
	 * Move cursor to first node with given name (including subtrees)
	 *
	 * @param	string	$sName	node name
	 * @return	bool
	 */
	public function goToNode($sName)
	{
		while($bSuccess = $this->read())
		{
			if($this->nodeType == self::ELEMENT && $this->name == $sName)
			{
				return true;
			}
		}

		return $bSuccess;
	}

	/**
	 * Check if given attribute name exists
	 *
	 * @param	string	$sName	attribute name
	 * @return 	bool
	 */
	public function isAttrSet($sName)
	{
		$sRes = parent::getAttribute($sName);
		return !empty($sRes);
	}

	/**
	 * Return attribute value
	 *
	 * @param	string		$sName 			attribute name
	 * @param	string		$sDefaultValue	default value
	 * @return	string
	 */
	public function getAttribute($sName, $sDefaultValue = null)
	{
		return $this->isAttrSet($sName) ? parent::getAttribute($sName) : $sDefaultValue;
	}

	/**
	 * Validate document against XSD
	 *
	 * @param	string	$sFile		path to file
	 * @param	string	$sSchema	patch to schema
	 * @return	bool
	 */
	public static function schemaValidate($sFile, $sSchema)
	{
		$oReader = new self();
		if(!$oReader->open($sFile))
		{
			return false;
		}

		$oReader->setSchema($sSchema);

		$sErro = false;
		while($oReader->read())
		{
			if(!$oReader->isValid())
			{
				$sError = $oReader->name;
				break;
			}
		}
		$oReader->close();

		return $sError;
	}
}
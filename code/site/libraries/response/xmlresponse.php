<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

class APIXMLResponse
{
	var $err_msg = '';

	var $err_code = '';

	var $response_id = '';

	var $api = '';

	var $version = '';

	var $data = null;

	public function __construct($response)
	{
		$app = JFactory::getApplication();
		$this->data = new \stdClass;

		if ($response instanceof Exception) {
			$this->err_msg = $response->getMessage();
			$this->err_code = $response->getCode();
		}
		else
		{
			$this->api = "{$response->component}.{$response->resource}";
			$this->response_id = $response->response_id;			
			$this->data = $response->get('response');
		}
	}

	/**
	 * Transforms the plugin response to an XML string
	 *
	 * @return mixed
	 *
	 * @return  string
	 *
	 * @since 1.0
	 */
	public function __toString()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		$this->_toXMLRecursive($this, $xml);

		return $xml->asXML();
	}

	/**
	 * Method description
	 *
	 * @param   STRING  $element  element
	 * @param   STRING  &$xml     xml
	 *
	 * @return  mixed
	 *
	 * @since 1.0
	 */
	protected function _toXMLRecursive($element, &$xml)
	{
		if (! is_array($element) && ! is_object($element))
		{
			return null;
		}

		if (is_object($element))
		{
			$element = get_object_vars($element);
		}

		foreach ($element as $key => $value)
		{
			$this->_handleMultiDimensions($key, $value, $xml);
		}
	}

	/**
	 * Method _handleMultiDimensions
	 *
	 * @param   STRING  $key    key
	 * @param   STRING  $value  value
	 * @param   STRING  &$xml   xml
	 *
	 * @return  mixed
	 *
	 * @since 1.0
	 */
	protected function _handleMultiDimensions($key, $value, &$xml)
	{
		if (is_array($value) || is_object($value))
		{
			$node = $xml->addChild($key);
			$this->_toXMLRecursive($value, $node);
		}
		else
		{
			$node = $xml->addChild($key, htmlspecialchars($value));
		}
	}
}

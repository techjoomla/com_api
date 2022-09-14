<?php
/**
 * @package    Com_Api
 * @copyright  Copyright (C) 2009 - 2020 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

use Joomla\CMS\Factory;

/**
 * Class APIJSONResponse to convert the response of API in json
 *
 * @since  1.0
 */
class APIJSONResponse
{
	public $err_msg = '';

	public $err_code = '';

	public $response_id = '';

	public $api = '';

	public $version = '';

	public $data = null;

	protected $callbackname = 'callback';

	/**
	 * Constructor for APIXMLResponse
	 *
	 * @param   OBJECT  $response  The response object
	 *
	 * @since 1.0
	 */
	public function __construct($response)
	{
		$app = Factory::getApplication();
		$this->data = new \stdClass;

		if ($response instanceof Exception)
		{
			$this->err_msg = $response->getMessage();
			$this->err_code = $response->getCode();
		}
		else
		{
			$this->api = "{$response->component}.{$response->resource}";
			$this->response_id = $response->response_id;
			$this->data = $response->get('response');

			if (!empty($customAttributes = $response->get('customAttributes')->toArray()))
			{
				// Unset the already set class variables from the custom attributes
				$diffAttr = array_diff_key($customAttributes, get_object_vars($this));

				foreach ($diffAttr as $customKey => $customValue)
				{
					$this->$customKey = $customValue;
				}
			}
		}
	}

	/**
	 * Transforms the plugin response to a JSON-encoded string
	 * Can also return JSONP if the callback is set
	 *
	 * @return  string
	 *
	 * @since 1.0
	 */
	public function __toString()
	{
		$app = Factory::getApplication();
		$callback = $app->input->get($this->callbackname, '', 'CMD');

		if ($callback)
		{
			return $callback . '(' . json_encode($this) . ')';
		}
		else
		{
			return json_encode($this);
		}
	}
}

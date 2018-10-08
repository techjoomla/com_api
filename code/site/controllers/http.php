<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.plugin.helper');

/**
 * ApiControllerHttp class
 *
 * @since  1.0
 */
class ApiControllerHttp extends ApiController
{
	/**
	 * Used to find callback in the url
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $callbackname = 'callback';

	/**
	 * Typical view method for MVC based architecture
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$this->resetDocumentType();
		$app = JFactory::getApplication();
		$name = $app->input->get('app', '', 'CMD');

		$params = JComponentHelper::getParams('com_api');
		$callMethod = $app->input->getMethod();
		$httpOrigin = $app->input->server->getString('HTTP_REFERER', '');

		$JUriObj = JUri::getInstance($httpOrigin);
		$referer = $JUriObj->toString(array('scheme', 'host', 'port'));

		// Special method for OPTIONS method
		if ((! empty($params->get("allow_cors"))))
		{
			$corsUrls = $params->get('cors', "*");

			if ($corsUrls === "*")
			{
				header("Access-Control-Allow-Origin: " . '*');
			}
			else
			{
				$corsUrlsArray = array_map('trim', explode(',', $corsUrls));

				if (in_array($referer, $corsUrlsArray))
				{
					header("Access-Control-Allow-Origin: " . $referer);
				}
			}

			header("Access-Control-Allow-Methods: " . strtoupper($params->get("allow_cors")));
		}

		if ($callMethod === "OPTIONS")
		{
			header("Content-type: application/json");
			header("Access-Control-Allow-Headers: " . $params->get("allow_headers"));

			jexit();
		}

		try
		{
			JResponse::setHeader('status', 200);
			$resourceResponse = ApiPlugin::getInstance($name)->fetchResource();

			echo $this->respond($resourceResponse);
		}
		catch (Exception $e)
		{
			JResponse::setHeader('status', $e->http_code);
			echo $this->respond($e);
		}
	}

	/**
	 * Send the response in the correct format
	 *
	 * @param   OBJECT  $response  exception
	 *
	 * @return  void
	 *
	 * @since 2.0
	 */
	private function respond($response)
	{
		$app = JFactory::getApplication();
		$accept = $app->input->server->get('HTTP_ACCEPT', 'application/json', 'STRING');
		$compatibility = $app->input->server->get('HTTP_X_COMPATIBILITY_MODE', 0, 'INT');

		// Enforce JSON in compatibility mode
		if ($compatibility)
		{
			$output = new \stdClass;
			header("Content-type: application/json");

			if ($response instanceof Exception)
			{
				$output->message = $response->getMessage();
				$output->code = $response->getCode();
			}
			else
			{
				$output = $response->get('response');
			}

			echo json_encode($output);

			die();
		}

		switch ($accept)
		{
			case 'application/json':
			default:
				header("Content-type: application/json");
				$format = 'json';
			break;

			case 'application/xml':
				header("Content-type: application/xml");
				$format = 'xml';
			break;
		}

		$outputOverrride = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/' . $format . '/api.php';

		if (file_exists($outputOverrride))
		{
			require_once $outputOverrride;
		}
		else
		{
			require_once JPATH_COMPONENT . '/libraries/response/' . $format . 'response.php';
		}

		$classname = 'API' . ucfirst($format) . 'Response';
		$output = new $classname($response);

		echo $output->__toString();

		jexit();
	}

	/**
	 * Resets the document type to format=raw
	 *
	 * @todo Figure out if there is a better way to do this
	 *
	 * @return void
	 *
	 * @since 0.1
	 */
	private function resetDocumentType()
	{
		JResponse::clearHeaders();
	}
}

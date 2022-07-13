<?php
/**
 * @package     Joomla.Site
 * @subpackage  Com_api
 *
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;


/**
 * ApiControllerHttp class
 *
 * @since  1.0
 */
class ApiControllerHttp extends ApiController
{
	public $callbackname = 'callback';

	/**
	 * Typical view method for MVC based architecture
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  Mixed
	 *
	 * @since   12.2
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$this->resetDocumentType();
		$app = Factory::getApplication();
		$name = $app->input->get('app', '', 'CMD');

		$params = ComponentHelper::getParams('com_api');
		$callMethod = $app->input->getMethod();
		$httpOrigin = $app->input->server->getString('HTTP_REFERER', '');

		$UriObj = Uri::getInstance($httpOrigin);
		$referer = $UriObj->toString(array('scheme', 'host', 'port'));

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
			header("status: 200");
			//JResponse::setHeader('status', 200);
			$resource_response = ApiPlugin::getInstance($name)->fetchResource();

			echo $this->respond($resource_response);
		}
		catch (Exception $e)
		{
			header("status: " . $e->http_code);
			//JResponse::setHeader('status', $e->http_code);
			echo $this->respond($e);
		}
	}

	/**
	 * Send the response in the correct format
	 *
	 * @param   OBJECT  $response  exception
	 *
	 * @return  json
	 *
	 * @since 2.0
	 */
	private function respond($response)
	{
		$app = Factory::getApplication();
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

		$output_overrride = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/' . $format . '/api.php';

		if (file_exists($output_overrride))
		{
			require_once $output_overrride;
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
		if (!headers_sent())
		{
			foreach (headers_list() as $header)
			{
				header_remove($header);
			}
		}
		//JResponse::clearHeaders();
	}
}

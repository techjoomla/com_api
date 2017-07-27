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

jimport('joomla.application.component.controller');
jimport('joomla.plugin.helper');
JLoader::discover('API', JPATH_COMPONENT . '/libraries/exceptions');
//JLoader::discover('API', JPATH_COMPONENT . '/libraries/response');

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
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  Mixed
	 *
	 * @since   12.2
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$this->resetDocumentType();
		$app = JFactory::getApplication();
		$name = $app->input->get('app', '', 'CMD');

		// Set CORS header
		$cors_urls = explode("\n", JComponentHelper::getParams('com_api')->get('cors'));

		foreach ($cors_urls as $cors_url)
		{
			JResponse::setHeader('Access-Control-Allow-Origin', $cors_url);
		}

		try
		{
			JResponse::setHeader('status', 200);
			$resource_response = ApiPlugin::getInstance($name)->fetchResource();
			echo $this->respond($resource_response);
		}
		catch (Exception $e)
		{
			JResponse::setHeader('status', $e->http_code);
			echo $this->respond($e);
		}

		//exit;
	}

	/**
	 * Method description
	 *
	 * @param   OBJECT  $exception  exception
	 *
	 * @return  json
	 *
	 * @since 1.0
	 */
	private function respond($response)
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$accept = $app->input->server->get('HTTP_ACCEPT', 'application/json', 'STRING');

		switch($accept) {
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

		$output_overrride = JPATH_ROOT . '/' . $app->getTemplate() . '/'.$format.'/api.php';

		if (file_exists($output_overrride)) {
			require_once $output_overrride;
		}
		else
		{
			require_once JPATH_COMPONENT . '/libraries/response/'.$format.'response.php';
		}

		$classname = 'API'.ucfirst($format).'Response';
		$output = new $classname($response);

		echo $output->__toString();

		die();
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

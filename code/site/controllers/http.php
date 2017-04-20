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

/**
 * ApiControllerHttp class
 *
 * @since  1.0
 */
class ApiControllerHttp extends ApiController
{
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
			echo ApiPlugin::getInstance($name)->fetchResource();
		}
		catch (Exception $e)
		{
			echo $this->sendError($e);
		}
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
	private function sendError($exception)
	{
		JResponse::setHeader('status', $exception->getCode());
		$error = new APIException($exception->getMessage(), $exception->getCode());
		JFactory::getDocument()->setMimeEncoding('application/json');

		require_once JPATH_SITE.'/components/com_api/views/view.json.php';

		$xml = JFactory::getXML(JPATH_ADMINISTRATOR .'/components/com_api/api.xml');
		$version = (string)$xml->version;

		if($version >= 2.0) {
			$res = array();	
			$res['responseCode'] = $error->getCode();
			$res['errorMsg'] = $error->getMessage();
			return APIViewJSON :: display($res);
		} else {
			return json_encode($error->toArray());
		}
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

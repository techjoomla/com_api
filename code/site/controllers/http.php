<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport('joomla.plugin.helper');

class ApiControllerHttp extends ApiController
{
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	public function display( $cachable = false, $urlparams = array() )
	{
		$this->resetDocumentType();
		$app = JFactory::getApplication();
		$name = $app->input->get('app','','CMD');
		
		// Set CORS header
		$cors_urls = explode("\n", JComponentHelper::getParams('com_api')->get('cors'));
		foreach ($cors_urls as $cors_url)
		{
			JResponse::setHeader( 'Access-Control-Allow-Origin', $cors_url );
		}

		try {
			
			echo ApiPlugin::getInstance( $name )->fetchResource();

		}  catch ( Exception $e ) {
			echo $this->sendError( $e );
		}
	}

	private function sendError( $exception )
	{
		JResponse::setHeader( 'status', $exception->getCode() );
		$error = new APIException( $exception->getMessage(), $exception->getCode() );
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		return json_encode( $error->toArray() );
	}


	/**
	 * Resets the document type to format=raw
	 *
	 * @return void
	 * @since 0.1
	 * @todo Figure out if there is a better way to do this
	 */
	private function resetDocumentType()
	{
		JResponse::clearHeaders();
	}
}

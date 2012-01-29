<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerHttp extends ApiController
{
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	public function display()
	{
		$this->resetDocumentType();

		jimport( 'joomla.plugin.helper' );
		$name = JRequest::getCmd( 'app' );

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
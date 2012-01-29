<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

$library_path = JPATH_COMPONENT .DS. 'libraries';

JLoader::register( 'APIController', $library_path .DS. 'controller.php' );
JLoader::register( 'APIModel', $library_path .DS. 'model.php' );
JLoader::register( 'APIView', $library_path .DS. 'view.php' );
JLoader::register( 'APIHelper', $library_path .DS. 'helper.php' );
JLoader::register( 'APIPlugin', $library_path .DS. 'plugin.php' );
JLoader::register( 'APIError', $library_path .DS. 'error.php' );
JLoader::register( 'APIException', $library_path .DS. 'exception.php' );
JLoader::register( 'APICache', $library_path .DS. 'cache.php' );
JLoader::register( 'APIResource', $library_path .DS. 'resource.php' );
JLoader::register( 'APIAuthentication', $library_path .DS. 'authentication.php' );
JLoader::register( 'APIAuthenticationKey', $library_path .DS. 'authentication' .DS. 'key.php' );
JLoader::register( 'APIAuthenticationUser', $library_path .DS. 'authentication' .DS. 'user.php' );

$view	= JRequest::getCmd( 'view', '', 'method' );
if ( $view ) {
	$controller = $view;
} else {
	$controller = JRequest::getCmd( 'c', 'http', 'method' );
}

$c_path	= JPATH_COMPONENT .DS. 'controllers' .DS. strtolower( $controller ) . '.php';
if ( file_exists( $c_path ) ) {
	include_once $c_path;
	$c_name	= 'ApiController' . ucwords( $controller );
} else {
	JError::raiseError( 404, JText::_( 'COM_API_CONTROLLER_NOT_FOUND' ) );
}

$controller = new $c_name();
$controller->execute( JRequest::getCmd( 'task', 'display', 'method' ) );
$controller->redirect();
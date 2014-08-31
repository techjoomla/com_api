<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
error_reporting(0);
ini_set('display_errors','Off');

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

$front_end = JPATH_SITE.'/components/com_api';

JLoader::register( 'APIController', $front_end.'/libraries/controller.php' );
JLoader::register( 'ApiControllerAdmin',$front_end.'/libraries/admin/controller.php' );
JLoader::register( 'APIModel', $front_end.'/libraries/model.php' );
JLoader::register( 'APIView', $front_end.'/libraries/view.php' );

$app	= JFactory::getApplication();

//$view       = JRequest::getCmd( 'view', '' );
//$controller = JRequest::getCmd( 'c', '' );

$view       = $app->input->get( 'view', '' , 'CMD' );
$controller = $app->input->get( 'c', '', 'CMD');



if ( $view && !$controller ) {
	$controller	= $view;
}

$c_path	= JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.strtolower( $controller ) . '.php';

if ( file_exists( $c_path ) ) {
	include_once $c_path;
	$c_name	= 'ApiController' . ucwords( $controller );
} else {
		$c_name = 'ApiControllerAdmin';
}


$controller = new $c_name();

$task = $app->input->get('task','display','CMD');

//print_r($controller);die("in api.php admin");

//$controller->execute( JRequest::getCmd( 'task', 'display' ) );
$controller->execute( $task);
$controller->redirect();

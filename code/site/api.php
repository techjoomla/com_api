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

$library_path = JPATH_COMPONENT.'/libraries';

JLoader::register('APIController', $library_path.'/controller.php');
JLoader::register('APIModel', $library_path.'/model.php');
JLoader::register('APIView', $library_path.'/view.php');
JLoader::register('APIPlugin', $library_path.'/plugin.php');
JLoader::register('APIError', $library_path.'/error.php');
JLoader::register('ApiException', $library_path.'/exception.php');
JLoader::register('APICache', $library_path.'/cache.php');
JLoader::register('APIResource', $library_path.'/resource.php');
JLoader::register('APIAuthentication', $library_path.'/authentication.php');
JLoader::register('APIAuthenticationKey', $library_path.'/authentication/key.php');
JLoader::register('APIAuthenticationLogin', $library_path.'/authentication/login.php');

$app = JFactory::getApplication();

//$view	= JRequest::getCmd('view', '');
$view = $app->input->get('view','','CMD');


if ($view) :
	$c	= $view;
else :
	//$c	= JRequest::getCmd('c', 'http');
	$c	= $app->input->get('c','http','CMD');
endif;

$c_path	= JPATH_COMPONENT.'/controllers/'.strtolower($c).'.php';
if (file_exists($c_path)) :
	include_once $c_path;
	$c_name	= 'ApiController'.ucwords($c);
else :
	//JError::raiseError(404, JText::_('COM_API_CONTROLLER_NOT_FOUND'));
	throw new Exception(JText::_('COM_API_CONTROLLER_NOT_FOUND'),404);

endif;

$command = $app->input->get('task','display','CMD');

$controller = new $c_name(); //print_r($controller);die("in api.php");
$controller->execute($command);
$controller->redirect();

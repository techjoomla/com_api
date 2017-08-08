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

$library_path = JPATH_COMPONENT . '/libraries';

JLoader::register('APIController', $library_path . '/controller.php');
JLoader::register('APIModel', $library_path . '/model.php');
JLoader::register('APIView', $library_path . '/view.php');
JLoader::register('APIPlugin', $library_path . '/plugin.php');
JLoader::register('APIError', $library_path . '/error.php');
JLoader::register('ApiException', $library_path . '/exception.php');
JLoader::register('APICache', $library_path . '/cache.php');
JLoader::register('APIResource', $library_path . '/resource.php');
JLoader::register('APIAuthentication', $library_path . '/authentication.php');
JLoader::register('APIAuthenticationKey', $library_path . '/authentication/key.php');
JLoader::register('APIAuthenticationLogin', $library_path . '/authentication/login.php');
JLoader::register('APIAuthenticationSession', $library_path . '/authentication/session.php');
JLoader::register('APIHelper', $library_path . '/helper.php');
JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_api/tables');
JLoader::discover('API', JPATH_COMPONENT . '/libraries/exceptions');

$app = JFactory::getApplication();

$view = $app->input->get('view', '', 'CMD');

if ($view)
{
	$c = $view;
}
else
{
	$c = $app->input->get('c', 'http', 'CMD');
}

$c_path = JPATH_COMPONENT . '/controllers/' . strtolower($c) . '.php';

if (file_exists($c_path))
{
	include_once $c_path;
	$c_name = 'ApiController' . ucwords($c);
}
else
{
	// JError::raiseError(404, JText::_('COM_API_CONTROLLER_NOT_FOUND'));
	throw new Exception(JText::_('COM_API_CONTROLLER_NOT_FOUND'), 404);
}

$command = $app->input->get('task', 'display', 'CMD');

$controller = new $c_name;
$controller->execute($command);
$controller->redirect();

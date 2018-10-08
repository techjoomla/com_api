<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

$libraryPath = JPATH_COMPONENT . '/libraries';

JLoader::register('APIController', $libraryPath . '/controller.php');
JLoader::register('APIModel', $libraryPath . '/model.php');
JLoader::register('APIView', $libraryPath . '/view.php');
JLoader::register('APIPlugin', $libraryPath . '/plugin.php');
JLoader::register('APIError', $libraryPath . '/error.php');
JLoader::register('ApiException', $libraryPath . '/exception.php');
JLoader::register('APICache', $libraryPath . '/cache.php');
JLoader::register('APIResource', $libraryPath . '/resource.php');
JLoader::register('APIAuthentication', $libraryPath . '/authentication.php');
JLoader::register('APIAuthenticationKey', $libraryPath . '/authentication/key.php');
JLoader::register('APIAuthenticationLogin', $libraryPath . '/authentication/login.php');
JLoader::register('APIAuthenticationSession', $libraryPath . '/authentication/session.php');
JLoader::register('APIHelper', $libraryPath . '/helper.php');
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

$controllerPath = JPATH_COMPONENT . '/controllers/' . strtolower($c) . '.php';

if (file_exists($controllerPath))
{
	include_once $controllerPath;
	$className = 'ApiController' . ucwords($c);
}
else
{
	throw new Exception(JText::_('COM_API_CONTROLLER_NOT_FOUND'), 404);
}

$command = $app->input->get('task', 'display', 'CMD');

$controller = new $className;
$controller->execute($command);
$controller->redirect();

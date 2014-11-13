<?php
/**
 * @version    SVN: <svn_id>
 * @package    Api
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

// No direct access.
defined('_JEXEC') or die();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_api'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_SITE . '/components/com_api/defines.php';

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Api');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

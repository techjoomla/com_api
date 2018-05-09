<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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

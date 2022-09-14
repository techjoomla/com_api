<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_api'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_SITE . '/components/com_api/defines.php';

// Include dependancies

$controller	= BaseController::getInstance('Api');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();

<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;

// Define wrapper class
define('COM_APIS_WRAPPER_CLASS', "api-wrapper");

if (JVERSION < '4.0.0')
{
    HTMLHelper::_('formbehavior.chosen', 'select');
    HTMLHelper::_('behavior.tabstate');
}

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

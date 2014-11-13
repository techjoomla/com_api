<?php
/**
 * @version    SVN: <svn_id>
 * @package    Api
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

$document = JFactory::getDocument();

// For joomla 2.5.x
if (JVERSION < '3.0')
{
	// Define wrapper class
	define('COM_APIS_WRAPPER_CLASS', "api-wrapper techjoomla-bootstrap");

	// Other
	JHtml::_('behavior.tooltip');

	$document->addStyleSheet(JUri::root(true) . '/media/techjoomla_strapper/css/bootstrap.min.css');
	$document->addStyleSheet(JUri::root(true) . '/media/techjoomla_strapper/css/bootstrap-responsive.min.css');
}
else
{
	// Define wrapper class
	define('COM_APIS_WRAPPER_CLASS', "api-wrapper");

	// Tabstate
	JHtml::_('behavior.tabstate');

	// Other
	JHtml::_('behavior.tooltip');

	// Bootstrap tooltip and chosen js
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('formbehavior.chosen', 'select');
}

$document->addStyleSheet(JUri::root(true) . '/media/techjoomla_strapper/css/strapper.css');

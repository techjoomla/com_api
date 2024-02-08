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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Factory;

/**
 * Content component helper.
 *
 * @since  1.0
 */
class ApiHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName = '')
	{
		if (JVERSION < '4.0.0')
		{
			$submenus = array();
			$submenus[] = array(
				'title' => Text::_('COM_API_TITLE_KEYS'), 'link' => 'index.php?option=com_api&view=keys', 'view' => $vName == 'keys'
			);
			$submenus[] = array(
				'title' => Text::_('COM_API_TITLE_LOGS'), 'link' => 'index.php?option=com_api&view=logs', 'view' => $vName == 'logs'
			);
	
			foreach ($submenus as $submenu)
			{
				JHtmlSidebar::addEntry($submenu['title'], $submenu['link'], $submenu['view']);
			}
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  CMSObject
	 *
	 * @since  1.0
	 */
	public static function getActions()
	{
		$user = Factory::getUser();
		$result = new CMSObject;

		$assetName = 'com_api';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete', 'logs.manage'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

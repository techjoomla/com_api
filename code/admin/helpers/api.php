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

/**
 * Content component helper.
 *
 * @package     Api
 * @subpackage  com_api
 * @since       1.0
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
		$submenus[] = array('title' => JText::_('COM_API_TITLE_KEYS'), 'link' => 'index.php?option=com_api&view=keys', 'view' => $vName == 'keys');
		$submenus[] = array('title' => JText::_('COM_API_TITLE_LOGS'), 'link' => 'index.php?option=com_api&view=logs', 'view' => $vName == 'logs');

		foreach ($submenus as $submenu)
		{
			if (version_compare(JVERSION, '3.0.0', 'ge'))
			{
				JHtmlSidebar::addEntry(
					$submenu['title'],
					$submenu['link'],
					$submenu['view']
				);
			}
			else
			{
				JSubMenuHelper::addEntry(
					$submenu['title'],
					$submenu['link'],
					$submenu['view']
				);
			}
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 *
	 * @since  1.0
	 */
	public static function getActions()
	{
		$user = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_api';

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.own',
			'core.edit.state',
			'core.delete',
			'logs.manage'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

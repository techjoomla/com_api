<?php

/**
 * @version     1.0.0
 * @package     com_api
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Api helper.
 */
class ApiHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
			$submenus[] = array('title'=>JText::_('COM_API_TITLE_KEYS'), 'link'=>'index.php?option=com_api&view=keys', 'view'=>'keys');
			
			foreach ($submenus as $submenu) {
				if (version_compare(JVERSION, '3.0.0', 'ge')) {
        	JHtmlSidebar::addEntry(
						$submenu['title'],
						$submenu['link'],
						$submenu['view']
					);
				} else {
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
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_api';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }


}

<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Api.
 */
class ApiViewLogs extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        ApiHelper::addSubmenu('logs');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/api.php';

        $state = $this->get('State');
        $canDo = ApiHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_API_TITLE_LOGS'), 'logs.png');

        if ($canDo->get('core.edit.state')) {
					//If this component does not use state then show a direct delete button as we can not trash
					JToolBarHelper::deleteList('', 'logs.delete', 'JTOOLBAR_DELETE');
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'logs.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('logs.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_api');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_api&view=logs');

        $this->extra_sidebar = '';
        
    }

	protected function getSortFields()
	{
		return array(
		'u.name' => JText::_('COM_API_LOGS_USER'),
		'a.hash' => JText::_('COM_API_KEYS_HASH'),
		'a.ip_address' => JText::_('COM_API_LOGS_IP_ADDRESS'),
		'a.time' => JText::_('COM_API_LOGS_TIME'),
		);
	}

}

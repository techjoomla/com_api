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

jimport('joomla.application.component.view');

/**
 * View class for a list of Api.
 */
class ApiViewKeys extends JViewLegacy {

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

        ApiHelper::addSubmenu('keys');

        $this->addToolbar();

				if (version_compare(JVERSION, '3.0.0', 'ge')) {
					$this->sidebar = JHtmlSidebar::render();
				}
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

        JToolBarHelper::title(JText::_('COM_API_TITLE_KEYS'), 'keys.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/key';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('key.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('key.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('keys.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('keys.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'keys.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('keys.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'keys.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('keys.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_api');
        }

        //Set sidebar action - New in 3.0
        if (version_compare(JVERSION, '3.0.0', 'ge')) {
					JHtmlSidebar::setAction('index.php?option=com_api&view=keys');

					$this->extra_sidebar = '';
        
					JHtmlSidebar::addFilter(
						JText::_('JOPTION_SELECT_PUBLISHED'),
						'filter_published',
						JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
					);
				}
    }

	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.userid' => JText::_('COM_API_KEYS_USERID'),
		'a.domain' => JText::_('COM_API_KEYS_DOMAIN'),
		'a.state' => JText::_('JSTATUS'),
		'a.last_used' => JText::_('COM_API_KEYS_LAST_USED'),
		);
	}

}

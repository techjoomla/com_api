<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die(); 
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for list of keys
 *
 * @since  1.0
 */
class ApiViewKeys extends HtmlView
{
	/**
	 * The model state.
	 *
	 * @var   CMSObject
	 * @since 1.0
	 */
	protected $state;

	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $items;

	/**
	 * The pagination object.
	 *
	 * @var   Pagination
	 * @since 1.0
	 */
	protected $pagination;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		ApiHelper::addSubmenu('keys');

		$this->publish_states = array(
			'' => Text::_('JOPTION_SELECT_PUBLISHED'), '1' => Text::_('JPUBLISHED'), '0' => Text::_('JUNPUBLISHED'), '*' => Text::_('JALL')
		);

		$this->addToolbar();

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/api.php';

		$state = $this->get('State');
		$canDo = ApiHelper::getActions($state->get('filter.category_id'));

		if (JVERSION >= '3.0')
		{
			ToolBarHelper::title(Text::_('COM_API_TITLE_KEYS'), 'key');
		}
		else
		{
			ToolBarHelper::title(Text::_('COM_API_TITLE_KEYS'), 'keys.png');
		}

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/key';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				ToolBarHelper::addNew('key.add', 'JTOOLBAR_NEW');
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				ToolBarHelper::editList('key.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				ToolBarHelper::divider();
				ToolBarHelper::custom('keys.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolBarHelper::custom('keys.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($canDo->get('core.delete'))
			{
				ToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'keys.delete', 'JTOOLBAR_DELETE');
				ToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_api');
		}

		// Set sidebar action - New in 3.0
		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			JHtmlSidebar::setAction('index.php?option=com_api&view=keys');
			$this->extra_sidebar = '';
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.id' => Text::_('JGRID_HEADING_ID'), 'a.userid' => Text::_('COM_API_KEYS_USERID'), 'a.domain' => Text::_('COM_API_KEYS_DOMAIN'),
				'a.state' => Text::_('JSTATUS'), 'a.last_used' => Text::_('COM_API_KEYS_LAST_USED')
		);
	}
}

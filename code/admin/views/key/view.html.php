<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * View class key form.
 *
 * @since  1.0
 */
class ApiViewKey extends JViewLegacy
{
	/**
	 * The model state.
	 *
	 * @var   JObject
	 * @since 1.0
	 */
	protected $state;

	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $item;

	/**
	 * A JForm instance with filter fields.
	 *
	 * @var    JForm
	 * @since  1.0
	 */
	protected $form;

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
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$viewTitle = JText::_('COM_API_ADD_KEY');
		}
		else
		{
			$viewTitle = JText::_('COM_API_EDIT_KEY');
		}

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title($viewTitle, 'pencil-2');
		}
		else
		{
			JToolBarHelper::title($viewTitle, 'key.png');
		}

		if (isset($this->item->checked_out))
		{
			$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = ApiHelper::getActions();

		// If not checked out, can save the item.
		if (! $checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('key.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('key.save', 'JTOOLBAR_SAVE');
		}

		if (! $checkedOut && ($canDo->get('core.create')))
		{
			JToolBarHelper::custom('key.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (! $isNew && $canDo->get('core.create'))
		{
			JToolBarHelper::custom('key.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('key.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('key.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}

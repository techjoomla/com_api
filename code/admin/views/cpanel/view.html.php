<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;

/**
 * Cpanel class
 *
 * @since  1.0
 */
class ApiViewCpanel extends ApiView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     \HtmlView::loadTemplate()
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		if ($this->routeLayout($tpl))
		{
			return;
		}

		$this->generateToolbar();

		$views = $this->getMainViews();

		$this->assignRef('views', $views);
		$this->assignRef('modified', $modified);

		parent::display($tpl);
	}

	/**
	 * Method to generate toolbar
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function generateToolbar()
	{
		JToolBarHelper::title(Text::_('COM_API') . ': ' . Text::_('COM_API_CONTROL_PANEL'));
		JToolBarHelper::preferences('com_api', 500, 500);
	}
}

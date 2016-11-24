<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Api
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  Copyright (C) 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class ApiController
 *
 * @since  1.6
 */
class apiFrontendController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $app  = JFactory::getApplication();
        $view = $app->input->getCmd('view', 'keys');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}

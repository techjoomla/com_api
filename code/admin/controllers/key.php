<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

/**
 * Key controller class.
 *
 * @since  1.0
 */
class ApiControllerKey extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @see     \JControllerLegacy
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct()
	{
		$this->view_list = 'keys';
		parent::__construct();
	}
}

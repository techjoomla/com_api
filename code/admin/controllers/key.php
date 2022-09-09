<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Key controller class.
 *
 * @since  1.0
 */
class ApiControllerKey extends FormController
{
	/**
	 * Constructor.
	 *
	 * @see     \BaseController
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct()
	{
		$this->view_list = 'keys';
		parent::__construct();
	}
}

<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_api
 *
 * @copyright   Copyright (C) 2024 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

/**
 * Base API Controller
 *
 * @since  1.0.0
 */
class ApiController extends BaseController
{
	/**
	 * The option name
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $option;

	/**
	 * Base Controller Constructor
	 *
	 * @param   array  $config  Controller initialization configuration parameters
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = [])
	{
		parent::__construct();

		$app = Factory::getApplication();

		$this->option = $app->input->get('option', '', 'STRING');

		ListModel::addIncludePath(JPATH_SITE . '/components/com_api/models');
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_api/tables');
	}
}

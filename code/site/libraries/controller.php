<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

//class ApiController extends JController {
class ApiController extends BaseController {

	/**
	 * Base Controller Constructor
	 *
	 * @param array $config Controller initialization configuration parameters
	 * @return void
	 * @since 0.1
	 */

	public function __construct($config=array()) {
		parent::__construct();

		$app = Factory::getApplication();

		$this->option = $app->input->get('option','','STRING');

		ListModel::addIncludePath(JPATH_SITE.'/components/com_api/models');
		Table::addIncludePath(JPATH_ROOT.'/administrator/components/com_api/tables');
	}
}

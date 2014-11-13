<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined('_JEXEC') or die;
jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class ApiModelDocumentation extends ApiModel
{

	protected $option 		= null;
	protected $view			= null;
	protected $context		= null;
	protected $pagination 	= null;

	protected $list			= null;
	protected $total		= null;

	public function __construct()
	{
		parent::__construct();

		$app = JFactory::getApplication();

		$this->option = $app->input->get('option','','CMD');
		$this->view   = $app->input->get('view','','CMD');
		$this->context = $this->option . '.categories';

		$this->populateState();
	}

	protected function populateState()
	{
    	$app = JFactory::getApplication();

		$search 			= $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$limit 				= $app->getUserStateFromRequest($this->context.'.limit', 'limit', '', 'string');
		$this->setState('limit', $limit);

		$limitstart 		= $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', '', 'string');
		$this->setState('limitstart', $limitstart);

		$filter_order		= $app->getUserStateFromRequest($this->context.'.filter.order', 'filter_order', 'k.created', 'string');
		$this->setState('filter.order', $filter_order);

		$filter_order_Dir	= $app->getUserStateFromRequest($this->context.'.filter.order_dir', 'filter_order_Dir', 'DESC', 'string');
		$this->setState('filter.order_dir', $filter_order_Dir);
  	}

	public function getList($override=false, $filter=true) {
		JPluginHelper::importPlugin('api');
		
		$dispatcher = JEventDispatcher::getInstance();
		$api_paths = ApiResource::addIncludePath();
		$methods = array ('get', 'post', 'put', 'delete', 'head');

		foreach ($api_paths as $path) {
			$resources = JFolder::files($path);
			foreach ($resources as $resource) {
				$item = new stdClass;
				$item->app = basename($path);
				$item->resource = JFile::stripExt(basename($resource));
				
				$class_name = ucfirst($item->app) . 'ApiResource' . ucfirst($item->resource);
				require_once $path . '/' . $resource;
				
				$class_methods = get_class_methods($class_name);
				$item->available_methods = array_intersect($methods, $class_methods);
				
				print_r($item->available_methods);
				
			}
		}
	}

}

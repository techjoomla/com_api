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

jimport('joomla.plugin.plugin');

abstract class ApiResource {

	protected $plugin;
	protected $allowed_methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD');

	public function __construct(ApiPlugin $plugin) {

		$this->plugin = $plugin;

	}

	final public function invoke() {
		$method_name	= $this->plugin->get('request_method');

		if (in_array($method_name, $this->allowed_methods) && method_exists($this, $method_name) && is_callable(array($this, $method_name))) :
			$this->$method_name();
		else :

			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_METHOD_NOT_FOUND'));
		endif;
	}

	final public static function getInstance($name, ApiPlugin $plugin, $prefix=null)
	{

		if (is_null($prefix))
		{
			$prefix = $plugin->get('component').'ApiResource';
		}

		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$resourceClass = $prefix.ucfirst($type);

		if (!class_exists( $resourceClass ))
		{
			jimport('joomla.filesystem.path');
			if($path = JPath::find(self::addIncludePath(), strtolower($type).'.php'))
			{
				require_once $path;

				if (!class_exists($resourceClass))
				{
					// Resource class not found
					return false;
				}
			}
			else
			{
				// Resource file not found
				return false;
			}
		}

		$instance = new $resourceClass($plugin);

		return $instance;
	}

	final public static function addIncludePath( $path=null )
	{
		static $paths;

		if ($paths === null)
		{
			$paths = array();
		}

		settype($path, 'array');

		if (!empty( $path ) && !in_array( $path, $paths ))
		{
			// loop through the path directories
			foreach ($path as $dir)
			{
				// no surrounding spaces allowed!
				$dir = trim($dir);

				// add to the top of the search dirs
				// so that custom paths are searched before core paths
				array_unshift($paths, $dir);
			}
		}

		return $paths;
	}

	final public static function getErrorResponse($code, $message, $newFormat = 0)
	{
		$error = new stdClass;

		if (!$newFormat)
		{
			$error->code = $code;
			$error->message = $message;
		}
		else
		{
			$error->err_code = $code;
			$error->err_message = $message;
			$error->data = new stdClass;
		}

		return $error;
	}
}

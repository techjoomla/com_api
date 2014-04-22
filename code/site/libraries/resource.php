<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
//print_r($this->plugin);die("in resource.php 1");
		if (in_array($method_name, $this->allowed_methods) && method_exists($this, $method_name) && is_callable(array($this, $method_name))) :
			$this->$method_name();
		else :

			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_METHOD_NOT_FOUND'));
		endif;
	}

	final public function getInstance($name, ApiPlugin $plugin, $prefix=null)
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
		//print_r($plugin);die("in apiresource file");
		$instance = new $resourceClass($plugin);

		return $instance;
	}

	final public function addIncludePath( $path=null )
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

	final public function getErrorResponse($code, $message)
	{
		$error = new stdClass;
		$error->code = $code;
		$error->message = $message;

		return $error;
	}
}

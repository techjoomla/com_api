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
jimport('joomla.filesystem.file');
jimport('joomla.application.component.helper');

/**
 * @API_plugin base class
 * API resource class 
*/
class ApiPlugin extends JPlugin {

	protected $user				= null;
	protected $format			= null;
	protected $response			= null;
	protected $request			= null;
	protected $request_method	= null;
	protected $request_headers	= null;
	protected $resource_acl		= array();
	protected $cache_folder		= 'com_api';
	protected $content_types	= array(
									'application/json' 	=> 'json',
									'application/xml'	=> 'xml'
								);

	static $instances		= array();
	static $plg_prefix		= 'plgAPI';
	static $plg_path		= '/plugins/api/';
	
	public $callbackname = 'callback';

	public static function getInstance($name)
	{
		$app = JFactory::getApplication();
		$param_path = JPATH_BASE.self::$plg_path.$name.'.xml';
		$plugin	= JPluginHelper::getPlugin('api', $name);
		
		if (isset(self::$instances[$name])) :
			return self::$instances[$name];
		endif;

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$dispatcher = JDispatcher::getInstance();
		}
		else
		{
			$dispatcher = JEventDispatcher::getInstance();
		}

		if (empty($plugin)) :
			ApiError::raiseError(400, JText::sprintf('COM_API_PLUGIN_CLASS_NOT_FOUND', ucfirst($name)));
		endif;

		$plgfile	= JPATH_BASE.self::$plg_path.$name.'/'.$name.'.php';
		if (!JFile::exists($plgfile)) :
			ApiError::raiseError(400, JText::sprintf('COM_API_FILE_NOT_FOUND', ucfirst($name)));
		endif;

		include_once $plgfile;
		$class 	= self::$plg_prefix.ucwords($name);

		if (!class_exists($class)) :
			ApiError::raiseError(400, JText::sprintf('COM_API_PLUGIN_CLASS_NOT_FOUND', ucfirst($name)));
		endif;

		$cparams = JComponentHelper::getParams('com_api');
		$handler = new $class($dispatcher, array('params'=>$cparams));
		$handler->set('params', $cparams);

		$call_methd = $app->input->server->get('REQUEST_METHOD','','STRING');

		//switch case for differ calling method
		switch ($call_methd)
		{
			case 'GET' :
							$handler->set('component', $app->input->get('app','','CMD'));
							$handler->set('resource', $app->input->get('resource','','CMD'));
							$handler->set('format', $handler->negotiateContent($app->input->get('output',null,'CMD')));
							break;

			case 'POST' :
							$handler->set('component', $app->input->get('app','','CMD'));
							$handler->set('resource', $app->input->get('resource','','CMD'));
							$handler->set('format', $handler->negotiateContent($app->input->get('output',null,'CMD')));
							break;

			case 'PUT' :
							$handler->set('component', $app->input->get('app','','CMD'));
							$handler->set('resource', $app->input->get('resource','','CMD'));
							$handler->set('format', $handler->negotiateContent($app->input->get('output',null,'CMD')));
							break;

			case 'DELETE':
							$handler->set('component', $app->input->get('app','','CMD'));
							$handler->set('resource', $app->input->get('resource','','CMD'));
							$handler->set('format', $handler->negotiateContent($app->input->get('output',null,'CMD')));
							break;

			case 'PATCH':
							$handler->set('component', $app->input->get('app','','CMD'));
							$handler->set('resource', $app->input->get('resource','','CMD'));
							$handler->set('format', $handler->negotiateContent($app->input->post->get('output',null,'CMD')));
							break;
		}

		$handler->set('request_method', $app->input->server->get('REQUEST_METHOD','','STRING'));

		self::$instances[$name] = $handler;

		return self::$instances[$name];
	}
	
	public function __construct(&$subject, $config = array())
	{
		//parent::__construct($subject, $config);
	}
	
	/**
	 * Intelligently negotiates the content type based on explicit declaration or header (HTTP_ACCEPT) declaration. If neither is present, it will default to the component parameter default.
	 * @param	string	$output	String content declaration (usually 'json' or 'xml')
	 * @return	string
	 */
	protected function negotiateContent($output=null)
	{
		$format = null;
		if (is_null($output) && isset($_SERVER['HTTP_ACCEPT']))
		{
			if (in_array($_SERVER['HTTP_ACCEPT'], array_keys($this->content_types)))
			{
				$format = $_SERVER['HTTP_ACCEPT'];
			}
		} elseif (in_array($output, $this->content_types)) {
			$flipped = array_flip($this->content_types);
			$format = $flipped[$output];
		}

		if (is_null($format))
		{
			$output = $this->params->get('default_content_type', 'json');
			$flipped = array_flip($this->content_types);
			$format = $flipped[$output];
		}

		return $format;
	}

	/**
	 * Sets the access level of a given resource
	 * @param	string	$resource	Resource name
	 * @param	string	$access		The access string (public or protected)
	 * @param	string	$method		The request method for the resource
	 * @return boolean
	 */
	final public function setResourceAccess($resource, $access, $method='GET') {
		$method = strtoupper($method);

		$this->resource_acl[$resource][$method] = $access;
		return true;
	}

	/**
	 * Checks the access level of a given resource
	 * @param	string	$resource	Resource name
	 * @param 	string	$method		The requested method for that resource
	 * @param	boolean $returnParamsDefault	Set to true to have the component parameters default access level returned if not explicitly set
	 * @return	mixed
	 */
	final public function getResourceAccess($resource, $method='GET', $returnParamsDefault=true) {
		$method = strtoupper($method);

		if (isset($this->resource_acl[$resource]) && isset($this->resource_acl[$resource][$method]))
		{
			return $this->resource_acl[$resource][$method];
		}
		else
		{
			if ($returnParamsDefault)
			{
				return $this->params->get('resource_access','protected');
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Finds and calls the requested resource
	 * @param	string	$resource_name	Requested resource name
	 * @return	string
	 */
	final public function fetchResource($resource_name=null) {

		if ($resource_name == null)
		{
			$resource_name = $this->get('resource');
		}

		$resource_obj = ApiResource::getInstance($resource_name, $this);

		if ($resource_obj === false)
		{
			$this->checkInternally($resource_name);
		}

		$user = APIAuthentication::authenticateRequest();
		$this->set('user', $user);
		$session = JFactory::getSession();
		$session->set('user', $user);

		$access = $this->getResourceAccess($resource_name, $this->request_method);
		if ($access == 'protected' && $user === false)
		{
			ApiError::raiseError(403, APIAuthentication::getAuthError());
		}

		if (!$this->checkRequestLimit())
		{
			ApiError::raiseError(403, JText::_('COM_API_RATE_LIMIT_EXCEEDED'));
		}

		$this->log();
		$this->lastUsed();

		if ($resource_obj !== false)
		{
			$resource_obj->invoke();
		}
		else
		{
			call_user_func(array($this, $resource_name));
		}

		$output		= $this->encode();

		return $output;
	}

	/**
	 * Checks to see if resource exists as a method on the main plugin
	 * @param	string	$resource_name	Requested resource name
	 * @return boolean
	 */
	final private function checkInternally($resource_name)
	{
		if (!method_exists($this, $resource_name))
		{
			ApiError::raiseError(404, JText::sprintf('COM_API_PLUGIN_METHOD_NOT_FOUND', ucfirst($resource_name)));
		}

		if (!is_callable(array($this, $resource_name)))
		{
			ApiError::raiseError(404, JText::sprintf('COM_API_PLUGIN_METHOD_NOT_CALLABLE', ucfirst($resource_name)));
		}

		return true;
	}

	/**
	 * Determines whether or not a request is over the time limit
	 * @return boolean
	 */
	final private function checkRequestLimit()
	{

		$app = JFactory::getApplication();

		$limit = $this->params->get('request_limit', 0);
		if ($limit == 0)
		{
			return true;
		}

		$hash = $app->input->get('key', '','STRING');
		$ip_address = $app->input->server->get('REMOTE_ADDR', '', 'STRING');

		$time = $this->params->get('request_limit_time', 'hour');
		switch($time)
		{
			case 'day':
			$offset = 60*60*24;
			break;

			case 'minute':
			$offset = 60;
			break;

			case 'hour':
			default:
			$offset = 60*60;
			break;
		}

		$query_time = time() - $offset;

		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__api_logs "
				."WHERE `time` >= ".$db->Quote($query_time)." "
				."AND (`hash` = ".$db->Quote($hash)." OR `ip_address` = ".$db->Quote($ip_address).")"
				;

		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result >= $limit)
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	/**
	 * Logs the incoming request to the database
	 */
	final private function log()
	{
		if (!$this->params->get('log_requests')) { return; }
		
		$app = JFactory::getApplication();
		//vg - for exclude password from log
		$params = JComponentHelper::getParams('com_api');
		$excludes = $params->get('exclude_log');
		$pst_dt = $app->input->post->getArray(array());
		$exld_arr = explode(",",$excludes);
		$req_url = JFactory::getURI()->getQuery();

		foreach($exld_arr as $val)
		{
			if(!empty($pst_dt[$val]))
			{
				//unset($pst_dt[$val]);
				$pst_dt[$val] = '******';
			}
			//for get call
			if(strpos($req_url,$val))
			{
				$req_arr = explode("&",$req_url);
				foreach($req_arr as $ky => $re_nd)
				{
					$pos = strpos($re_nd,$val);
					if($pos === 0)
					{
						$req_arr[$ky] = $val.'=******';
					}
				}
				$req_url = implode('&',$req_arr);
			}
		}
		//end

		$table = JTable::getInstance('Log', 'ApiTable');
		$date = JFactory::getDate();
		
		$table->hash = $app->input->get('key', '','STRING');
		$table->ip_address = $app->input->server->get('REMOTE_ADDR', '', 'STRING');
		$table->time = $date->toSql();
		$table->request = $req_url;

		//$table->post_data = $app->input->post->getArray(array());
		$table->post_data = $pst_dt;
		$table->store();
	}
	
	/**
	 * Sets the last updated time for a key
	 */
	final private function lastUsed()
	{
		$app = JFactory::getApplication();
		$table = JTable::getInstance('Key', 'ApiTable');

		$hash = $app->input->get('key', '','STRING');
		$table->setLastUsed($hash);
	}

	/**
	 * Setter method for $response instance variable
	 * @param	mixed	$data	The plugin's output
	 */
	public function setResponse($data)
	{
		$this->set('response', $data);
	}

	/**
	 * Determines the method with which to encode the output based on the requested content type
	 * @return string
	 */
	public function encode()
	{
		$document = JFactory::getDocument();
		$document->setMimeEncoding($this->format);

		$format_name = $this->content_types[$this->format];
		$method = 'to'.ucfirst($format_name);

		if (!method_exists($this, $method))
		{
			ApiError::raiseError(406, JText::_('COM_API_PLUGIN_NO_ENCODER'));
		}

		if (!is_callable(array($this, $method)))
		{
			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_NO_ENCODER'));
		}

		return $this->$method();
	}

	/**
	 * Transforms the plugin response to a JSON-encoded string
	 * Can also return JSONP if the callback is set
	 * @return string
	 */
	protected function toJson()
	{
		$app = JFactory::getApplication();
		$callback = $app->input->get($this->callbackname, '');
		
		if ($callback) {
			return $callback . '(' . json_encode($this->get('response')) . ')';
		} else {
			return json_encode($this->get('response'));
		}
	}

	/**
	 * Transforms the plugin response to an XML string
	 * @return string
	 */
	protected function toXml()
	{
		$response = $this->get('response');
		$xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		$this->_toXMLRecursive($response, $xml);

		return $xml->asXML();
	}

	protected function _toXMLRecursive($element, &$xml) {

		if (!is_array($element) && !is_object($element)) :
			return null;
		endif;

		if (is_object($element)) :
			$element = get_object_vars($element);
		endif;

		foreach($element as $key => $value) :
			$this->_handleMultiDimensions($key, $value, $xml);
		endforeach;
	}
	
	protected function _handleMultiDimensions($key, $value, &$xml)
	{
		if (is_array($value) || is_object($value)) :
			$node = $xml->addChild($key);
			$this->_toXMLRecursive($value, $node);
		else :
			$node = $xml->addChild($key, htmlspecialchars($value));
		endif;
	}

/**
	* Method to get current logged in API user
	* @return JUser
 */
	public function getUser()
	{
		return $this->user;
	}
}

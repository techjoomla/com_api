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

class ApiPlugin extends JPlugin
{
	protected $user               = null;
	public    $params             = null;
	protected $format             = null;
	protected $response           = null;
	protected $request            = null;
	protected $request_method     = null;
	protected $request_headers    = null;
	protected $skip_request_limit = false;
	protected $resource_acl       = array();
	protected $cache_folder       = 'com_api';
	protected $content_types      = array(
		'application/json' 	=> 'json',
		'application/xml'	=> 'xml'
		);

	static	$instances  = array();
	static	$plg_prefix = 'plgAPI';
	static	$plg_path   = '/plugins/api/';

	public static function getInstance($name) 
	{	
		if ( isset( self::$instances[$name] ) ) {
			return self::$instances[$name];
		}

		$plugin	= JPluginHelper::getPlugin( 'api', $name );

		if ( empty( $plugin ) ) {
			throw new Exception( JText::_( 'COM_API_PLUGIN_CLASS_NOT_FOUND' ), 400 );
		}

		jimport( 'joomla.filesystem.file' );

		$plgfile	= JPATH_BASE.self::$plg_path.$name.DS.$name.'.php';
		$param_path = JPATH_BASE.self::$plg_path.$name.DS.$name.'.xml';

		if ( !JFile::exists( $plgfile ) ) {
			throw new Exception( JText::_( 'COM_API_FILE_NOT_FOUND' ), 400 );
		}

		include $plgfile;
		$class 	= self::$plg_prefix . ucwords( $name );

		if ( !class_exists( $class ) ) {
			throw new Exception( JText::_( 'COM_API_PLUGIN_CLASS_NOT_FOUND' ), 400 );
		}

		$handler =  new $class();

		$cparams = JComponentHelper::getParams( 'com_api' );
		$params  = new JRegistry;
		$params->loadString( $plugin->params );
		$cparams->merge( $params );

		$handler->set( 'params', $cparams );
		$handler->set( 'component', JRequest::getCmd( 'app' ) );
		$handler->set( 'resource', JRequest::getCmd( 'resource' ) );
		$handler->set( 'format', $handler->negotiateContent( JRequest::getCmd( 'output', null ) ) );
		$handler->set( 'request_method', JRequest::getMethod() );

		self::$instances[$name] = $handler;

		return self::$instances[$name];
	}

	public function __construct()
	{
		
	}

	//public function __call($name, $arguments) {
	//	throw new Exception( JText::_('COM_API_PLUGIN_METHOD_UNREACHABLE'), 400 );
	//}
	
	/**
	 * Intelligently negotiates the content type based on explicit declaration or header (HTTP_ACCEPT) declaration. If neither is present, it will default to the component parameter default.
	 * @param	string	$output	String content declaration (usually 'json' or 'xml')
	 * @return	string
	 */
	protected function negotiateContent( $output = null )
	{
		$format = null;
		if ( is_null( $output ) && isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			if ( in_array( $_SERVER['HTTP_ACCEPT'], array_keys( $this->content_types ) ) ) {
				$format = $_SERVER['HTTP_ACCEPT'];
			}
		} elseif ( in_array( $output, $this->content_types ) ) {
			$flipped = array_flip( $this->content_types );
			$format = $flipped[$output]; 
		}

		if ( is_null( $format ) ) {
			$output = $this->params->get( 'default_content_type', 'json' );
			$flipped = array_flip( $this->content_types );
			$format = $flipped[$output];
		}

		return $format;
	}

	/**
	 * Sets the access level of a given resource
	 * @param	mixed	$resource	Resource name
	 * @param	string	$access		The access string (public or protected)
	 * @param	string	$method		The request method for the resource
	 * @return boolean
	 */
	final public function setResourceAccess( $resources, $access, $method = 'GET' ) {
		$method = strtoupper( $method );

		foreach ( (array) $resources as $resource ) {
			$this->resource_acl[$resource][$method] = $access;
		}

		return true;
	}
	
	/**
	 * Checks the access level of a given resource
	 * @param	string	$resource	Resource name
	 * @param 	string	$method		The requested method for that resource
	 * @param	boolean $returnParamsDefault	Set to true to have the component parameters default access level returned if not explicitly set
	 * @return	mixed
	 */
	final public function getResourceAccess( $resource, $method = 'GET', $returnParamsDefault = true )
	{
		$method = strtoupper( $method );

		if ( isset( $this->resource_acl[$resource] ) && isset( $this->resource_acl[$resource][$method] ) ) {
			return $this->resource_acl[$resource][$method];
		} else {
			if ( $returnParamsDefault ) {
				return $this->params->get( 'resource_access', 'protected' );
			} else {
				return false;
			}
		}
	}

	/**
	 * Finds and calls the requested resource
	 * @param	string	$resource_name	Requested resource name
	 * @return	string
	 */
	final public function fetchResource( $resource_name = null )
	{
		if ( $resource_name == null ) {
			$resource_name = $this->get( 'resource' );
		}

		$resource_obj = ApiResource::getInstance( $resource_name, $this );

		if ( $resource_obj === false ) {
			$this->checkInternally( $resource_name );
		}

		$access = $this->getResourceAccess( $resource_name, $this->request_method );

		if ( $access == 'protected' ) {
			$auth_handler = APIAuthentication::getInstance();
			$user = $auth_handler->authenticateRequest();
			if ( $user === false ) {
				throw new Exception( $auth_handler->getError(), 403 );
			}

			$this->set( 'user', $user );
		}

		if ( !$this->checkRequestLimit() )  {
			throw new Exception( JText::_('COM_API_RATE_LIMIT_EXCEEDED'), 403 );
		}

		$this->log();

		if ($resource_obj !== false) {
			$resource_obj->invoke();
		} else {
			call_user_func( array( $this, $resource_name ) );
		}
        $output= array();
        $output = $this->encode();
		
		return $output;
	}
	
	/**
	 * Checks to see if resource exists as a method on the main plugin
	 * @param	string	$resource_name	Requested resource name
	 * @return boolean
	 */
	final private function checkInternally( $resource_name )
	{
		if ( !method_exists( $this, $resource_name ) ) {
			throw new Exception( JText::_('COM_API_PLUGIN_METHOD_NOT_FOUND'), 404 );
		}

		if ( !is_callable( array( $this, $resource_name ) ) ) {
			throw new Exception( JText::_('COM_API_PLUGIN_METHOD_NOT_CALLABLE'), 404 );
		}

		return true;
	}
	
	/**
	 * Determines whether or not a request is over the time limit
	 * @return boolean
	 */
	final private function checkRequestLimit() 
	{
		$limit = $this->params->get( 'request_limit', 0 );
		if ( $limit == 0 || $this->get('skip_request_limit', false) ) {
			return true;
		}

		$hash = JRequest::getVar( 'key', '' );
		$ip_address = JRequest::getVar( 'REMOTE_ADDR', '', 'server' );

		$time = $this->params->get( 'request_limit_time', 'hour' );
		switch( $time ) {
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

		if ( $result >= $limit ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Logs the incoming request to the database
	 */
	final private function log() 
	{
		$table = JTable::getInstance( 'Log', 'ApiTable' );
		$table->hash = JRequest::getVar( 'key', '' );
		$table->ip_address = JRequest::getVar( 'REMOTE_ADDR', '', 'server' );
		$table->time = time();
		$table->request = JFactory::getURI()->getQuery();
		$table->store();
	}
	
	/**
	 * Setter method for $response instance variable
	 * @param	mixed	$data	The plugin's output
	 */
	public function setResponse( $data ) 
	{
		$this->set( 'response', $data );
	}
	
	/**
	 * Determines the method with which to encode the output based on the requested content type
	 * @return string
	 */
	public function encode() 
	{	
		$document = JFactory::getDocument();
		$document->setMimeEncoding( $this->format );

		$format_name = $this->content_types[$this->format];
		$method = 'to' . ucfirst( $format_name );

		if ( !method_exists( $this, $method ) ) {
			throw new Exception( JText::_( 'COM_API_PLUGIN_NO_ENCODER' ), 406 );
		}

		if ( !is_callable( array( $this, $method ) ) ) {
			throw new Exception( JText::_( 'COM_API_PLUGIN_NO_ENCODER' ), 404 );
		}

		return $this->$method();
	}

	/**
	 * Transforms the plugin response to a JSON-encoded string
	 * @return string
	 */
	protected function toJson() 
	{
		$json = json_encode($this->get('response'));
		$app = JFactory::getApplication();
		$callback = $app->input->get('callback');
		if ($callback) {
			return $callback . '(' . $json . ')';
		} else {
			return $json;
		}
		
	}
	
	/**
	 * Transforms the plugin response to an XML string
	 * @return string
	 */
	protected function toXml() 
	{
		$response = $this->get( 'response' );
		$xml = new SimpleXMLElement( '<?xml version="1.0"?><response></response>' );

		$this->_toXMLRecursive( $response, $xml );

		return $xml->asXML();
	}

	protected function _toXMLRecursive( $element, &$xml )
	{
		if ( !is_array( $element ) && !is_object( $element ) ) {
			return null;
		}

		if ( is_object( $element ) ) {
			$element = get_object_vars( $element );
		}

		foreach ( $element as $key => $value ) {
			$this->_handleMultiDimensions( $key, $value, $xml );
		}
	}

	protected function _handleMultiDimensions( $key, $value, &$xml )
	{
		if ( is_array( $value ) || is_object( $value ) ) {
			$node = $xml->addChild( $key );
			$this->_toXMLRecursive( $value, $node );
		} else {
			$node = $xml->addChild( $key, htmlspecialchars( $value ) );
		}
	}
}

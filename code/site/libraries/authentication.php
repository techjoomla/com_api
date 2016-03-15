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

abstract class ApiAuthentication extends JObject {

	protected	$auth_method		= null;
	protected	$domain_checking	= null;
	static		$auth_errors		= array();

	public function __construct($params) {

		parent::__construct();

		$app = JFactory::getApplication();
		$key = $app->input->get('key','','STRING');
		
		if(empty($key))
		{
			$key = $app->input->post->get('key','','STRING');
		}

		if(empty($key))
		{
			$this->set('auth_method',$params->get('auth_method','username'));
			$this->set('auth_method',$params->get('auth_method','password'));
			$this->set('auth_method', $params->get('auth_method', 'login'));
		}
		else
		{
			$this->set('auth_method', $params->get('auth_method', 'key'));
		}
		$this->set('domain_checking', $params->get('domain_checking', 1));
  }

	abstract public function authenticate();

	public static function authenticateRequest() {
		$params			= JComponentHelper::getParams('com_api');
		$app = JFactory::getApplication();
		
		$key = $app->input->get('key','','STRING');
		
		if(empty($key))
		{
			$key = $app->input->post->get('key','','STRING'); 
		}

		if(!empty($key))
		$method			= 'key';
		else
		$method			= 'login';

		$className 		= 'APIAuthentication'.ucwords($method);

		$auth_handler 	= new $className($params);

		$user_id		= $auth_handler->authenticate();
		
		if ($user_id === false) :
			self::setAuthError($auth_handler->getError());
			return false;
		else :
			$user	= JFactory::getUser($user_id);
			if (!$user->id) :
				self::setAuthError(JText::_("COM_API_USER_NOT_FOUND"));
				return false;
			endif;

			if ($user->block == 1) :
				self::setAuthError(JText::_("COM_API_BLOCKED_USER"));
				return false;
			endif;
			
			// v 1.8.1 - to set admin info headers
			//$log_user = JFactory::getUser();
			$isroot = $user->authorise('core.admin');

			if($isroot)
			{
				JResponse::setHeader( 'x-api', self::getCom_apiVersion());
				JResponse::setHeader( 'x-plugins', implode(',',self::getPluginsList()) );
			}
			//
			
			return $user;

		endif;

	}
	
	public static function setAuthError($msg) {
		self::$auth_errors[] = $msg;
		return true;
	}

	public static function getAuthError() {
		if (empty(self::$auth_errors)) :
			return false;
		endif;
		return array_pop(self::$auth_errors);
	}
	
	//v- 1.8.1 get all api type plugin versions
	public static function getPluginsList()
	{
		$plgs = JPluginHelper::getPlugin('api');
		$plg_arr = array();
		foreach($plgs as $plg)
		{
			$xml = JFactory::getXML(JPATH_SITE.'/plugins/api/'.$plg->name.'/'.$plg->name.'.xml');
			$version = (string)$xml->version;
			$plg_arr[] = $plg->name.'-'.$version;
		}
		return $plg_arr;
	}
	
	//get com_api version
	public static function getCom_apiVersion()
	{
		$xml = JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_api/api.xml');
		return $version = (string)$xml->version;
	}

}

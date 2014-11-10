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

class APICache {

	const CACHE_GROUP 	= 'com_api';
	const DAY			= 86400;
	const HALF_DAY		= 43200;
	const HOUR			= 3600;
	const MINUTE		= 60;
	
	public static function callback($object, $method, $args=array(), $cache_lifetime=null, $overrideConfig=false) {
		
		$conf 			= JFactory::getConfig();
		$cacheactive 	= $conf->getValue('config.caching');
		$cachetime		= $conf->getValue('config.cachetime');
		
		$cache= & JFactory::getCache(self::CACHE_GROUP,'callback');

		if ($overrideConfig) :
			$cache->setCaching(1); //enable caching
		endif;

		if ($cache_lifetime) :
			$cache->setLifeTime($cache_lifetime);
		endif;
		
		$callback	= array(array($object, $method));
		$cache_args = array_merge($callback, $args);
		$data = call_user_func_array(array($cache, 'call'), $cache_args);
		
		if ($overrideConfig) :
			$cache->setCaching($cacheactive);
		endif;
		
		if ($cache_lifetime) :
			$cache->setLifeTime($cachetime);
		endif;
		
		return $data;
	}
	
}

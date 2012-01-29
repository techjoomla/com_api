<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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

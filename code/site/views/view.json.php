<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class APIViewJSON {
	
	public function __construct() {
		
	}
		
	public static function display($data) {

		$response = new stdClass;

		if($data['err_code']) {
			$response->err_code 	= $data['err_code'];
			$response->err_message  = $data['err_message'];
			$response->data 		= [];
		} else {			
			$response->err_code 	= "";
			$response->err_message  = "";
 			$response->data 		= $data['data'];
 		}

		return json_encode($response);
	}
}

<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class APIHelper
{
	function getAPIUserID()
	{
		static $user_id;
                           
		if ( !$user_id ) {
			$user_id = APIAuthentication::getInstance()->authenticate();
		}

		return $user_id;
	}

	function setSessionUser()
	{
		$session  =& JFactory::getSession();
		$session->set( 'user', JUser::getInstance( APIHelper::getAPIUserID() ) );
	}

	function unsetSessionUser()
	{
		$session  =& JFactory::getSession();
		$session->clear( 'user' );
	}
}

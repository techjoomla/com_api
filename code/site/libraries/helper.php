<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Rafael Corral
 * @link 	http://www.corephp.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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

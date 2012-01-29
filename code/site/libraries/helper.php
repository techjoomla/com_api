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

	function setSessionUser( $user_id = false )
	{
		if ( false === $user_id ) {
			$user_id = APIHelper::getAPIUserID();
		}

		$session =& JFactory::getSession();
		$session->set( 'user', JUser::getInstance( $user_id ) );
	}

	function unsetSessionUser()
	{
		$session  =& JFactory::getSession();
		$session->clear( 'user' );
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_api';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ( $actions as $action ) {
			$result->set( $action, $user->authorise( $action, $assetName ) );
		}

		return $result;
	}

}
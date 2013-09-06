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
jimport( 'joomla.application.component.model' );

class ApiAuthenticationKey extends ApiAuthentication
{
	protected $auth_method     = null;
	protected $domain_checking = null;

	public function authenticate()
	{
		$key   = JRequest::getVar( 'key' );
		$token = $this->loadTokenByHash( $key );

		if ( !$token ) {
			$this->setError( JText::_( 'COM_API_KEY_NOT_FOUND' ) );
			return false;
		}
		
		if ( !$token->published ) {
			$this->setError( JText::_( 'COM_API_KEY_DISABLED' ) );
			return false;
		}

		return $token->user_id;
	}

	public function loadTokenByHash( $hash )
	{
		$db = JFactory::getDBO();
		$db->setQuery( "SELECT * FROM #__api_keys WHERE hash = " . $db->Quote( $hash ) );
		$token = $db->loadObject();

		return $token;
	}
}
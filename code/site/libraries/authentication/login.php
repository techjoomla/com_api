<?php 
/**
 * @package	API
 * @version 1.5
 * @author 	Rafael Corral
 * @link 	http://www.rafaelcorral.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;

class ApiAuthenticationLogin extends ApiAuthentication
{
	protected $auth_method     = null;
	protected $domain_checking = null;

	public function authenticate()
	{
		$username = JRequest::getVar( 'username' );
		$password = JRequest::getVar( 'password' );
		$user = $this->loadUserByCredentials( $username, $password );
		
     
		if ( $user === false ) {
			$this->setError(JText::_('Username/password does not match'));
			return false;
		}
		
		return $user;
	}

	public function loadUserByCredentials( $user, $pass )
	{
		jimport('joomla.user.authentication');

		$authenticate = JAuthentication::getInstance();   
		$response = $authenticate->authenticate(array( 'username' => $user, 'password' => $pass ));
		
		//if ($response === true) {
		if ($response->status == 1) {
		
		    $app = JFactory::getApplication();    
			$response = $app->login(array('username'=>$user, 'password'=>$pass));
			
			$db = JFactory::getDBO();
			$db->setQuery("SELECT id FROM #__users WHERE username = " . $db->Quote($user));
			$userid = $db->loadResult();	
			$app->logout();
			return $userid;
		} else {
		
			return false;
		}
		
		
	}
}

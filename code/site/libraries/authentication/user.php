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

use Joomla\CMS\Factory;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\User\User;

class ApiAuthenticationUser extends ApiAuthentication
{
	protected $auth_method     = null;
	protected $domain_checking = null;

	public function authenticate()
	{
		$app = Factory::getApplication();

		$username = $app->input->post->get('username','','STRING');
		$password = $app->input->post->get('password','','STRING');

		//$username = Factory::getApplication()->input->get( 'username' );
		//$password = Factory::getApplication()->input->get( 'password' );

		$user = $this->loadUserByCredentials( $username, $password );

		// Remove username and password from request for when it gets logged
		$uri = Factory::getURI();
		$uri->delVar('username');
		$uri->delVar('password');

		if ( $user === false ) {
			// Errors are already set, just return
			return false;
		}

		return $user->id;
	}

	public function loadUserByCredentials( $user, $pass )
	{

		$authenticate = Authentication::getInstance();
		$response = $authenticate->authenticate(array( 'username' => $user, 'password' => $pass ));

		if ($response->status === Authentication::STATUS_SUCCESS) {
			$instance = User::getInstance($response->username);
			if ( $instance === false ) {
				$this->setError( JError::getError() );
				return false;
			}
		} else {
			if ( isset( $response->error_message ) ) {
				$this->setError( $response->error_message );
			}else {
				$this->setError( $response->getError() );
			}

			return false;
		}

		return $instance;
	}
}

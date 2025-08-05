<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_api
 *
 * @copyright   Copyright (C) 2024 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

/**
 * API Login Authentication class
 *
 * @since  1.0.0
 */
class ApiAuthenticationLogin extends ApiAuthentication
{
	/**
	 * Authentication method
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $auth_method = null;

	/**
	 * Domain checking enabled
	 *
	 * @var    bool
	 * @since  1.0.0
	 */
	protected $domain_checking = null;

	/**
	 * Method to check authentication
	 *
	 * @return  int  User ID on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function authenticate()
	{
		$app = Factory::getApplication();

		$username = $app->input->post->get('username', '', 'STRING');
		$password = $app->input->post->get('password', '', 'STRING');

		$userId = $this->loadUserByCredentials($username, $password);

		// Remove username and password from request for security
		$app->input->set('username', null);
		$app->input->set('password', null);

		if ($userId === false)
		{
			// Errors are already set, just return

			return false;
		}

		return $userId;
	}

	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @param   STRING  $user  user
	 * @param   STRING  $pass  pass
	 *
	 * @return  int
	 *
	 * @since	1.6
	 */
	public function loadUserByCredentials($user, $pass)
	{
		$authenticate = Authentication::getInstance();

		$response = $authenticate->authenticate(['username' => $user, 'password' => $pass], []);

		if ($response->status === Authentication::STATUS_SUCCESS)
		{
			$userId = UserHelper::getUserId($response->username);

			if ($userId === false)
			{
				$this->setError(Text::_('JERROR_LOGIN_DENIED'));
				return false;
			}
		}
		else
		{
			if (isset($response->error_message))
			{
				$this->setError($response->error_message);
			}
			else
			{
				$this->setError(Text::_('JERROR_AUTHENTICATION_FAILED'));
			}

			return false;
		}

		return $userId;
	}
}

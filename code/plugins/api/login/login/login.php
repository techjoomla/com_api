<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  API.Login
 *
 * @copyright   Copyright (C) 2005 - 2025 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Date\Date;

/**
 * Login API Resource for Joomla 5
 *
 * @package     Joomla.Plugin
 * @subpackage  API.Login
 * @since       5.0
 */
class LoginApiResourceLogin extends ApiResource
{
	/**
	 * Handle POST requests for login
	 *
	 * @return  void
	 * @since   5.0
	 */
	public function post()
	{
		$this->plugin->setResponse($this->login());
	}

	/**
	 * Handle GET requests for login (for testing)
	 *
	 * @return  void
	 * @since   5.0
	 */
	public function get()
	{
		$this->plugin->setResponse($this->login());
	}

	/**
	 * Perform user login
	 *
	 * @return  array
	 * @since   5.0
	 */
	private function login()
	{
		try {
			$app = Factory::getApplication();
			$input = $app->getInput();

			// Get credentials
			$username = $input->getString('username', '');
			$password = $input->getString('password', '');

			if (empty($username) || empty($password)) {
				return array(
					'success' => false,
					'message' => 'Username and password required',
					'data' => null
				);
			}

			// Prepare credentials
			$credentials = array(
				'username' => $username,
				'password' => $password
			);

			// Authenticate user
			$authenticate = Authentication::getInstance();
			$response = $authenticate->authenticate($credentials);

			if ($response->status !== Authentication::STATUS_SUCCESS) {
				return array(
					'success' => false,
					'message' => 'Invalid credentials',
					'data' => null
				);
			}

			// Get user by username
			$userId = UserHelper::getUserId($username);
			if (!$userId) {
				return array(
					'success' => false,
					'message' => 'User not found',
					'data' => null
				);
			}

			$user = User::getInstance($userId);

			if (!$user->id || $user->block) {
				return array(
					'success' => false,
					'message' => 'User account is blocked or not found',
					'data' => null
				);
			}

			// Generate API key
			$apiKey = $this->generateApiKey($user->id);

			return array(
				'success' => true,
				'message' => 'Login successful',
				'data' => array(
					'auth' => $apiKey,
					'user_id' => $user->id,
					'username' => $user->username,
					'name' => $user->name,
					'email' => $user->email,
					'groups' => $user->getAuthorisedGroups()
				)
			);

		} catch (Exception $e) {
			return array(
				'success' => false,
				'message' => 'Login error: ' . $e->getMessage(),
				'data' => null
			);
		}
	}

	/**
	 * Generate API key for user
	 *
	 * @param   int  $userId  User ID
	 * @return  string
	 * @since   5.0
	 */
	private function generateApiKey($userId)
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$date = new Date();

		// Generate random key
		$key = UserHelper::genRandomPassword(32);
		$hash = md5($key . $userId . time());

		// Check if user already has a key
		$query = $db->getQuery(true)
			->select('id')
			->from('#__api_keys')
			->where('userid = ' . (int) $userId);

		$db->setQuery($query);
		$existingKey = $db->loadResult();

		if ($existingKey) {
			// Update existing key
			$query = $db->getQuery(true)
				->update('#__api_keys')
				->set('hash = ' . $db->quote($hash))
				->set('created = ' . $db->quote($date->toSql()))
				->where('userid = ' . (int) $userId);
		} else {
			// Insert new key
			$query = $db->getQuery(true)
				->insert('#__api_keys')
				->columns('userid, domain, state, created, created_by, hash')
				->values((int) $userId . ', ' . $db->quote('*') . ', 1, ' . $db->quote($date->toSql()) . ', ' . (int) $userId . ', ' . $db->quote($hash));
		}

		$db->setQuery($query);
		$db->execute();

		return $hash;
	}
}

<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  API.Users
 *
 * @copyright   Copyright (C) 2005 - 2025 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\DatabaseInterface;

/**
 * Users API Resource for Joomla 5
 *
 * @package     Joomla.Plugin
 * @subpackage  API.Users
 * @since       5.0
 */
class UsersApiResourceUsers extends ApiResource
{
	/**
	 * Get users list or specific user
	 *
	 * @return  void
	 * @since   5.0
	 */
	public function get()
	{
		try {
			$app = Factory::getApplication();
			$input = $app->getInput();
			
			$id = $input->getInt('id', 0);
			$limit = $input->getInt('limit', 20);
			$offset = $input->getInt('offset', 0);
			
			if ($id > 0) {
				// Get specific user
				$result = $this->getUserById($id);
			} else {
				// Get users list
				$result = $this->getUsersList($limit, $offset);
			}
			
			$this->plugin->setResponse($result);
			
		} catch (Exception $e) {
			$this->plugin->setResponse(
				array(
					'success' => false,
					'message' => $e->getMessage(),
					'data' => null
				)
			);
		}
	}
	
	/**
	 * Get user by ID
	 *
	 * @param   int  $id  User ID
	 * @return  array
	 * @since   5.0
	 */
	private function getUserById($id)
	{
		$user = User::getInstance($id);
		
		if (!$user->id) {
			throw new Exception('User not found', 404);
		}
		
		$userData = array(
			'id' => $user->id,
			'name' => $user->name,
			'username' => $user->username,
			'email' => $user->email,
			'block' => $user->block,
			'registerDate' => $user->registerDate,
			'lastvisitDate' => $user->lastvisitDate,
			'groups' => $user->getAuthorisedGroups(),
			'guest' => $user->guest
		);
		
		return array(
			'success' => true,
			'message' => 'User retrieved successfully',
			'data' => $userData
		);
	}
	
	/**
	 * Get users list
	 *
	 * @param   int  $limit   Limit
	 * @param   int  $offset  Offset
	 * @return  array
	 * @since   5.0
	 */
	private function getUsersList($limit, $offset)
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		
		$query = $db->getQuery(true)
			->select('id, name, username, email, block, registerDate, lastvisitDate')
			->from('#__users')
			->where('block = 0')
			->order('name ASC');
		
		if ($limit > 0) {
			$query->setLimit($limit, $offset);
		}
		
		$db->setQuery($query);
		$users = $db->loadAssocList();
		
		// Get total count
		$countQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__users')
			->where('block = 0');
		
		$db->setQuery($countQuery);
		$total = $db->loadResult();
		
		return array(
			'success' => true,
			'message' => 'Users retrieved successfully',
			'data' => array(
				'users' => $users,
				'total' => $total,
				'limit' => $limit,
				'offset' => $offset
			)
		);
	}
	
	/**
	 * Create new user (POST)
	 *
	 * @return  void
	 * @since   5.0
	 */
	public function post()
	{
		try {
			$app = Factory::getApplication();
			$input = $app->getInput();
			
			// Get data from request
			$data = array();
			$data['name'] = $input->getString('name', '');
			$data['username'] = $input->getString('username', '');
			$data['email'] = $input->getString('email', '');
			$data['password'] = $input->getString('password', '');
			$data['password2'] = $input->getString('password2', '');
			
			// Basic validation
			if (empty($data['name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
				throw new Exception('Missing required fields: name, username, email, password');
			}
			
			if ($data['password'] !== $data['password2']) {
				throw new Exception('Password confirmation does not match');
			}
			
			// Create user
			$user = new User();
			$user->set('name', $data['name']);
			$user->set('username', $data['username']);
			$user->set('email', $data['email']);
			$user->set('password', $data['password']);
			$user->set('block', 0);
			$user->set('groups', array(2)); // Registered user group
			
			if (!$user->save()) {
				throw new Exception('Failed to create user: ' . $user->getError());
			}
			
			$result = array(
				'success' => true,
				'message' => 'User created successfully',
				'data' => array(
					'id' => $user->id,
					'name' => $user->name,
					'username' => $user->username,
					'email' => $user->email
				)
			);
			
			$this->plugin->setResponse($result);
			
		} catch (Exception $e) {
			$this->plugin->setResponse(
				array(
					'success' => false,
					'message' => $e->getMessage(),
					'data' => null
				)
			);
		}
	}
}

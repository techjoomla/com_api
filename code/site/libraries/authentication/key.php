<?php
/**
 * @package    Techjoomla.API
 * @copyright  Copyright (C) 2009-2017 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.model');

/**
 * API resource class
 *
 * @since  1.0
 */
class ApiAuthenticationKey extends ApiAuthentication
{
	protected	$auth_method		= null;

	protected	$domain_checking	= null;

	/**
	 * Authenticate the user using the key in the header or request
	 *
	 * @return  string  User id of the user or false
	 */
	public function authenticate()
	{
		$app          = JFactory::getApplication();
		$query_token  = $app->input->get('key', '', 'STRING');
		$header_token = $this->getBearerToken();
		$key          = $header_token ? $header_token : $query_token;
		$token        = $this->loadTokenByHash($key);

		if (isset($token->state) && $token->state == 1)
		{
			// Get user for this key
			$user         = JFactory::getUser($token->userid);
			$isSuperAdmin = $user->authorise('core.admin');

			// If this user is super admin user
			if ($isSuperAdmin)
			{
				$userToImpersonate = self::getUserToImpersonate();

				// If other is to be impersonated
				if ($userToImpersonate)
				{
					$searchFor      = '';
					$searchForValue = '';

					if (preg_match('/email:(\S+)/', $userToImpersonate, $matches))
					{
						$searchFor      = 'email';
						$searchForValue = $matches[1];
					}
					elseif (preg_match('/username:(\S+)/', $userToImpersonate, $matches))
					{
						$searchFor      = 'username';
						$searchForValue = $matches[1];
					}
					elseif (is_numeric($userToImpersonate))
					{
						$userId = $userToImpersonate;
					}
					else
					{
						ApiError::raiseError("400", JText::_('COM_API_USER_NOT_FOUND'), 'APIValidationException');

						return false;
					}

					// If username or emailid exists ?
					if ($searchFor)
					{
						$db = JFactory::getDbo();
						$query = $db->getQuery(true)
							->select($db->quoteName('id'))
							->from($db->quoteName('#__users'))
							->where($db->quoteName($searchFor) . ' = ' . $db->quote($searchForValue));
						$db->setQuery($query);

						if ($id = $db->loadResult())
						{
							return $id;
						}
						else
						{
							ApiError::raiseError("400", JText::_('COM_API_USER_NOT_FOUND'), 'APIValidationException');

							return false;
						}
					}
					// If userid exists ?
					elseif ($userId)
					{
						$table = JUser::getTable();

						if ($table->load($userId))
						{
							return $userId;
						}
						else
						{
							ApiError::raiseError("400", JText::_('COM_API_USER_NOT_FOUND'), 'APIValidationException');

							return false;
						}
					}
				}
			}

			return $token->userid;
		}

		$this->setError(JText::_('COM_API_KEY_NOT_FOUND'));

		return false;
	}

	/**
	 * Load a token row using hash
	 *
	 * @param   STRING  $hash  The token hash
	 *
	 * @return  OBJECT
	 */
	public function loadTokenByHash($hash)
	{
		$table = JTable::getInstance('Key', 'ApiTable');
		$table->loadByHash($hash);

		return $table;
	}
}

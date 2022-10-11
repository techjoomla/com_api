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

use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;

/**
 * Class for API authetication
 *
 * @since  1.0
 */
abstract class ApiAuthentication extends CMSObject
{
	protected $auth_method     = null;

	protected $domain_checking = null;

	public static $auth_errors = array();

	/**
	 * Constructor
	 *
	 * @param   object  $params  config
	 *
	 * @since 1.0
	 */
	public function __construct($params)
	{
		parent::__construct();

		$this->set('auth_method', self::getAuthMethod());
		$this->set('domain_checking', $params->get('domain_checking', 1));
	}

	/**
	 * Authenticate
	 *
	 * @return  void
	 *
	 * @since 1.0
	 */
	abstract public function authenticate();

	/**
	 * Authenticate Request
	 *
	 * @return  mixed
	 *
	 * @since 1.0
	 */
	public static function authenticateRequest()
	{
		$params       = ComponentHelper::getParams('com_api');
		$app          = Factory::getApplication();

		$className    = 'APIAuthentication' . ucwords(self::getAuthMethod());

		$auth_handler = new $className($params);
		$user_id      = $auth_handler->authenticate();

		if ($user_id === false)
		{
			self::setAuthError($auth_handler->getError());

			return false;
		}
		else
		{
			$user = Factory::getUser($user_id);

			if (!$user->id)
			{
				self::setAuthError(Text::_("COM_API_USER_NOT_FOUND"));

				return false;
			}

			if ($user->block == 1)
			{
				self::setAuthError(Text::_("COM_API_BLOCKED_USER"));

				return false;
			}

			/* V1.8.1 - to set admin info headers
			$log_user = Factory::getUser(); */
			$isroot = $user->authorise('core.admin');

			if ($isroot)
			{
				header("x-api: " . self::getCom_apiVersion());
				header("x-plugins: " . implode(',', self::getPluginsList()));
				//JResponse::setHeader('x-api', self::getCom_apiVersion());
				//JResponse::setHeader('x-plugins', implode(',', self::getPluginsList()));
			}

			return $user;
		}
	}

	/**
	 * Set Auth Error
	 *
	 * @param   STRING  $msg  Message
	 *
	 * @return  boolean
	 *
	 * @since 1.0
	 */
	public static function setAuthError($msg)
	{
		self::$auth_errors[] = $msg;

		return true;
	}

	/**
	 * Get Auth Error
	 *
	 * @return  mixed
	 *
	 * @since 1.0
	 */
	public static function getAuthError()
	{
		if (empty(self::$auth_errors))
		{
			return false;
		}

		return array_pop(self::$auth_errors);
	}

	/**
	 * Get all api type plugin versions
	 *
	 * @return  mixed
	 *
	 * @since 1.8.1
	 */
	public static function getPluginsList()
	{
		$plugins    = PluginHelper::getPlugin('api');
		$pluginsArr = array();

		foreach ($plugins as $plg)
		{
			$xml          = simplexml_load_file(JPATH_SITE . '/plugins/api/' . $plg->name . '/' . $plg->name . '.xml');
			$version      = (string) $xml->version;
			$pluginsArr[] = $plg->name . '-' . $version;
		}

		return $pluginsArr;
	}

	/**
	 * Get com_api version
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	public static function getCom_apiVersion()
	{
		$xml = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_api/api.xml');

		return $version = (string) $xml->version;
	}

	/**
	 * Get Auth Method
	 *
	 * @return  string  Auth method
	 *
	 * @since 1.0
	 */
	private static function getAuthMethod()
	{
		$app = Factory::getApplication();
		$key = $app->input->get('key');

		if (isset($_SERVER['HTTP_X_AUTH']) && $_SERVER['HTTP_X_AUTH'])
		{
			$authMethod = $_SERVER['HTTP_X_AUTH'];
		}
		elseif ($key || self::getBearerToken())
		{
			$authMethod = 'key';
		}
		else
		{
			$authMethod = 'login';
		}

		return $authMethod;
	}

	/**
	 * Find if the user is trying to send a Bearer token
	 *
	 * @return  string  Token
	 */
	public static function getBearerToken()
	{
		$headers = self::getAuthorizationHeader();

		if ( !empty($headers) )
		{
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches))
			{
				return $matches[1];
			}
		}

		return null;
	}

	/**
	 * Get the authorization header
	 *
	 * @return  string  Header Value
	 */
	private static function getAuthorizationHeader()
	{
		$headers = null;

		if (isset($_SERVER['Authorization']))
		{
			$headers = trim($_SERVER["Authorization"]);
		}
		elseif (isset($_SERVER['HTTP_AUTHORIZATION']))
		{
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		}
		elseif (function_exists('apache_request_headers'))
		{
			$requestHeaders = apache_request_headers();

			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

			if (isset($requestHeaders['Authorization']))
			{
				$headers = trim($requestHeaders['Authorization']);
			}
		}

		if (isset($_SERVER['X-Authorization']))
		{
			$headers = trim($_SERVER["X-Authorization"]);
		}
		elseif (isset($_SERVER['HTTP_X_AUTHORIZATION']))
		{
			$headers = trim($_SERVER["HTTP_X_AUTHORIZATION"]);
		}
		elseif (function_exists('apache_request_headers'))
		{
			$requestHeaders = apache_request_headers();

			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

			if (isset($requestHeaders['X-Authorization']))
			{
				$headers = trim($requestHeaders['X-Authorization']);
			}
		}

		return $headers;
	}

	/**
	 * Find if the user is trying impersonate other user
	 *
	 * @return  int|string|NULL  User id or Email id or null
	 *
	 * @since   2.4.0
	 */
	public static function getImpersonateHeader()
	{
		$jinput           = Factory::getApplication()->input;
		$xImpersonate     = $jinput->server->get('X-Impersonate', '', 'STRING');
		$httpXImpersonate = $jinput->server->get('HTTP_X_IMPERSONATE', '', 'STRING');

		if (!empty($xImpersonate))
		{
			return $xImpersonate;
		}
		elseif (!empty($httpXImpersonate))
		{
			return $httpXImpersonate;
		}
	}

	/**
	 * Find if the user is trying impersonate other user
	 *
	 * @param   int  $tokenUserId  The userid for which token hash is validated
	 *
	 * @return  int|boolean  User id or Email id or null
	 *
	 * @since   2.4.0
	 */
	public static function getUserIdToImpersonate($tokenUserId)
	{
		// Lets find out if user trying to impersonate other user
		$userToImpersonate = self::getImpersonateHeader();

		// If other is to be impersonated
		if (!$userToImpersonate)
		{
			return false;
		}

		// Get user from tokenUserId
		$user         = Factory::getUser($tokenUserId);
		$isSuperAdmin = $user->authorise('core.admin');

		// If this user is not super admin user, return false
		if (!$isSuperAdmin)
		{
			ApiError::raiseError(403, "Not authorised to use Impersonation", 'APIUnauthorisedException');
		}

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
			ApiError::raiseError("400", Text::_('COM_API_INVALID_USER_TO_IMPERSONATE'), 'APIValidationException');

			return false;
		}

		// If username or emailid exists ?
		if ($searchFor)
		{
			$db = Factory::getDbo();
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
				ApiError::raiseError("400", Text::_('COM_API_INVALID_USER_TO_IMPERSONATE'), 'APIValidationException');

				return false;
			}
		}
		// If userid exists ?
		elseif ($userId)
		{
			$table = User::getTable();

			if ($table->load($userId))
			{
				return $userId;
			}
			else
			{
				ApiError::raiseError("400", Text::_('COM_API_INVALID_USER_TO_IMPERSONATE'), 'APIValidationException');

				return false;
			}
		}
	}
}

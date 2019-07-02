<?php
/**
 * @package     API
 * @subpackage  com_api
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.1.0
 * @build-date      2019/04/03
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Jfbconnec ApiAuthentication class
 *
 * @since  1.0
 */
class ApiAuthenticationJfbconnect extends ApiAuthentication
{
	protected $auth_method     = null;

	protected $domain_checking = null;

	/**
	 * Authenticate the user using the key in the header or request
	 *
	 * @return  int|boolean  User id of the user or false
	 */
	public function authenticate()
	{
		// Validate if JFB is installed
		$this->validateInstall();

		// Init vars
		$app          = JFactory::getApplication();
		$providerName = $app->input->json->get('provider', '', 'STRING');
		$accessToken  = $app->input->json->get('access_token', '', 'STRING');

		if (empty($providerName))
		{
			ApiError::raiseError(400, JText::_('COM_API_JFBCONNECT_MISSING_PROVIDER'));
		}

		if (empty($accessToken))
		{
			ApiError::raiseError(400, JText::_('COM_API_JFBCONNECT_MISSING_ACCESS_TOKEN'));
		}

		// Get provider object
		$provider = $this->jfbGetProvider($providerName);

		// Based on: JFB code from components/com_jfbconnect/controllers/authenticate.php callback()

		/*try
		{
			$provider->client->authenticate();
		}
		catch (Exception $e)
		{
			ApiError::raiseError(400, JText::_('api auth error'));
		}*/

		/*echo '<br/> provider class is: ' . get_class($provider);
		$methods = get_class_methods($provider);
		foreach($methods as $method) { echo $method; echo "<br>";}
		*/

		// Look for if JFB user mapping exists, get jUserId
		$jUserId = $this->jfbGetJoomlaUserId($provider, $accessToken);

		// If user not found, try registering new user
		if (!$jUserId)
		{
			$jUserId = $this->jfbRegisterUser($provider);
		}

		return $jUserId;
	}

	/**
	 * Validates if JFBConnect is installed and enabled
	 *
	 * @return  boolean
	 *
	 * @since  v2.0.1
	 */
	private function validateInstall()
	{
		jimport('joomla.filesystem.file');

		// Check if JFB is installed and enabled
		if (JFile::exists(JPATH_ROOT . '/components/com_jfbconnect/jfbconnect.php')
			&& JComponentHelper::isEnabled('com_jfbconnect', true))
		{
			return true;
		}

		ApiError::raiseError(500, JText::_('PLG_API_JFBCONNECT_NOT_INSTALLED'));

		return false;
	}

	/**
	 * Returns JFBConnect provider class object
	 *
	 * @param   string  $providerName  Provider name eg - google / facebook
	 *
	 * @return  object
	 *
	 * @since  2.0.1
	 */
	private function jfbGetProvider($providerName)
	{
		// Based on: JFB code from components/com_jfbconnect/controllers/authenticate.php getProvider()
		if ($providerName)
		{
			$provider = JFBCFactory::provider($providerName);

			if (empty($provider->name))
			{
				ApiError::raiseError(500, JText::_('Invalid provider'));
			}

			return $provider;
		}
	}

	/**
	 * Returns Joomla user id from jfb user mapping
	 *
	 * @param   object  $provider     JFBCOnnect provider class object
	 *
	 * @param   string  $accessToken  Provider access token
	 *
	 * @return  int
	 *
	 * @since  2.0.1
	 */
	public function jfbGetJoomlaUserId($provider, $accessToken)
	{
		if (strtolower($provider->name) == 'google')
		{
			// Based on: JFB code from components/com_jfbconnect/libraries/provider/google.php -> setupAuthentication()
			// Google client needs access token as array
			$accessToken = array('access_token' => $accessToken);
			$provider->client->setToken($accessToken);
		}
		elseif (strtolower($provider->name) == 'facebook')
		{
			// Based on: JFB code from administrator/assets/facebook-api/base_facebook.php -> setAccessToken()
			$provider->client->setAccessToken($accessToken);
		}

		// Based on: JFB code from components/com_jfbconnect/controllers/login.php login()
		$providerUserId = $provider->getProviderUserId();
		$userMapModel   = JFBCFactory::usermap();

		// Check if they have a Joomla user and log that user in. If not, create them one
		$jUserId = $userMapModel->getJoomlaUserId($providerUserId, strtolower($provider->name));

		return $jUserId;
	}

	/**
	 * Register new user using JFB
	 *
	 * @param   object  $provider  JFBCOnnect provider class object
	 *
	 * @return  int
	 *
	 * @since  2.0.1
	 */
	private function jfbRegisterUser($provider)
	{
		// Declare vars needed for JFB code to work
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
		$loginRegisterModel = JModelLegacy::getInstance('LoginRegister', 'JFBConnectModel');
		$userMapModel       = JFBCFactory::usermap();
		$providerUserId     = $provider->getProviderUserId();
		$jUserId            = 0;

		// START - Use JFB code
		// Based on: JFB code from components/com_jfbconnect/controllers/login.php login()
		$profile       = $provider->profile->fetchProfile($providerUserId, array('email'));
		$providerEmail = $profile->get('email', null);

		// Check if automatic email mapping is allowed, and see if that email is registered
		// AND the Facebook user doesn't already have a Joomla account
		if (!$provider->initialRegistration && JFBCFactory::config()->getSetting('facebook_auto_map_by_email'))
		{
			if ($providerEmail != null)
			{
				$jUserEmailId = $userMapModel->getJoomlaUserIdFromEmail($providerEmail);

				if (!empty($jUserEmailId))
				{
					// Found a user with the same email address
					// do final check to make sure there isn't a FB account already mapped to it
					$tempId = $userMapModel->getProviderUserId($jUserEmailId, strtolower($provider->name));

					if (!$tempId)
					{
						JFBConnectUtilities::clearJFBCNewMappingEnabled();

						if ($userMapModel->map($jUserEmailId, $providerUserId, strtolower($provider->name), $provider->client->getToken()))
						{
							JFBCFactory::log(JText::sprintf('COM_JFBCONNECT_MAP_USER_SUCCESS', $provider->name));

							// Update the temp jId so that we login below
							$jUserId = $jUserEmailId;
						}
						else
						{
							JFBCFactory::log(JText::sprintf('COM_JFBCONNECT_MAP_USER_FAIL', $provider->name));
						}
					}
				}
			}
		}

		/*
		 * check if user registration is turn off
		 * !allowUserRegistration and !social_registration => registration not allowed
		 * !allowUserRegistration and social_registration => registration allowed
		 * allowUserRegistration and !social_registration => registration not allowed
		 * JComponentHelper::getParams('com_users')->get('allowUserRegistration') check is not needed since
		 * we prioritized the JFBConnect social registration config
		*/

		if (JFBCFactory::config()->getSetting('social_registration') == 0 && !$jUserId)
		{
			JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_USER_REGISTRATION_DISABLED'), 'notice');

			// Commmented code below for com_api plugin

			// $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
			// return false;

			return 0;
		}

		// Check if no mapping, and Automatic Registration is set. If so, auto-create the new user.
		if (!$jUserId && JFBCFactory::config()->getSetting('automatic_registration'))
		{
			// User is not in system, should create their account automatically
			if ($loginRegisterModel->autoCreateUser($providerUserId, $provider))
			{
				$jUserId = $userMapModel->getJoomlaUserId($providerUserId, strtolower($provider->name));
			}
		}

		// END - use JFB code

		return $jUserId;
	}
}

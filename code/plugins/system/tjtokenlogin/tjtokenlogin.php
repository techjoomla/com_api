<?php
/**
 * @package     API
 * @subpackage  System.tjtokenlogin
 *
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$jwtBasePath = JPATH_SITE . '/components/com_api/vendors/php-jwt/src';
$jwtFilePath = $jwtBasePath . '/JWT.php';

if (!JFile::exists($jwtFilePath))
{
	return;
}

JLoader::import('JWT', $jwtBasePath);
JLoader::import('DomainException', $jwtBasePath);
JLoader::import('InvalidArgumentException', $jwtBasePath);
JLoader::import('UnexpectedValueException', $jwtBasePath);
JLoader::import('DateTime', $jwtBasePath);

use Firebase\JWT\JWT;
use Firebase\JWT\DomainException;
use Firebase\JWT\InvalidArgumentException;
use Firebase\JWT\UnexpectedValueException;
use Firebase\JWT\DateTime;

/**
 * Class for Tjtokenlogin System Plugin
 *
 * @since  1.0.0
 */
class PlgSystemTjtokenlogin extends JPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Valiate JWT token method to run onAfterInitialise
	 * Only purpose is to initialise the login authentication process if a cookie is present
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 * @throws  InvalidArgumentException
	 */
	public function onAfterInitialise()
	{
		// Get the application if not done by JPlugin. This may happen during upgrades from Joomla 2.5.
		if (!$this->app)
		{
			$this->app = JFactory::getApplication();
		}

		// No remember me for admin.
		if ($this->app->isClient('administrator'))
		{
			return;
		}

		// Get logintoken
		$input      = JFactory::getApplication()->input;
		$loginToken = $input->get->get('logintoken', '', 'STRING');

		// If loginToken is not set, return
		if (!$loginToken)
		{
			return false;
		}

		// Get id from payload
		$loginTokenArray = explode('.', $loginToken);

		if (!isset($loginTokenArray[1]))
		{
			return false;
		}

		// Note - The token payload is a JSON string encoded as Base64
		// And no keys are required to decode it.
		$payload = $loginTokenArray[1];
		$payload = base64_decode($payload);
		$payload = json_decode($payload);

		if (!isset($payload->id))
		{
			return false;
		}

		// Load api key table
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_api/tables');
		$table = JTable::getInstance('Key', 'ApiTable');
		$table->load(array('userid' => $payload->id));
		$key = $table->hash;

		// Generate claim for jwt
		// @TODO - set other claims
		$data = [
			"id" => trim($payload->id),
			/*"iat" => '',
			"exp" => '',
			"aud" => '',
			"sub" => ''*/
		];

		// We are using HS256 algo to generate JWT
		$jwt = JWT::encode($data, trim($key), 'HS256');

		if ($jwt !== $loginToken)
		{
			return false;
		}

		// @if (JFactory::getUser()->get('guest'))
		// {

		$this->app->login(array('id' => $payload->id, 'key' => $key), array('silent' => true));

		$redirect = $input->get->get('redirect', '', 'STRING');
		$redirect = base64_decode($redirect);
		$this->app->redirect(JRoute::_($redirect, false));

		// }
	}
}

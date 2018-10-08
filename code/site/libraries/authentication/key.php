<?php
/**
 * @package     API
 * @subpackage  com_api
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

JLoader::registerNamespace('Lcobucci\JWT', JPATH_SITE . "/components/com_api/libraries");
use Lcobucci\JWT\Parser;

/**
 * API resource class
 *
 * @since  1.0
 */
class ApiAuthenticationKey extends ApiAuthentication
{
	protected $auth_method = null;

	protected $domain_checking = null;

	/**
	 * Authenticate the user using the key in the header or request
	 *
	 * @return  string  User id of the user or false
	 */
	public function authenticate()
	{
		$app = JFactory::getApplication();
		$queryToken = $app->input->get('key', '', 'STRING');
		$headerToken = $this->getBearerToken();
		$key = $headerToken ? $headerToken : $queryToken;
		$parser = new Parser;

		try
		{
			$token = $parser->parse($key);

			// Verify the signature
			$algorithamSigner = $this->getAlgorithmSigner($token->getHeader('alg', ''));
			$signer = new $algorithamSigner;

			// Get the key
			$signerKey = $this->getSignerKey($token->getHeader('alg', ''));
			$verify = $token->verify($signer, $signerKey);

			if (!$verify)
			{
				$this->setError(JText::_('COM_API_IVALID_JWT_TOKEN'));

				return false;
			}

			$key = $token->getClaim("key");
		}
		catch (InvalidArgumentException $e)
		{
			// Not a valid JWT key keep this empty for next release
		}

		$token = $this->loadTokenByHash($key);

		if (isset($token->state) && $token->state == 1)
		{
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
	 * @return  object
	 */
	public function loadTokenByHash($hash)
	{
		$table = JTable::getInstance('Key', 'ApiTable');
		$table->loadByHash($hash);

		return $table;
	}

	/**
	 * Ruturn the path of the JWT class
	 *
	 * @param   STRING  $algorithm  The requested JWT algorithm
	 *
	 * @return  mixed
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getAlgorithmSigner($algorithm)
	{
		switch ($algorithm)
		{
			case 'HS256':
				return '\\Lcobucci\JWT\Signer\Hmac\Sha256';
			case 'HS384':
				return '\\Lcobucci\JWT\Signer\Hmac\Sha384';
			case 'HS512':
				return '\\Lcobucci\JWT\Signer\Hmac\Sha512';
			case 'RS256':
				return '\\Lcobucci\JWT\Signer\Rsa\Sha256';
			default:
				return null;
		}
	}

	/**
	 * Ruturn the secret required to verify the jwt token depending on the requested algorithm
	 *
	 * @param   STRING  $algorithm  The requested JWT algorithm
	 *
	 * @return  mixed
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getSignerKey($algorithm)
	{
		switch ($algorithm)
		{
			case 'HS256':
			case 'HS384':
			case 'HS512':
				// @TODO Implement the key retrieval logic here
				return "2dc70be055c7b5d97fce02204c72d755";
			case 'RS256':
				// @TODO  Implement the key retrieval logic here
				return file_get_contents(JPATH_SITE . "/jwtRS256.key.pub");
			default:
				return null;
		}
	}
}

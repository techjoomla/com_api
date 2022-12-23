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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

/**
 * API resource class
 *
 * @since  1.0
 */
class ApiAuthenticationKey extends ApiAuthentication
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
		$header_token = $this->getBearerToken();
		$token        = $this->loadTokenByHash($header_token);

		if (isset($token->state) && $token->state == 1)
		{
			$userId = parent::getUserIdToImpersonate($token->userid);

			return $userId ? $userId : $token->userid;
		}

		$this->setError(Text::_('COM_API_KEY_NOT_FOUND'));

		return false;
	}

	/**
	 * Load a token row using hash
	 *
	 * @param   STRING  $hash  The token hash
	 *
	 * @return  OBJECT|boolean
	 */
	public function loadTokenByHash($hash)
	{
		$table = Table::getInstance('Key', 'ApiTable');
		$table->loadByHash($hash);

		return $table;
	}
}

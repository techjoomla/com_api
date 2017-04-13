<?php
/**
 * @package     Joomla.Site
 * @subpackage  Com_api
 *
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

/**
 * API user authentication
 *
 * @since  1.0
 */
class ApiAuthenticationSession extends ApiAuthentication
{
	protected $auth_method = null;

	protected $domain_checking = null;

	/**
	 * Authenticate user
	 *
	 * @return mixed
	 *
	 * @since 1.0
	 */
	public function authenticate()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if (! $user->id)
		{
			$this->setError(JText::_('COM_API_LOGIN_MSG'));

			return false;
		}

		return $user->id;
	}
}

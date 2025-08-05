<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  API.Users
 *
 * @copyright   Copyright (C) 2024 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * API Plugin for Users
 *
 * @since  1.0.0
 */
class plgAPIUsers extends ApiPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings
	 *
	 * @since   1.0.0
	 */
	public function __construct(&$subject, $config = [])
	{
		parent::__construct($subject, $config);

		ApiResource::addIncludePath(dirname(__FILE__) . '/users');
		
		// Load language file for plugin frontend
		try {
			$lang = Factory::getLanguage(); 
			$lang->load('plg_api_users', JPATH_ADMINISTRATOR, '', true);
		} catch (Exception $e) {
			// Language loading failed, continue without error
		}
		
		// Set the login resource to be public
		$this->setResourceAccess('login', 'public', 'get');
		$this->setResourceAccess('users', 'public', 'post');
		$this->setResourceAccess('config', 'public', 'get');
	}
}

<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  API.Login
 *
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://techjoomla.com
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Login API Plugin
 *
 * @since  1.0
 */
class plgAPILogin extends ApiPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   1.0
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config = array());

		ApiResource::addIncludePath(dirname(__FILE__) . '/login');

		// Set resource access
		$this->setResourceAccess('login', 'public', 'post');
		$this->setResourceAccess('login', 'public', 'get');
	}
}

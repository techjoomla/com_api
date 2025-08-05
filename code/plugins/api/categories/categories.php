<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  API.Categories
 *
 * @copyright   Copyright (C) 2024 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Category API plugin class
 *
 * @since  1.0.0
 */
class PlgAPICategories extends ApiPlugin
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

		ApiResource::addIncludePath(dirname(__FILE__) . '/categories');

		$this->setResourceAccess('categories', 'public', 'get');
		$this->setResourceAccess('category', 'public', 'get');
	}
}

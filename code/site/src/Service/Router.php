<?php
/**
 * @package    Com_Api
 * @subpackage Router
 * @copyright  Copyright (C) 2025 Machado Meyer. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Component\Router\RouterInterface;

/**
 * Router factory for com_api component
 *
 * @since  5.0.0
 */
class ApiRouterFactory implements RouterFactoryInterface
{
	/**
	 * Create a router for com_api
	 *
	 * @param   \Joomla\CMS\Application\CMSApplicationInterface  $application  The application object
	 * @param   \Joomla\CMS\Menu\AbstractMenu                    $menu         The menu object to work with
	 *
	 * @return  RouterInterface
	 *
	 * @since   5.0.0
	 */
	public function createRouter($application, $menu): RouterInterface
	{
		require_once JPATH_COMPONENT . '/router.php';
		return new APIRouter($application, $menu);
	}
}

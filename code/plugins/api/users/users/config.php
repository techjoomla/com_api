<?php
/**
 * @package     Com_Api
 * @subpackage  Com_api-plugins
 *
 * @copyright   Copyright (C) 2009-2017 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

require_once JPATH_ROOT . '/components/com_api/libraries/authentication/user.php';
require_once JPATH_ROOT . '/components/com_api/libraries/authentication/login.php';

/**
 * API class UsersApiResourceConfig
 *
 * @since  1.0
 */
class UsersApiResourceConfig extends ApiResource
{
	/**
	 * Method get
	 *
	 * @return  mixed
	 *
	 * @since 1.0
	 */
	public function get()
	{
		$obj = new stdClass;

		// Get joomla,easyblog and easysocial configuration
		// Get version of easysocial and easyblog
		$easyblog   = JPATH_ADMINISTRATOR . '/components/com_easyblog/easyblog.php';
		$easysocial = JPATH_ADMINISTRATOR . '/components/com_easysocial/easysocial.php';

		// Eb version
		if (File::exists($easyblog))
		{
			$obj->easyblog = $this->getCompParams('com_easyblog', 'easyblog');
		}

		// Es version
		if (File::exists($easysocial))
		{
			/*$xml = Factory::getXML(JPATH_ADMINISTRATOR .'/components/com_easysocial/easyblog.xml');
			$obj->easysocial_version = (string)$xml->version;*/
			$obj->easysocial = $this->getCompParams('com_easysocial', 'easysocial');
		}

		$obj->global_config = $this->getJoomlaConfig();
		$obj->plugin_config = $this->getpluginConfig();

		$this->plugin->setResponse($obj);
	}

	/**
	 * Method to get groups list
	 *
	 * @return  ApiPlugin response object
	 *
	 * @since 2.0
	 */
	public function post()
	{
		$this->plugin->setResponse(Text::_('PLG_API_USERS_UNSUPPORTED_METHOD_POST'));
	}

	/**
	 * Method to update Easyblog auth keys
	 *
	 * @param   number  $cname  The  table
	 * @param   number  $name   The table
	 *
	 * @return  ApiPlugin response object
	 *
	 * @since 2.0
	 */
	public function getCompParams($cname=null,$name=null)
	{
		jimport('joomla.application.component.helper');

		$app   = Factory::getApplication();
		$cdata = array();

		$xmlFile = JPATH_ADMINISTRATOR . '/components/' . $cname . '/' . $name . '.xml';
		if (file_exists($xmlFile)) {
			$xml = simplexml_load_file($xmlFile);
			$cdata['version'] = (string) $xml->version;
		} else {
			$cdata['version'] = 'Unknown';
		}
		$jconfig          = Factory::getConfig();

		if ($cname == 'com_easyblog')
		{
			/*$xml = Factory::getXML(JPATH_ADMINISTRATOR .'/components/com_easyblog/easyblog.xml');
			$version = (string)$xml->version;*/

			if ($cdata['version'] < 5)
			{
				require_once JPATH_ROOT . '/components/com_easyblog/helpers/helper.php';
				$eb_params = EasyBlogHelper::getConfig();
			}
			else
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
				$eb_params = EB::config();
			}

			$cdata['main_max_relatedpost']                  = $eb_params->get('main_max_relatedpost');
			$cdata['layout_pagination_bloggers']            = $eb_params->get('layout_pagination_bloggers');
			$cdata['layout_pagination_categories']          = $eb_params->get('layout_pagination_categories');
			$cdata['layout_pagination_categories_per_page'] = $eb_params->get('layout_pagination_categories_per_page');
			$cdata['layout_pagination_bloggers_per_page']   = $eb_params->get('layout_pagination_bloggers_per_page');
			$cdata['layout_pagination_archive']             = $eb_params->get('layout_pagination_archive');
			$cdata['layout_pagination_teamblogs']           = $eb_params->get('layout_pagination_teamblogs');
		}
		else
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';
			$es_params = FD::config();
			$profiles = FD::model('profiles');

			// $cdata['conversations_limit']  = $es_params->get('conversations')->limit;
			$cdata['activity_limit']          = $es_params->get('activity')->pagination;
			$cdata['lists_limit']             = $es_params->get('lists')->display->limit;
			$cdata['comments_limit']          = $es_params->get('comments')->limit;
			$cdata['stream_pagination_limit'] = $es_params->get('stream')->pagination->pagelimit;
			$cdata['photos_pagination_limit'] = $es_params->get('photos')->pagination->photo;
			$cdata['album_pagination_limit']  = $es_params->get('photos')->pagination->album;
			$cdata['emailasusername']         = $es_params->get('registrations')->emailasusername;
			$cdata['displayName']             = $es_params->get('users')->displayName;
			$cdata['groups']['enabled']       = $es_params->get('groups')->enabled;

			$profiles_data = $profiles->getAllProfiles();

			/* Check for profile_type is allowed for Registration by vivek*/
			$allowed_profile_types = array();

			foreach ($profiles_data as $key )
			{
				if ($key->registration == '1')
				{
					array_push($allowed_profile_types, $key);
				}
			}

			$cdata['profile_types'] = $allowed_profile_types;
		}

		return $cdata;
	}

	/**
	 * Method to Get fb plugin config
	 *
	 * @return  ApiPlugin response object
	 *
	 * @since 2.0
	 */
	public function getpluginConfig()
	{
		$data         = array();
		$plugin       = PluginHelper::getPlugin('api', 'users');
		$pluginParams = new Registry($plugin->params);

		// Code for future use

		/* $plugin_es = JPluginHelper::getPlugin('api', 'easysocial');
		$pluginParams_es = new JRegistry($plugin_es->params); */

		$data['fb_login']  = $pluginParams->get('fb_login');
		$data['fb_app_id'] = $pluginParams->get('fb_app_id');
		$data['quick2art'] = $pluginParams->get('quick2art');

		return $data;
	}

	/**
	 * Method to Get joomla config changes
	 *
	 * @return  ApiPlugin response object
	 *
	 * @since 2.0
	 */
	public function getJoomlaConfig()
	{
		$jconfig                     = Factory::getConfig();
		$jarray                      = array();
		$jarray['global_list_limit'] = $jconfig->get('list_limit');
		$jarray['offset']            = $jconfig->get('offset');
		$jarray['offset_user']       = $jconfig->get('offset_user');

		return $jarray;
	}

	/**
	 * Method to update Easyblog auth keys
	 *
	 * @param   string  $user  The  table
	 * @param   string  $key   The table
	 *
	 * @return  ApiPlugin response object
	 *
	 * @since 2.0
	 */
	public function updateEauth($user=null,$key=null)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';
		$model       = FD::model('Users');
		$id          = $model->getUserId('username', $user->username);
		$user        = FD::user($id);
		$user->alias = $user->username;
		$user->auth  = $key;
		$user->store();

		return $id;
	}
}

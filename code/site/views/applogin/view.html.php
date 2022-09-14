<?php
/**
 * @package    API
 * @copyright  Copyright (C) 2009-2020 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;

require_once JPATH_SITE . '/components/com_api/vendors/php-jwt/src/JWT.php';

use Firebase\JWT\JWT;

/**
 * HTML Article View class for the Content component
 *
 * @since  1.5
 */
class ApiViewApplogin extends HtmlView
{
	public $keyObj = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     \HtmlView::loadTemplate()
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		if (!$user->id)
		{
			$msg = Text::_('COM_API_LOGIN_MSG');
			$uri = $_SERVER['REQUEST_URI'];
			$url = base64_encode($uri);
			$app->redirect(Route::_('index.php?option=com_users&view=login&return=' . $url, false), $msg);
		}

		$this->keyObj = $this->getKey();

		parent::display($tpl);
	}

	/**
	 * Get key for logged in user
	 *
	 * @return  object
	 *
	 * @since   2.5.2
	 */
	protected function getKey()
	{
		$obj   = new stdclass;
		$user = Factory::getUser();
		$id   = $user->id;

		require_once JPATH_ADMINISTRATOR . '/components/com_api/models/key.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_api/models/keys.php';

		$kmodel = new ApiModelKey;
		$model  = new ApiModelKeys;
		$key    = null;

		// Get login user hash
		// $kmodel->setState('user_id', $user->id);

		// $kmodel->setState('user_id', $id);
		// $log_hash = $kmodel->getList();
		$model->setState('user_id', $id);
		$log_hash = $model->getItems();

		$log_hash = (!empty($log_hash)) ? $log_hash[count($log_hash) - count($log_hash)] : $log_hash;

		if (!empty($log_hash))
		{
			$key = $log_hash->hash;
		}
		elseif ($key == null || empty($key))
		{
			// Create new key for user
			$data = array (
				'userid' => $user->id,
				'domain' => '' ,
				'state'  => 1,
				'id'     => '',
				'task'   => 'save',
				'c'      => 'key',
				'ret'    => 'index.php?option=com_api&view=keys',
				'option' => 'com_api',
				Session::getFormToken() => 1
			);

			$result = $kmodel->save($data);

			// $key  = $result->hash;

			if (!$result)
			{
				return false;
			}

			// Load api key table
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_api/tables');
			$table = Table::getInstance('Key', 'ApiTable');
			$table->load(array('userid' => $user->id));
			$key = $table->hash;
		}

		if (!empty($key))
		{
			$obj->auth = $key;
			$obj->code = '200';

			// $obj->id = $user->id;

			$obj->id = $id;

			// Generate claim for jwt
			$data = [
				"id" => trim($id),
				/*"iat" => '',
				"exp" => '',
				"aud" => '',
				"sub" => ''"*/
			];

			// Using HS256 algo to generate JWT
			$jwt = JWT::encode($data, trim($key), 'HS256');

			if (isset($jwt) && $jwt != '')
			{
				$obj->jwt = $jwt;
			}
			else
			{
				$obj->jwt = false;
			}
		}
		else
		{
			// Load language file for plugin
			$lang = Factory::getLanguage();
			$lang->load('plg_api_users', JPATH_ADMINISTRATOR,'',true);

			$obj->code = 403;
			$obj->message = Text::_('PLG_API_USERS_BAD_REQUEST_MESSAGE');
		}

		return $obj;
	}
}

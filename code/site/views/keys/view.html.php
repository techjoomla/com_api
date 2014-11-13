<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class ApiViewKeys extends ApiView {

	public $can_register = null;

	public function __construct() {
		parent::__construct();

		$user = JFactory::getUser();

		if (!$user->get('id'))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('COM_API_NOT_AUTH_MSG'));
			exit();
		}

		$params = JComponentHelper::getParams('com_api');

		$this->set('can_register', $params->get('key_registration', false) && $user->get('gid') >= $params->get('key_registration_access', 18));

	}

	public function display($tpl = null) {

		JHTML::stylesheet('com_api.css', 'components/com_api/assets/css/');

		if ($this->routeLayout($tpl)) :
			return;
		endif;

		$user	= JFactory::getUser();

		$model	= JModelLegacy::getInstance('Key', 'ApiModel');
		$model->setState('user_id', $user->get('id'));
		$tokens	= $model->getList();

		$new_token_link = JRoute::_('index.php?option=com_api&view=keys&layout=new');

		$this->session_token = JHtml::_('form.token');
		$this->new_token_link = $new_token_link;
		$this->user = $user;
		$this->tokens = $tokens;

		parent::display($tpl);
	}

	protected function displayNew($tpl=null) {
		$this->setLayout('edit');
		$this->displayEdit($tpl);
	}

	protected function displayEdit($tpl=null) {

		$app	= JFactory::getApplication();

		JHTML::script('joomla.javascript.js', 'includes/js/');

		$this->assignRef('return', $_SERVER['HTTP_REFERER']);

		$key	= JTable::getInstance('Key', 'ApiTable');
		if ($id = $app->input->get('id', 0 ,'INT')) :
			$key->load($id);
			if ($key->user_id != JFactory::getUser()->get('id')) :
				JFactory::getApplication()->redirect($_SERVER['HTTP_REFERER'], JText::_('COM_API_UNAUTHORIZED_EDIT_KEY'));
				return false;
			endif;
		elseif (!$this->can_register) :
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_api&view=keys'), JText::_('COM_API_UNAUTHORIZED_REGISTER'));
			return false;
		endif;

		$this->assignRef('key', $key);

		parent::display($tpl);
	}

}

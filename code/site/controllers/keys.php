<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerKeys extends ApiController {


	public function display($cachable = false, $urlparams = array()) {
		parent::display();
	}

	private function checkAccess() {
		$user	= JFactory::getUser();

		if ($user->get('gid') == 25) :
			return true;
		endif;

		$params	= JComponentHelper::getParams('com_api');

		if (!$params->get('key_registration')) :
			return false;
		endif;

		$access_level = $params->get('key_registration_access');

		if ($user->get('gid') < $access_level) :
			return false;
		endif;

		return true;
	}

	public function cancel() {

		//JRequest::checkToken() or jexit(JText::_("COM_API_INVALID_TOKEN"));
		 JSession::checkToken() or jexit(JText::_("COM_API_INVALID_TOKEN"));

		$this->setRedirect(JRoute::_('index.php?option=com_api&view=keys', FALSE));
	}

	public function save() {

		JSession::checkToken('default') or jexit(JText::_("COM_API_INVALID_TOKEN"));

		//vishal - for j3.2
		$app = JFactory::getApplication();
		$id 	= $app->input->post->get('id',0,'INT');

		if (!$id && !$this->checkAccess()) :
			JFactory::getApplication()->redirect('index.php', JText::_('COM_API_NOT_AUTH_MSG'));
			exit();
		endif;

		//$domain	= JRequest::getVar('domain', '', 'post', 'string');
		$domain	= $app->input->post->get('domain','','STRING');

		$data	= array(
			'id'		=> $id,
			'domain'	=> $domain,
			'user_id'	=> JFactory::getUser()->get('id'),
			'enabled'	=> 1
		);

		$model	= JModel::getInstance('Key', 'ApiModel');

		if ($model->save($data) === false) :
			$this->setRedirect($_SERVER['HTTP_REFERER'], $model->getError(), 'error');
			return false;
		endif;

		$this->setRedirect(JRoute::_('index.php?option=com_api&view=keys'), JText::_('COM_API_KEY_SAVED'));

	}

	public function delete() {

		//vishal - for j3.2
    	$app = JFactory::getApplication();

		//$key = $app->input->get('key');
		//JRequest::checkToken('request') or jexit(JText::_("COM_API_INVALID_TOKEN"));
		JSession::checkToken('default') or jexit(JText::_("COM_API_INVALID_TOKEN"));

		if (!$this->checkAccess()) :
			JFactory::getApplication()->redirect('index.php', JText::_('COM_API_NOT_AUTH_MSG'));
			exit();
		endif;

		$user_id	= JFactory::getUser()->get('id');
		//$id 		= JRequest::getInt('id', 0);
		$id 		= $app->input->get('id','','INT');

		$table 	= JTable::getInstance('Key', 'ApiTable');
		$table->load($id);

		if ($user_id != $table->user_id) :
			$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_("COM_API_UNAUTHORIZED_DELETE_KEY"), 'error');
			return false;
		endif;

		$table->delete($id);

		$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_("COM_API_SUCCESSFUL_DELETE_KEY"));

	}

}

<?php 
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
	
		$model	= JModel::getInstance('Key', 'ApiModel');
		$model->setState('user_id', $user->get('id'));
		$tokens	= $model->getList();
		
		$new_token_link = JRoute::_('index.php?option=com_api&view=keys&layout=new');
		
		$this->assignRef('session_token', JUtility::getToken());
		$this->assignRef('new_token_link', $new_token_link);
		$this->assignRef('user', $user);
		$this->assignRef('tokens', $tokens);
		
		parent::display($tpl);
	}	
	
	protected function displayNew($tpl=null) {
		$this->setLayout('edit');
		$this->displayEdit($tpl);
	}
	
	protected function displayEdit($tpl=null) {
		
		JHTML::script('joomla.javascript.js', 'includes/js/');
		
		$this->assignRef('return', $_SERVER['HTTP_REFERER']);
		
		$key	= JTable::getInstance('Key', 'ApiTable');
		if ($id = JRequest::getInt('id', 0)) :
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
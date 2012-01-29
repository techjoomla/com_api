<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerAdmin extends ApiController {
	
	public function __construct($config=array()) {
		parent::__construct($config);
		$this->registerTask('apply', 'save');
		$this->registerTask('add', 'edit');
		
		$default_url = 'index.php?option='.$this->get('option');
		$view = JRequest::getVar('view','');
		if ($view)
			$default_url .= '&view='.$view;
			
		$this->set('default_url', $default_url);
		
	}
	
	public function display() {
		$app	= JFactory::getApplication();
		$view 	= JRequest::getVar('view', '');
		if (!$view) :
			JRequest::setVar('view', 'cpanel');
		endif;
		
		parent::display();
	
	}
	
	public function edit() {
		$app 	= JFactory::getApplication();
		$view	= $this->getEntityName();
		$layout = 'default';
		JRequest::setVar('view', $view);
		JRequest::setVar('layout', $layout);
		parent::display();
	}
	
	public function cancel() {
		JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		$this->setRedirect(JRequest::getVar('ret', $this->get('default_url')), $msg);
	}
	
	public function remove($hash='post') {
		JRequest::checkToken($hash) or jexit(JText::_('INVALID_TOKEN'));
		$name	= $this->getEntityName();
		$post 	= JRequest::get('post');
		$model 	= $this->getModel($name);
		
		$cid	= JRequest::getVar('cid', array(), $hash, 'array');
		if (empty($cid)) :
			$cid = JRequest::getVar('id', 0, $hash, 'int');
		endif;
		
		if ($cid) :
			if (!$model->delete($cid)) :
				$msg	= $model->getError();
				$type	= 'error';
			else :
				$msg	= JText::_('COM_API_DELETE_SUCCESS');
				$type	= 'message';
			endif;
		else :
			$msg	= JText::_('COM_API_NO_SELECTION');
			$type	= 'error';
		endif;
		
		$url = isset($post['ret']) ? $post['ret'] : JRequest::getVar('HTTP_REFERER', $this->get('default_url'), 'server');
		$this->setRedirect($url, $msg, $type);
	}
	
	public function save() {
		JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		$name	= $this->getEntityName();
		$post 	= JRequest::get('post');
		$model 	= $this->getModel($name);
		
		if (!$item = $model->save($post)) :
			$msg = $model->getError();
			$url = JRequest::getVar('HTTP_REFERER', $this->get('default_url'), 'server');
			$this->setRedirect($url, $msg, 'error');
			return;
		endif;
		
		$name = strtolower($name);
		$msg = JText::_("COM_API_SAVE_SUCCESSFUL");
		if($this->getTask() == 'apply') :
			$url = "index.php?option=".$this->get('option')."&view=".$name."&cid[]=".$item->id;
		elseif (isset($post['ret'])) :
			$url = $post['ret'];
		else :
			$url = JRequest::getVar('HTTP_REFERER', $this->get('default_url'), 'server');
		endif;
		$this->setRedirect($url, $msg);
	}
	
	public function publish() {
		JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		$this->changeState(1);
		
		if ($error = $this->getError()) :
			$msg = $error;
			$type = 'error';
		else :
			$msg = JText::_("COM_API_PUBLISH_SUCCESS");
			$type = 'message';
		endif;
		
		$this->setRedirect(JRequest::getVar('HTTP_REFERER', $this->get('default_url'), 'server'), $msg, $type);
	}
	
	public function unpublish() {
		JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		$this->changeState(0);
		if ($error = $this->getError()) :
			$msg = $error;
			$type = 'error';
		else :
			$msg = JText::_("COM_API_UNPUBLISH_SUCCESS");
			$type = 'message';
		endif;
		
		$this->setRedirect(JRequest::getVar('HTTP_REFERER', $this->get('default_url'), 'server'), $msg, $type);
	}
	
	protected function changeState($state, $cids=array(), $table_class=null) {
		if (empty($cids)) :
			$cids = JRequest::getVar('cid', array(), 'post', 'array');
		endif;
		
		$table_class = $table_class ? $table_class : $this->getEntityName();
		
		$table 	= JTable::getInstance($table_class, 'ApiTable');
		
		if (!$table->publish($cids, $state)) :
			$this->setError($table->getError());
			return false;
		endif;
		
		return true;
	}
	
	protected function getEntityName() {
		preg_match( '/(.*)Controller(.*)/i', get_class( $this ), $r );
		if (!isset($r[2])) :
			return $r[1];
		else :
			return $r[2];
		endif;
	}
	
}
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;


class ApiControllerAdmin extends ApiController {

	public function __construct($config=array()) {
		parent::__construct($config);
		$this->registerTask('apply', 'save');
		$this->registerTask('add', 'edit');

		$app = Factory::getApplication();

		$default_url = 'index.php?option='.$this->get('option');

		//$view = Factory::getApplication()->input->get('view','');
		$view = $app->input->get('view','','STRING');

		$task = $app->input->post->get('task','','STRING');

		if($task == 'add' || $task == 'edit')
			$view = 'key';

		if ($view)
			$default_url .= '&view='.$view;

		$this->set('default_url', $default_url);

	}

	public function display() {
		$app	= Factory::getApplication();

		//$view 	= Factory::getApplication()->input->get('view', '');

		$view = $app->input->get('view','','STRING');

		if (!$view) :
			//Factory::getApplication()->input->set('view', 'cpanel');
			$app->input->set('view','cpanel');

		endif;

		parent::display();

	}

	public function edit() {
		$app 	= Factory::getApplication();
		$view	= $this->getEntityName();

		$layout = 'default';

		//$app->input->post->set('view',$view);
		//$app->input->post->set('layout',$layout);

		$app->input->set('view',$view);
		$app->input->set('layout',$layout);

		parent::display();
	}

	public function cancel() {

		//JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		Session::checkToken() or jexit(Text::_('INVALID_TOKEN'));
		$app 	= Factory::getApplication();
		//$this->setRedirect(Factory::getApplication()->input->get('ret', $this->get('default_url')), $msg);
		$this->setRedirect($app->input->get('ret', $this->get('default_url'),'STRING'), $msg);
	}

	public function remove($hash='post') {

		Session::checkToken($hash) or jexit(Text::_('INVALID_TOKEN'));
		$app 	= Factory::getApplication();

		$name	= $this->getEntityName();
		//$post 	= Factory::getApplication()->input->get('post');
		$post 	= $app->input->post;
		$model 	= $this->getModel($name);

		//$cid	= Factory::getApplication()->input->get('cid', array(), $hash, 'array');
		$cid	= $app->input->post->get('cid',array(), 'ARRAY');

		if (empty($cid)) :
			//$cid = Factory::getApplication()->input->get('id', 0, $hash, 'int');
			$cid = $app->input->post->get('id', 0,'INT');
		endif;

		if ($cid) :
			if (!$model->delete($cid)) :
				$msg	= $model->getError();
				$type	= 'error';
			else :
				$msg	= Text::_('COM_API_DELETE_SUCCESS');
				$type	= 'message';
			endif;
		else :
			$msg	= Text::_('COM_API_NO_SELECTION');
			$type	= 'error';
		endif;

		$url = isset($post['ret']) ? $post['ret'] :$app->input->server->get('HTTP_REFERER', $this->get('default_url'), 'STRING');
		$this->setRedirect($url, $msg, $type);
	}

	public function save() {
		Session::checkToken()  or jexit(Text::_('INVALID_TOKEN'));

		$app = Factory::getApplication();

		$name	= $this->getEntityName();

		//vishal- add for create new key

		$post['user_id'] 	=  $app->input->post->get('user_id',0,'INT');
		$post['domain'] 	=  $app->input->post->get('domain','','STRING');
		$post['published'] 	=  $app->input->post->get('published',0,'INT');
		$post['id'] 	=  $app->input->post->get('id',0,'INT');
		$post['task'] 	=  $app->input->post->get('task','','STRING');
		$post['c'] 	=  $app->input->post->get('c','','STRING');
		$post['ret'] 	=  $app->input->post->get('ret','','STRING');
		$post['option'] 	=  $app->input->post->get('option');
		//$post[JSession::getToken()] 	=  $app->input->post->get(JSession::getToken(),'','INT');

		//end

		$model 	= $this->getModel($name);

		if (!$item = $model->save($post)) :
			$msg = $model->getError();
			//$url = Factory::getApplication()->input->get('HTTP_REFERER', $this->get('default_url'), 'server');
			$url = $app->input->server->get('HTTP_REFERER', $this->get('default_url'), 'STRING');
			$this->setRedirect($url, $msg, 'error');
			return;
		endif;

		$name = strtolower($name);
		$msg = Text::_("COM_API_SAVE_SUCCESSFUL");
		if($this->getTask() == 'apply') :
			$url = "index.php?option=".$this->get('option')."&view=".$name."&cid[]=".$item->id;
		elseif (isset($post['ret'])) :
			$url = $post['ret'];
		else :
			$url = $app->input->server->get('HTTP_REFERER', $this->get('default_url'), 'STRING');
		endif;
		$this->setRedirect($url, $msg);
	}

	public function publish() {

		//JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		Session::checkToken() or jexit(Text::_('INVALID_TOKEN'));

		$app = Factory::getApplication();

		$this->changeState(1);

		if ($error = $this->getError()) :
			$msg = $error;
			$type = 'error';
		else :
			$msg = Text::_("COM_API_PUBLISH_SUCCESS");
			$type = 'message';
		endif;

		$this->setRedirect($app->input->server->get('HTTP_REFERER', $this->get('default_url'), 'STRING'), $msg, $type);
	}

	public function unpublish() {

		//JRequest::checkToken() or jexit(JText::_('INVALID_TOKEN'));
		Session::checkToken()  or jexit(Text::_('INVALID_TOKEN'));

		$app = Factory::getApplication();

		$this->changeState(0);
		if ($error = $this->getError()) :
			$msg = $error;
			$type = 'error';
		else :
			$msg = Text::_("COM_API_UNPUBLISH_SUCCESS");
			$type = 'message';
		endif;

		$this->setRedirect($app->input->server->get('HTTP_REFERER', $this->get('default_url'), 'STRING'), $msg, $type);
	}

	protected function changeState($state, $cids=array(), $table_class=null) {

		$app = Factory::getApplication();

		if (empty($cids)) :
			$cids = $app->input->post->get('cid', array(),'ARRAY');
		endif;

		$table_class = $table_class ? $table_class : $this->getEntityName();

		$table 	= Table::getInstance($table_class, 'ApiTable');

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

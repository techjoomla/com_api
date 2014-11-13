<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ApiView extends JViewLegacy {

	protected $option	= null;
	protected $view		= null;

	function __construct() {

		//vishal - for j3.2 changes
		$app	= JFactory::getApplication();

		$option = $app->input->get('option','','STRING');

		$this->set('option', $option);
		parent::__construct();
	}

	public function display($tpl = null) {
		$app	= JFactory::getApplication();
		if ($app->isAdmin()) :
			$this->generateSubmenu();
		endif;

		parent::display($tpl);
	}

	private function generateSubmenu() {
		$views = $this->getMainViews();

		foreach($views as $item) :
			$link = 'index.php?option='.$this->get('option').'&view='.$item['view'];
			JSubMenuHelper::addEntry($item['name'], $link, ($this->get('view') == $item['view']));
		endforeach;

	}

	protected function getMainViews() {
		$views = array(
					array('name' => JText::_('COM_API_CONTROL_PANEL'), 'view' => 'cpanel'),
					array('name' => JText::_('COM_API_KEYS'), 'view' => 'keys'),
				);
		return $views;
	}

	protected function routeLayout($tpl) {
		$layout = ucwords(strtolower($this->getLayout()));

		if ($layout == 'Default') :
			return false;
		endif;

		$method_name = 'display'.$layout;
		if (method_exists($this, $method_name) && is_callable(array($this, $method_name))) :
			$this->$method_name($tpl);
			return true;
		else :
			$this->setLayout('default');
			return false;
		endif;

	}
}

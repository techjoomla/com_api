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

class ApiViewCpanel extends ApiView {

	public function display($tpl = null) {

		if ($this->routeLayout($tpl)) :
			return;
		endif;

		$this->generateToolbar();

		$views 		= $this->getMainViews();

		$this->assignRef('views', $views);
		$this->assignRef('modified', $modified);

		parent::display($tpl);
	}

	private function generateToolbar() {
		JToolBarHelper::title(JText::_('COM_API').': '.JText::_('COM_API_CONTROL_PANEL'));
		JToolBarHelper::preferences('com_api', 500, 500);
	}

}

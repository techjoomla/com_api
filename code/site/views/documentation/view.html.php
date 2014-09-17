<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Ashwin Date
 * @link 	http://www.techjoomla.com
 * @copyright Copyright (C) 2015 Techjoomla. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class ApiViewDocumentation extends ApiView {

	public $can_register = null;

	public function display($tpl = null) {

		JHTML::stylesheet('com_api.css', 'components/com_api/assets/css/');

		$user	= JFactory::getUser();

		$dmodel	= JModelLegacy::getInstance('Documentation', 'ApiModel');
		$endpoints	= $dmodel->getList();

		$kmodel	= JModelLegacy::getInstance('Key', 'ApiModel');
		$tokens	= $kmodel->getList();

		$this->endpoints = $endpoints;
		$this->user = $user;
		$this->tokens = $tokens;

		parent::display($tpl);
	}

}

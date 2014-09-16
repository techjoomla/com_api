<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiAuthenticationKey extends ApiAuthentication {

	protected	$auth_method		= null;
	protected	$domain_checking	= null;

	public function authenticate() {

		$app = JFactory::getApplication();
		$key = $app->input->get('key','','STRING');
		$token = $this->loadTokenByHash($key);

		if (!$token) :
			$this->setError(JText::_('COM_API_KEY_NOT_FOUND'));
			return false;
		endif;

		if (!$token->state) :
			$this->setError(JText::_('COM_API_KEY_DISABLED'));
			return false;
		endif;

		return $token->userid;
	}

	public function loadTokenByHash($hash) {
		
		$table = JTable::getInstance('Key', 'ApiTable');
		$table->loadByHash($hash);

		return $table;
	}

}

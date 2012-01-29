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

jimport('joomla.application.component.view');

class ApiView extends JView {

	protected $option	= null;
	protected $view		= null;

	function __construct() {
		$this->set('option', JRequest::getCmd('option'));
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

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

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

class ApiViewKey extends ApiView {
	
	public function display($tpl = null) {
		$this->generateToolbar();
		
		$model		= $this->getModel();
		$row		= $this->get('data');
		
		$return		= 'index.php?option='.$this->option.'&view=keys';
		
		$this->assignRef('return', $return);
		$this->assignRef('model', $model);
		$this->assignRef('row', $row);
		
		parent::display($tpl);
	}
	
	private function generateToolbar() {
		JToolBarHelper::title(JText::_('COM_API').': '.JText::_('COM_API_KEYS'));
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
	
}

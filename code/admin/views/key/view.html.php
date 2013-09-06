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
		//$this->generateToolbar();
		
		$model		= $this->getModel();
		//print_r($model);die();
		$row		= $this->get('data');
		//print_r($row);die();
		$return1		= 'index.php?option='.$this->option.'&view=keys';
		
		$this->return1	=$return1;
		$this->model	=$model;
		$this->row	=$row;
		
		//$this->assignRef('return', $return);
		//$this->assignRef('model', $model);
		//$this->assignRef('row', $row);
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

		$this->generateToolbar();
		//parent::display($tpl);
		parent::display($tpl);
	}
	
	private function generateToolbar() {
		//require_once JPATH_COMPONENT.'/helpers/api_my.php';

		JToolBarHelper::title(JText::_('COM_API').': '.JText::_('COM_API_KEYS'));
		JToolBarHelper::save('save_close');
		JToolbarHelper::save2new('save_new');
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		
		
		/*JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->row->id == 0);
        if (isset($this->row->checked_out)) {
		    $checkedOut	= !($this->row->checked_out == 0 || $this->row->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
		$canDo		= Api_myHelper::getActions();

		//JToolBarHelper::title(JText::_('COM_API_MY_TITLE_KEY'), 'key.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			//JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		
		if (empty($this->row->id)) {
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		}
		//JToolBarHelper::back('back' , 'index.php?option=com_api&view=keys');
*/
	}
	
}

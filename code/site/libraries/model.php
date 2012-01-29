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

jimport('joomla.application.component.model');

class ApiModel extends JModel {
	
	public function __construct($config=array()) {
		parent::__construct($config);
	}
	
	public function getPagination() {
		
		if (!$this->get('total')) :
			$this->getTotal();
		endif;
		
		if (empty($this->pagination)) {
		  jimport('joomla.html.pagination');
		  $this->pagination = new JPagination($this->get('total'), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->pagination;
  	}
	
}
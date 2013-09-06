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

class ApiModelKeys extends ApiModel
{

	protected $option 		= null;
	protected $view			= null;
	protected $context		= null;
	protected $pagination 	= null;

	protected $list			= null;
	protected $total		= null;

	public function __construct()
	{
		parent::__construct();

		$this->option  = JRequest::getCmd('option');
		$this->view    = JRequest::getCmd('view');
		$this->context = $this->option . '.categories';

		$this->populateState();    
	}

	protected function populateState()
	{
    	$app = JFactory::getApplication();

		$search 			= $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);
		
		$limit 				= $app->getUserStateFromRequest($this->context.'.limit', 'limit', '', 'string');
		$this->setState('limit', $limit);
		
		$limitstart 		= $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', '', 'string');
		$this->setState('limitstart', $limitstart);
		
		$filter_order		= $app->getUserStateFromRequest($this->context.'.filter.order', 'filter_order', 'k.created', 'string');
		$this->setState('filter.order', $filter_order);
		
		$filter_order_Dir	= $app->getUserStateFromRequest($this->context.'.filter.order_dir', 'filter_order_Dir', 'DESC', 'string');
		$this->setState('filter.order_dir', $filter_order_Dir);
  	}
	
	public function getList($override=false, $filter=true) {
		if (!$override && $this->get('list') !== null) :
			return $this->get('list');
		endif;
		
		$where	= $this->buildWhere();
		$order	= $this->buildOrder();
		
		$query	= "SELECT k.*, u.name, u.username "
				."FROM #__api_keys AS k "
				."LEFT JOIN #__users AS u ON u.id = k.user_id "
				.$where
				.$order
				;
				
		$this->_db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$this->list	= $this->_db->loadObjectList();
		
		if ($filter) :
			$this->filterList($this->list);
		endif;
		
		return $this->list;
	}
	
	private function filterList( &$list )
	{
		for ( $i = 0; $i < count( $list ); $i++ ) {
			$row				= $list[$i];
			$row->checked_out	= false;
			$row->checked 		= JHTML::_('grid.checkedout', $row, $i );
			$row->published_html = JHTML::_('grid.published', $row, $i);
			$row->admin_link 	= 'index.php?option='.$this->get('option').'&view=key&cid[]='.$row->id;
		}
	}
	
	public function getTotal( $override = false )
	{
		if ( !$override && $this->get( 'total' ) !== null ) {
			return $this->get( 'list' );
		}

		$where = $this->buildWhere();
		$order = $this->buildOrder();

		$query	= "SELECT COUNT(*) "
				. "FROM #__api_keys AS k"
				. $where
				. $order
				;

		$this->_db->setQuery( $query );
		$this->total = $this->_db->loadResult();

		return $this->total;
	}

	private function buildWhere()
	{
		$where  = null;
		$wheres = array();

		if ( !empty( $wheres ) ) {
			$where = " WHERE " . implode( ' AND ', $wheres );
		}

		return $where;
	}

	private function buildOrder()
	{
		$ordering = null;

		$ordering = " ORDER BY " . $this->getState( 'filter.order' )
			. ' ' . $this->getState( 'filter.order_dir' );

		return $ordering;
	}
}

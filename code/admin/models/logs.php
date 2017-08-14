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

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Api records.
 */
class ApiModelLogs extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'user', 'u.name',
                'hash', 'a.hash',
                'ip_address', 'a.ip_address',
                'time', 'a.time',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_api');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.time', 'desc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
			// Create a new query object.
			$db = $this->getDbo();
			$query = $db->getQuery(true);

			// Select the required fields from the table.
			$query->select(
							$this->getState(
											'list.select', 'DISTINCT a.*'
							)
			);
			$query->from('`#__api_logs` AS a');
			$query->join('LEFT', $db->quoteName('#__api_keys', 'k') . ' ON a.hash=k.hash');
			$query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON k.userid=u.id');
      $query->select('u.name, u.id AS uid');
        

			// Filter by search in title
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				if (substr($search,0,3) == 'uid') {
					$query->where('u.id = ' . (int)substr($search,4));
				} elseif(substr($search,0,2) == 'ip') {
					$query->where('a.ip_address = ' . $db->Quote(substr($search,3)));
				} else {
					$search = $db->Quote('%' . $db->escape($search, true) . '%');
					$query->where(	'a.request LIKE ' . $search . 
													' OR a.post_data LIKE ' . $search . 
													' OR u.name LIKE ' . $search . 
													' OR a.hash LIKE ' . $search);
				}
			}

			// Add the list ordering clause.
			$orderCol = $this->state->get('list.ordering');
			$orderDirn = $this->state->get('list.direction');
			if ($orderCol && $orderDirn) {
					$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}

			return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }
    
    public function delete($cid) {
			$table = JTable::getInstance('Log', 'ApiTable');
			
			foreach ($cid as $id) {
				return $table->delete($id);
			}
		}

}

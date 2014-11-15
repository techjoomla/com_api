<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */
// No direct access
defined('_JEXEC') or die;

/**
 * key Table class
 */
class ApiTablekey extends JTable
{
    
    /**
     * Constructor
     * @param JDatabase A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__api_keys', 'id', $db);
    }
    
    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param    array        Named array
     * @return    null|string    null is operation was satisfactory, otherwise returns an error
     * @see        JTable:bind
     * @since    1.5
     */
    public function bind($array, $ignore = '')
    {
        
        
        $input = JFactory::getApplication()->input;
        $task  = $input->getString('task', '');
        if (($task == 'save' || $task == 'apply') && (!JFactory::getUser()->authorise('core.edit.state', 'com_api') && $array['state'] == 1))
        {
            $array['state'] = 0;
        }
        if ($array['id'] == 0)
        {
            $array['created_by'] = JFactory::getUser()->id;
        }
        
        if (isset($array['params']) && is_array($array['params']))
        {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }
        
        if (isset($array['metadata']) && is_array($array['metadata']))
        {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }
        if (!JFactory::getUser()->authorise('core.admin', 'com_api.key.' . $array['id']))
        {
            $actions         = JFactory::getACL()->getActions('com_api', 'key');
            $default_actions = JFactory::getACL()->getAssetRules('com_api.key.' . $array['id'])->getData();
            $array_jaccess   = array();
            foreach ($actions as $action)
            {
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $this->setRules($array['rules']);
        }
        
        return parent::bind($array, $ignore);
    }
    
    /**
     * This function convert an array of JAccessRule objects into an rules array.
     * @param type $jaccessrules an arrao of JAccessRule objects.
     */
    private function JAccessRulestoArray($jaccessrules)
    {
        $rules = array();
        foreach ($jaccessrules as $action => $jaccess)
        {
            $actions = array();
            foreach ($jaccess->getData() as $group => $allow)
            {
                $actions[$group] = ((bool) $allow);
            }
            $rules[$action] = $actions;
        }
        return $rules;
    }
    
    /**
     * Overloaded check function
     */
    public function check()
    {
        
        if (!$this->userid)
        {
            JError::raiseWarning(100, JText::_('COM_API_KEY_NO_USER'));
            return false;
        }
        
        return parent::check();
    }
    
    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;
        
        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state  = (int) $state;
        
        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array(
                    $this->$k
                );
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }
        
        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);
        
        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
        {
            $checkin = '';
        }
        else
        {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }
        
        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery('UPDATE `' . $this->_tbl . '`' . ' SET `state` = ' . (int) $state . ' WHERE (' . $where . ')' . $checkin);
        $this->_db->query();
        
        // Check for a database error.
        if ($this->_db->getErrorNum())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        
        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin each row.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }
        
        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks))
        {
            $this->state = $state;
        }
        
        $this->setError('');
        return true;
    }
    
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);
        if ($result)
        {
            
            
        }
        return $result;
    }
    
    public function store($updateNulls = false)
    {
        if ($this->userid)
        {
            if (!$this->hash)
            {
                $string     = $this->userid . time();
                $this->hash = md5($string); //@TODO : Better hashing algo
            }
            return parent::store($updateNulls = false);
        }
        
    }
    
    public function setLastUsed($hash)
    {
        $key = $this->loadByHash($hash);
        
        $date            = JFactory::getDate();
        $this->last_used = $date->toSql();
        $this->store();
    }
    
    public function loadByHash($hash)
    {
        $this->load(array(
            'hash' => $hash
        ));
    }
    
}

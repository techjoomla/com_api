<?php

/**
 * @version     1.0.0
 * @package     com_api_my
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// No direct access
defined('_JEXEC') or die;

/**
 * key Table class
 */
class ApiTablekey extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
		//echo "ff";
        parent::__construct('#__api_keys', 'id', $db);
    }

    

}

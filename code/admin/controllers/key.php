<?php
/**
 * @version     1.0.0
 * @package     com_api
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Key controller class.
 */
class ApiControllerKey extends JControllerForm
{

    function __construct() {
        $this->view_list = 'keys';
        parent::__construct();
    }

}
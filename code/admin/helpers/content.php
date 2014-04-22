<?php
/**
 * @version		$Id: article.php 21593 2011-06-21 02:45:51Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// This file is just here to trick Joomla's Article model to act as if we are loading an article
require_once JPATH_ADMINISTRATOR . '/components/com_content/helpers/content.php';
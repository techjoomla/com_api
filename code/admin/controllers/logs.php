<?php
/**
 * @package     API
 * @subpackage  com_api
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

/**
 * Logs list controller class.
 *
 * @since  1.0
 */
class ApiControllerLogs extends JControllerAdmin
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \JModelLegacy|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   3.0
	 */
	public function getModel($name = 'log', $prefix = 'ApiModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}

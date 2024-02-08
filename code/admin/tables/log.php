<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Table\Table;
use Joomla\Data\DataObject;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Access\Access;
use Joomla\Utilities\ArrayHelper;

/**
 * Log Table class
 *
 * @since  1.0
 */
class ApiTablelog extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DataObjectbaseDriver  &$db  Database object
	 *
	 * @since  1.0
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__api_logs', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the Table instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $array   An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function bind($array, $ignore = '')
	{
		$input = Factory::getApplication()->input;
		$task = $input->getString('task', '');

		if ($array['id'] == 0)
		{
			$array['created_by'] = Factory::getUser()->id;
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new Registry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (! Factory::getUser()->authorise('core.admin', 'com_api.key.' . $array['id']))
		{
			$actions = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_api/access.xml', "/access/section[@name='key']/");
			$defaultActions = Access::getAssetRules('com_api.key.' . $array['id'])->getData();
			$arrayJaccess = array();

			foreach ($actions as $action)
			{
				$arrayJaccess[$action->name] = $defaultActions[$action->name];
			}

			$array['rules'] = $this->RulestoArray($arrayJaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * This function convert an array of Rule objects into an rules array.
	 *
	 * @param   array  $jaccessrules  an array of Rule objects.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	private function RulestoArray($jaccessrules)
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
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function store($updateNulls = false)
	{
		if (is_array($this->post_data))
		{
			$this->post_data = ArrayHelper::toString($this->post_data, '=', '&');
		}

		return parent::store($updateNulls = false);
	}

	/**
	 * Method to load an asset by its name.
	 *
	 * @param   string  $hash  The hash of the record.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function loadByHash($hash)
	{
		$this->load(array('hash' => $hash));
	}
}

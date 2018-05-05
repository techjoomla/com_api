<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');

/**
 * Abstract Form Field class
 *
 * @since  1.0
 */
class JFormFieldCreatedby extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = "createdby";

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Load user
		$userId = $this->value;

		if ($userId)
		{
			$user = JFactory::getUser($userId);
		}
		else
		{
			$user = JFactory::getUser();
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		}

		$html[] = "<div>" . $user->name . " (" . $user->username . ")</div>";

		return implode(" ", $html);
	}
}

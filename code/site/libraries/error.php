<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_api
 *
 * @copyright   Copyright (C) 2024 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * API Error Handler
 *
 * @since  1.0.0
 */
class ApiError
{
	/**
	 * Raise an API error
	 *
	 * @param   int     $code             Error code
	 * @param   string  $msg              Error message
	 * @param   string  $exceptionClass   Exception class to use
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
	public static function raiseError(int $code, string $msg, string $exceptionClass = 'APIException'): void
	{
		throw new $exceptionClass($msg, $code);
	}
}

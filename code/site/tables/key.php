<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die;

class ApiTableKey extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__api_keys', 'id', $db );
	}
}
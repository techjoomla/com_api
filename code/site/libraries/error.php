<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

class ApiError extends JException
{
	function raiseError($code, $msg)
	{
		//throw new Exception($msg, $code);
		//return ApiResource::getErrorResponse( $code, $msg );
		throw new Exception($msg, $code);
	}
}

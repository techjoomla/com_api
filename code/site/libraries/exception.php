<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

class ApiException extends JException
{
	public function toArray()
	{
		//print_r($this->code);die("in exception class");
		return ApiResource::getErrorResponse( $this->code, $this->message );
	}
}

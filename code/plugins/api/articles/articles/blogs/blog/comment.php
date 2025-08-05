<?php
/**
 * @package API plugins
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://www.techjoomla.com
*/
class CommentSimpleSchema {

	public $commentid;
	
	public $postid;
	
	public $title;
	
	public $text;
	
	public $textplain;
	
	public $created_date;
	
	public $updated_date;
	
	public $created_date_elapsed;
	
	public $author;
	
	public function __construct() {
		$this->author 		= new PersonSimpleSchema;
	}

}

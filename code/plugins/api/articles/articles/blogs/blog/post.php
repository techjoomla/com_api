<?php
//jimport('simpleschema.easyblog.person');
//jimport('simpleschema.easyblog.category');
require_once JPATH_SITE.'/plugins/api/articles/articles/blogs/category.php';
require_once JPATH_SITE.'/plugins/api/articles/articles/blogs/person.php';

class PostSimpleSchema {

	public $postid;
	
	public $title;
	
	public $introtext;
	
	public $text;
	
	public $textplain;
	
	public $image = array();
	
	public $created_date;
	
	public $created_date_elapsed;
	
	public $updated_date;
	
	public $author;
	
	public $comments;
	
	public $url;
	
	public $tags = array();
	
	public $rating;
	
	public $category;
	
	public function __construct() {
		$this->author 		= new PersonSimpleSchema;
		$this->category 	= new CategorySimpleSchema;
	}

}

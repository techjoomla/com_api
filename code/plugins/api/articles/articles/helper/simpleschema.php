<?php

/**
 * @package API plugins
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://www.techjoomla.com
*/

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
//jimport( 'simpleschema.easyblog.blog.post' );
require_once JPATH_SITE.'/plugins/api/articles/articles/blogs/blog/post.php';
require_once JPATH_SITE.'/plugins/api/articles/articles/blogs/category.php';
require_once JPATH_SITE.'/plugins/api/articles/articles/helper/contenthelper.php';

//for world merit development
//require_once JPATH_SITE.'/components/com_content/models/media.php';

class BlogappSimpleSchema 
{
	public function mapPost($blog, $strip_tags='', $text_length=0, $skip=array())
	{

		$creator = Factory::getUser( $blog->created_by );
		
		$item = new PostSimpleSchema;
	
		$item->postid = $blog->id;
		$item->title = $blog->title;
		$item->introtext = $this->sanitize($blog->introtext);

		if(!isset($blog->fulltext))
		{
			$blog->fulltext = '';
		}
		$item->textplain = $blog->fulltext;
		$item->text = $this->sanitize($blog->fulltext);
        $item->imagem_do_cabecalho = $blog->imagem_do_cabecalho;
		
		if(empty($item->text))
		{
			$item->text = $blog->introtext." ".$blog->fulltext; 
			$item->textplain = $blog->introtext." ".$blog->fulltext; 
		}
		
		$item->textplain = $item->textplain;

		$item->image = new stdClass();
		
		$img_obj = json_decode($blog->images);
		if(isset($img_obj->image_intro))
		{
			$item->image = (string)Uri::root().$img_obj->image_intro;
			//$item->image->full_image_url = Uri::root().$img_obj->image_fulltext;
		}
		else
		{
			//get media from custom component
			$media_obj = new TZ_PortfolioModelMedia();
			$med = $media_obj->getMedia($blog->id);

			//$item->image->url = (string)Uri::root().$med[count($med) - count($med)]->images;
			//$item->image->full_image_url = (string)Uri::root().$med[count($med) - count($med)]->images;
			$item->image = Uri::root().str_replace('.'.JFile::getExt($med[0] -> images),'_'
                                          .'S'.'.'.JFile::getExt($med[0] -> images),$med[0] -> images);
			/*$item->image->full_image_url = Uri::root().str_replace('.'.JFile::getExt($med[0] -> images),'_'
                                          .'L'.'.'.JFile::getExt($med[0] -> images),$med[0] -> images);*/
		}
		$item->created_date = JHTML::_('date', $blog->created, Text::_('DATE_FORMAT_LC2'));

		$item->author->name = $creator->username;
		$item->author->photo = null;
		
		$item->categoryid = $blog->catid;
		$item->category = $blog->category_title;
		
		$item->url = Uri::root() . trim('index.php?option=com_content&view=article&id=' . $item->postid .':'.$blog->alias );
		
		//load content module and position
		$c_obj = new BlogappContentHelper();
		$c_obj->loadContent($item);
		
		if(strpos($item->text,'src="data:image') == false)
		{	
			
			if (strpos($item->text,'href="index'))
			{
				$item->introtext = str_replace('href="index','href="'.Uri::root().'index',$item->introtext);
			    $item->text = str_replace('href="index','href="'.Uri::root().'index',$item->text);
				//$item->text = str_replace('src="','src="'.Uri::root(),$item->text);	
			}
			
			if (strpos($item->text,'href="images'))
			{
				$item->introtext = str_replace('href="images','href="'.Uri::root().'images',$item->introtext);
			    $item->text = str_replace('href="images','href="'.Uri::root().'images',$item->text);
				//$item->text = str_replace('src="','src="'.Uri::root(),$item->text);	
			}

			if ( strpos($item->text,'src="images') || strpos($item->introtext,'src="images') )
			{		    
			    $item->introtext = str_replace('src="','src="'.Uri::root(),$item->introtext);
				$item->text = str_replace('src="','src="'.Uri::root(),$item->text);	
			}

			if ( strpos($item->text,'src="/'))
			{		    
			    $item->introtext = str_replace('src="/','src="'.'http://',$item->introtext);
				$item->text = str_replace('src="/','src="'.'http://',$item->text);	
			}
			
			if ( strpos($item->text,'href="/'))
			{		    
			    $item->introtext = str_replace('href="/','href="'.'http://',$item->introtext);
				$item->text = str_replace('href="/','href="'.'http://',$item->text);	
			}
		}
		//code for set image to blog if it absent
		/*if(empty($item->image->url))
		{
			$dom = new domDocument;
			$dom->loadHTML($item->text);
			$dom->preserveWhiteSpace = false;
			$images = $dom->getElementsByTagName('img');
			
			foreach($images as $img)
			{
			    	$item->image->url = $img->getAttribute('src');

				if(!empty($item->image->url))
					break;
			}

		}*/

		return $item;
	}
	
	public function mapCategory( $cat )
	{
		$item = new CategorySimpleSchema();
		
		$item->id = $cat->id;
		$item->title = $cat->title;;
		//$item->description = $this->sanitize($cat->introtext);
		//$item->created_date = $cat->created;
		$item->path = $cat->path;
		$item->level = $cat->level;
		$item->lft = $cat->lft;
		$item->rgt = $cat->rgt;
		
		return $item;
	}
	
	public function sanitize($text) {
		$text = htmlspecialchars_decode($text);
		$text = str_ireplace('&nbsp;', ' ', $text);
		
		return $text;
	}
		
}



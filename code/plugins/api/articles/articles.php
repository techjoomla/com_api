<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class plgAPIArticles extends ApiPlugin
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config = array());

		//load helper file
		require_once JPATH_SITE.'/plugins/api/articles/articles/helper/simpleschema.php';

		ApiResource::addIncludePath(dirname(__FILE__).'/articles');

		// Load component language
		$this->loadComponentLanguage();

		// Set resources & access
		$this->setResourceAccess('article', 'public', 'get');     // Singular - one article by ID
		$this->setResourceAccess('articles', 'public', 'get');    // Plural - multiple articles
		$this->setResourceAccess('category', 'public', 'get');
		$this->setResourceAccess('latest', 'public', 'get');
	}

	/**
	 * Load component language files
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function loadComponentLanguage(): void
	{
		try {
			$app = Factory::getApplication();
			$lang = Factory::getLanguage();
			
			// Get language parameter from request
			$requestedLang = $app->input->get('lang', 'pt', 'STRING');
			$languageTag = $requestedLang === 'en' ? 'en-GB' : 'pt-BR';
			
			// Load content component language
			$extension = 'com_content';
			$baseDir = JPATH_ADMINISTRATOR;
			$reload = true;
			
			$lang->load($extension, $baseDir, $languageTag, $reload);
			
			// Also load site language files
			$lang->load($extension, JPATH_SITE, $languageTag, $reload);
			
			// Load Joomla core language
			$lang->load('joomla', JPATH_ADMINISTRATOR, $languageTag, $reload);
			$lang->load('joomla', JPATH_SITE, $languageTag, $reload);
			
		} catch (Exception $e) {
			// Log error but don't break execution
			Factory::getApplication()->enqueueMessage(
				Text::sprintf('PLG_API_ARTICLES_ERROR_LOADING_LANGUAGE', $e->getMessage()),
				'warning'
			);
		}
	}
}

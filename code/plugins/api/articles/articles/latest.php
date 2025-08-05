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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Carrega o modelo moderno do Joomla 5
BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_content/src/Model', 'Joomla\\Component\\Content\\Site\\Model\\');


class ArticlesApiResourceLatest extends ApiResource
{
	
	public function get()
	{
		$this->plugin->setResponse($this->getLatest());
	}
	//get latest article
	public function getLatest()
	{
		// Get the dbo
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$result = new stdClass();
		$app = Factory::getApplication();
		
		// Get parameters
		$lang = $app->input->get('lang', 'pt', 'STRING');
		$offset = $app->input->get('offset', 0, 'INT');
		$categoryId = $app->input->get('catid', 0, 'INT');
		
		// Handle limit with nolimit support
		$nolimit = $app->input->get('nolimit', 0, 'INT');
		if ($nolimit == 1) {
			$limit = 0; // No limit
		} else {
			$limit = $app->input->get('limit', 20, 'INT');
		}
		
		$languageCode = $lang === 'en' ? 'en-GB' : 'pt-BR';

		try {
			// Busca direta no banco de dados - mais simples e compatível com Joomla 5
			$query = $db->getQuery(true)
				->select('a.id, a.title, a.alias, a.introtext, a.fulltext, a.created, a.publish_up, a.catid, a.access')
				->select('c.title AS category_title, c.alias AS category_alias')
				->from('#__content AS a')
				->join('LEFT', '#__categories AS c ON c.id = a.catid')
				->where('a.state = 1')
				->where('a.publish_up <= NOW()')
				->where('(a.publish_down IS NULL OR a.publish_down >= NOW())')
				->where('(a.language = ' . $db->quote($languageCode) . ' OR a.language = ' . $db->quote('*') . ')');
			
			// Add category filter if specified
			if ($categoryId > 0) {
				$query->where('a.catid = ' . (int) $categoryId);
			}
			
			$query->order('a.publish_up DESC');
			
			// Apply limit and offset
			if ($limit > 0) {
				$query->setLimit($limit, $offset);
			}
			// For nolimit=1, don't apply any limit

			$db->setQuery($query);
			$items = $db->loadObjectList();

			if ($items) {
				foreach ($items as $item) {
					// Criar slug completo
					$item->slug = $item->id . ':' . $item->alias;
					$item->catslug = $item->catid . ':' . $item->category_alias;
					
					// Adicionar link do artigo
					$item->link = 'index.php?option=com_content&view=article&id=' . $item->slug . '&catid=' . $item->catslug;
				}
				
				$result->success = 1;
				$result->data = $items;
			} else {
				$result->success = 0;
				$result->data = [];
				$result->message = 'No articles found';
			}
		} catch (Exception $e) {
			$result->success = 0;
			$result->data = [];
			$result->message = 'Error fetching articles: ' . $e->getMessage();
		}

		return $result;
	}
	
	public function post()
	{  
		$this->plugin->setResponse("Use get method");
	}
}

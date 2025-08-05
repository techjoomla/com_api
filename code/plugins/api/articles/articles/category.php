<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  API.Articles
 *
 * @copyright   Copyright (C) 2024 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;


class ArticlesApiResourceCategory extends ApiResource
{
	public function get()
	{
		$this->plugin->setResponse($this->getCategory());
	}
	
	public function getCategory()
	{
		// Get the dbo
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$result = new stdClass();
		$app = Factory::getApplication();
		
		// Get parameters
		$catid = $app->input->get('id', 0, 'INT');
		$lang = $app->input->get('lang', 'pt', 'STRING');
		
		// Get pagination parameters
		$limit = $app->input->get('limit', 20, 'int');
		$offset = $app->input->get('offset', 0, 'int');
		$limitstart = $app->input->get('limitstart', $offset, 'int'); // backward compatibility
		
		// Get language code for filtering
		$languageCode = $lang === 'en' ? 'en-GB' : 'pt-BR';

		try {
			if ($catid) {
				// Get articles from specific category
				$query = $db->getQuery(true)
					->select('a.id, a.title, a.alias, a.introtext, a.fulltext, a.created, a.publish_up, a.catid, a.access')
					->select('c.title AS category_title, c.alias AS category_alias')
					->from('#__content AS a')
					->join('LEFT', '#__categories AS c ON c.id = a.catid')
					->where('a.state = 1')
					->where('a.catid = ' . (int) $catid)
					->where('a.publish_up <= NOW()')
					->where('(a.publish_down IS NULL OR a.publish_down >= NOW())')
					->where('(a.language = ' . $db->quote($languageCode) . ' OR a.language = ' . $db->quote('*') . ')')
					->order('a.publish_up DESC');

				// Apply limit and offset
				if ($limit > 0) {
					$db->setQuery($query, $limitstart, $limit);
				} else {
					$db->setQuery($query);
				}

				$items = $db->loadObjectList();

				if ($items) {
					foreach ($items as $item) {
						// Criar slug completo
						$item->slug = $item->id . ':' . $item->alias;
						$item->catslug = $item->catid . ':' . $item->category_alias;
						$item->link = 'index.php?option=com_content&view=article&id=' . $item->slug . '&catid=' . $item->catslug;
					}
				}

				$result->success = 1;
				$result->data = $items;
			} else {
				// Get all categories
				$query = $db->getQuery(true)
					->select('c.id, c.title, c.alias, c.description, c.published, c.parent_id')
					->select('COUNT(a.id) AS article_count')
					->from('#__categories AS c')
					->join('LEFT', '#__content AS a ON a.catid = c.id AND a.state = 1 AND (a.language = ' . $db->quote($languageCode) . ' OR a.language = ' . $db->quote('*') . ')')
					->where('c.published = 1')
					->where('c.extension = ' . $db->quote('com_content'))
					->where('(c.language = ' . $db->quote($languageCode) . ' OR c.language = ' . $db->quote('*') . ')')
					->group('c.id')
					->order('c.title ASC');

				// Apply limit and offset for categories list too
				if ($limit > 0) {
					$db->setQuery($query, $limitstart, $limit);
				} else {
					$db->setQuery($query);
				}

				$items = $db->loadObjectList();

				if ($items) {
					foreach ($items as $item) {
						// Criar slug completo
						$item->slug = $item->id . ':' . $item->alias;
						$item->link = 'index.php?option=com_content&view=category&id=' . $item->slug;
					}
				}

				$result->success = 1;
				$result->data = $items;
			}
		} catch (Exception $e) {
			$result->success = 0;
			$result->data = [];
			$result->message = 'Error fetching data: ' . $e->getMessage();
		}

		return $result;
	}
	
	public function post()
	{  
		$this->plugin->setResponse("Use GET method");
	}
}

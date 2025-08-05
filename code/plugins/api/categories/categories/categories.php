<?php
/**
 * @package     API
 * @subpackage  com_api
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Component\Categories\Administrator\Model\CategoriesModel;
use Joomla\CMS\Filter\InputFilter;

/**
 * Categories API resource class
 *
 * @package  API
 * @since    1.6.0
 */
class CategoriesApiResourceCategories extends ApiResource
{
	/**
	 * Get categories
	 *
	 * @return  object  Categories list wrapped inside standard api response wrapper
	 */
	public function get()
	{
		$this->plugin->setResponse($this->getCategoriesList());
	}

	/**
	 * Get list of categories based on input params
	 *
	 * @return  array
	 *
	 * @since   1.6.0
	 */
	public function getCategoriesList()
	{
		// Get application parameters
		$app   = Factory::getApplication();
		$input = $app->input;

		// Get pagination parameters with defaults
		$limit = $input->get('limit', 20, 'int');
		$offset = $input->get('offset', 0, 'int');
		$limitStart = $input->get('limitstart', $offset, 'int'); // backward compatibility
		$search = $input->get('search', '', 'string');

		// Get filters
		$filters = $input->get('filters', '', 'array');
		$inputFilter = InputFilter::getInstance();

		// Cleanup and set default values
		$extension = isset($filters['extension']) ? $inputFilter->clean($filters['extension'], 'cmd') : 'com_content';
		$language = isset($filters['language']) ? $inputFilter->clean($filters['language'], 'string') : '';
		$level = isset($filters['level']) ? $inputFilter->clean($filters['level'], 'string') : '';
		$published = isset($filters['published']) ? $inputFilter->clean($filters['published'], 'int') : 1;

		// Get database instance
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		// Build the query
		$query->select('c.id, c.title, c.alias, c.description, c.published, c.access, c.language, c.level, c.extension')
			->from('#__categories AS c')
			->where('c.extension = ' . $db->quote($extension))
			->where('c.published = ' . (int) $published);

		// Apply search filter
		if (!empty($search)) {
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(c.title LIKE ' . $search . ' OR c.description LIKE ' . $search . ')');
		}

		// Apply language filter
		if (!empty($language)) {
			$query->where('c.language IN (' . $db->quote($language) . ', ' . $db->quote('*') . ')');
		}

		// Apply level filter
		if (!empty($level)) {
			$query->where('c.level <= ' . (int) $level);
		}

		// Order by
		$query->order('c.lft ASC');

		// Apply limit and offset
		if ($limit > 0) {
			$db->setQuery($query, $limitStart, $limit);
		} else {
			$db->setQuery($query);
		}

		try {
			$categories = $db->loadObjectList();
			return $categories ? $categories : [];
		} catch (Exception $e) {
			return [];
		}
	}
}

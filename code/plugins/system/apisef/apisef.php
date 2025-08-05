<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.ApiSef
 * @copyright   Copyright (C) 2025 Machado Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

/**
 * System plugin to handle API SEF URLs
 *
 * @since  1.0.0
 */
class PlgSystemApiSef extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * After initialize event - executed before routing
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onAfterInitialise()
	{
		if ($this->app->isClient('administrator'))
		{
			return;
		}

		// Get current URI path
		$uri = Uri::getInstance();
		$path = trim($uri->getPath(), '/');
		$segments = explode('/', $path);

		// Debug log
		file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
			date('Y-m-d H:i:s') . ' - ApiSef Plugin triggered for path: ' . $path . PHP_EOL, FILE_APPEND);

		// Remove site path if present
		$sitePath = trim(Uri::base(true), '/');
		if (!empty($sitePath))
		{
			$siteSegments = explode('/', $sitePath);
			$segments = array_slice($segments, count($siteSegments));
		}

		// Check for API SEF pattern: {lang}/api/{app}/{resource}
		// Always requires language prefix
		if (count($segments) >= 4 && $segments[1] === 'api')
		{
			file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
				date('Y-m-d H:i:s') . ' - Processing API SEF URL with required lang prefix' . PHP_EOL, FILE_APPEND);
			$this->processApiSefUrl($segments);
		}
	}

	/**
	 * After route event - executed after routing but before dispatch
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onAfterRoute()
	{
		$input = $this->app->input;
		
		// Check if this is a com_api request that came from our SEF URL
		if ($input->get('option') === 'com_api' && $input->get('format') === 'json')
		{
			file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
				date('Y-m-d H:i:s') . ' - onAfterRoute: Processing com_api JSON request' . PHP_EOL, FILE_APPEND);
			
			// Set proper JSON headers
			$this->app->setHeader('Content-Type', 'application/json; charset=utf-8');
		}
	}

	/**
	 * Process API SEF URL and redirect to com_api
	 *
	 * @param   array  $segments  URL segments
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	private function processApiSefUrl($segments)
	{
		$input = $this->app->input;

		// Parse segments
		$lang = $segments[0]; // pt or en
		$api = $segments[1];  // api
		$app = $segments[2];  // advogados, articles, etc.
		$resource = $segments[3]; // advogado, latest, etc.

		// Debug log
		file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
			date('Y-m-d H:i:s') . " - Parsing: lang=$lang, api=$api, app=$app, resource=$resource" . PHP_EOL, FILE_APPEND);

		// Handle resource with ID or codigo
		$resourceParts = explode('/', $resource);
		$actualResource = $resourceParts[0];
		$identifier = isset($resourceParts[1]) ? $resourceParts[1] : null;

		// Set input variables for com_api
		$input->set('option', 'com_api');
		$input->set('app', $app);
		$input->set('resource', $actualResource);
		$input->set('lang', $lang);
		$input->set('format', 'json'); // Always JSON instead of raw

		file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
			date('Y-m-d H:i:s') . " - Set variables: option=com_api, app=$app, resource=$actualResource, lang=$lang" . PHP_EOL, FILE_APPEND);

		// Handle identifier (ID or codigo)
		if ($identifier !== null)
		{
			if (is_numeric($identifier))
			{
				$input->set('id', $identifier);
				file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
					date('Y-m-d H:i:s') . " - Set ID: $identifier" . PHP_EOL, FILE_APPEND);
			}
			else
			{
				$input->set('codigo', $identifier);
				file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
					date('Y-m-d H:i:s') . " - Set codigo: $identifier" . PHP_EOL, FILE_APPEND);
			}
		}

		// Handle query parameters from original URL
		$query = Uri::getInstance()->getQuery(true);
		foreach ($query as $key => $value)
		{
			// Skip basic routing parameters, but allow id/codigo if not set from URL segments
			if (!in_array($key, ['option', 'app', 'resource', 'lang', 'format']))
			{
				// For id/codigo, only exclude if we already set it from URL segments
				if (($key === 'id' || $key === 'codigo') && $identifier !== null)
				{
					file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
						date('Y-m-d H:i:s') . " - Skipping query param $key=$value (overridden by URL segment)" . PHP_EOL, FILE_APPEND);
					continue;
				}
				
				$input->set($key, $value);
				file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
					date('Y-m-d H:i:s') . " - Set query param: $key=$value" . PHP_EOL, FILE_APPEND);
			}
		}

		// Build the internal URL for com_api with JSON format
		$apiUrl = Uri::base() . 'index.php?option=com_api';
		$apiUrl .= '&app=' . urlencode($app);
		$apiUrl .= '&resource=' . urlencode($actualResource);
		$apiUrl .= '&lang=' . urlencode($lang);
		$apiUrl .= '&format=json'; // Always JSON instead of raw
		
		if ($identifier !== null) {
			if (is_numeric($identifier)) {
				$apiUrl .= '&id=' . urlencode($identifier);
			} else {
				$apiUrl .= '&codigo=' . urlencode($identifier);
			}
		}
		
		// Add query parameters
		foreach ($query as $key => $value) {
			// Skip basic routing parameters, but include id/codigo if not from URL segments
			if (!in_array($key, ['option', 'app', 'resource', 'lang', 'format'])) {
				// For id/codigo, only exclude if we already set it from URL segments
				if (($key === 'id' || $key === 'codigo') && $identifier !== null) {
					continue;
				}
				$apiUrl .= '&' . urlencode($key) . '=' . urlencode($value);
			}
		}

		// Add custom limit if not already present in query and unlimited mode is disabled
		if (!isset($query['limit']) && !$this->params->get('unlimited_mode', 0)) {
			$customLimit = $this->getCustomLimit($app);
			if ($customLimit > 0) {
				$apiUrl .= '&limit=' . $customLimit;
				file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
					date('Y-m-d H:i:s') . " - Added custom limit: $customLimit for app: $app" . PHP_EOL, FILE_APPEND);
			}
		} elseif ($this->params->get('unlimited_mode', 0)) {
			// In unlimited mode, set a very high limit if not specified
			if (!isset($query['limit'])) {
				$apiUrl .= '&limit=999999';
				file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
					date('Y-m-d H:i:s') . " - Unlimited mode: set limit to 999999" . PHP_EOL, FILE_APPEND);
			}
		}

		// Handle special nolimit parameter
		if (isset($query['nolimit']) && $query['nolimit'] == '1') {
			// Override any existing limit with a very high number
			$apiUrl = preg_replace('/&limit=\d+/', '', $apiUrl);
			$apiUrl .= '&limit=999999';
			file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
				date('Y-m-d H:i:s') . " - No limit mode activated: set limit to 999999" . PHP_EOL, FILE_APPEND);
		}

		file_put_contents(JPATH_ROOT . '/administrator/logs/apisef_debug.log', 
			date('Y-m-d H:i:s') . " - Redirecting to JSON: $apiUrl" . PHP_EOL, FILE_APPEND);

		// Redirect to the API URL with 303 status (See Other)
		// This is the cleanest approach for SEF URLs
		$this->app->redirect($apiUrl, 303);
	}

	/**
	 * Get custom limit for specific app
	 *
	 * @param   string  $app  The app name
	 *
	 * @return  int
	 *
	 * @since   1.0.0
	 */
	private function getCustomLimit($app)
	{
		// Check for app-specific limit first
		$appLimit = $this->params->get($app . '_limit', 0);
		if ($appLimit > 0) {
			return $appLimit;
		}

		// Fall back to default limit
		$defaultLimit = $this->params->get('default_limit', 50);
		return $defaultLimit;
	}
}

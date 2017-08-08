<?php
/**
 * @package    Techjoomla.API
 * @copyright  Copyright (C) 2009-2017 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_api
 *
 * @since  2.0.1
 */
class APIRouter extends JComponentRouterBase
{
	private  $views = array('documentation', 'keys');

	/**
	 * Build the route for the com_api component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build( &$query )
	{
		$segments = array();

		if (isset($query['app']))
		{
			$segments[0] = $query['app'];
			$segments[1] = $query['resource'];

			if (isset($query['id']))
			{
				$segments[2] = $query['id'];
			}

			unset($query['app'], $query['resource'], $query['id']);

			return $segments;
		}

		if (isset($query['view']))
		{
			$segments[0] = $query['view'];
			unset($query['view']);
		}

		if (isset($query['layout']))
		{
			$segments[1] = $query['layout'];

			if ($query['layout'] == 'edit' && isset($query['id']))
			{
				$segments[2] = $query['id'];
				unset($query['id']);
			}

			unset($query['layout']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse( &$segments )
	{
		$vars = array();

		if (in_array($segments[0], $this->views))
		{
			$vars['view'] = $segments[0];

			if (isset($segments[1]))
			{
				$vars['layout'] = $segments[1];
			}

			if ($vars['layout'] == 'edit' && isset($segments[2]))
			{
				$vars['id'] = $segments[2];
			}
		}
		else
		{
			$vars['format'] = 'raw';
			$vars['app'] = $segments[0];
			$vars['resource'] = $segments[1];

			if (isset($segments[2]))
			{
				$vars['id'] = $segments[2];
			}
		}

		return $vars;
	}
}

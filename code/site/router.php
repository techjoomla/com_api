<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

function ApiBuildRoute( &$query )
{
	$segments = array();

	if (isset($query['app'])) :
		return $segments;
	endif;

	if (isset($query['view'])) {
		$segments[0] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['layout'])) {
		$segments[1] = $query['layout'];
		if ($query['layout'] == 'edit' && isset($query['id'])) :
			$segments[2] = $query['id'];
			unset($query['id']);
		endif;
		unset($query['layout']);
	}

	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function ApiParseRoute( $segments )
{
	$vars = array();

	if (isset($segments[0])) :
		$vars['view'] = $segments[0];
	endif;
	
	if (isset($segments[1])) :
		$vars['layout'] = $segments[1];
	endif;
	
	if ($vars['layout'] == 'edit' && isset($segments[2])) :
		$vars['id'] = $segments[2];
	endif;
	
	return $vars;
}

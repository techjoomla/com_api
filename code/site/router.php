<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
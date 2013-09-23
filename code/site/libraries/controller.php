<?php
/**
 * @version		$Id
 * @package		Joomla
 * @subpackage	com_api
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class ApiController extends JControllerLegacy
{	
	/**
	 * Base Controller Constructor
	 *
	 * @param array $config Controller initialization configuration parameters
	 * @return void
	 * @since 0.1
	 */
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
		$this->set( 'option', JRequest::getCmd( 'option' ) );
		$app = JFactory::getApplication();

		JModelLegacy::addIncludePath( JPATH_ROOT .DS. 'components' .DS. 'com_api' .DS. 'models' );
		JTable::addIncludePath( JPATH_ROOT .DS. 'components' .DS. 'com_api' .DS. 'tables' );
	}
	
	public function display($cachable = false, $urlparams = false)
	{
		//require_once JPATH_COMPONENT.'/helpers/api_my.php';

		$view		= JFactory::getApplication()->input->getCmd('view', 'keys');
        JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}

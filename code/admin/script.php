<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die;

/**
 * Script file of HelloWorld component
 */
class com_apiInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install( $parent )
	{	
		echo '<p>' . JText::_('COM_API_INSTALL_TEXT') . '</p>';
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall( $parent ) 
	{
		echo '<p>' . JText::_('COM_API_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update( $parent )
	{
		echo '<p>' . JText::_('COM_API_UPDATE_TEXT') . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight( $type, $parent )
	{
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight( $type, $parent )
	{
		if ( in_array( $type, array( 'install', 'update' ) ) ) {
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			jimport('joomla.installer.installer');

			if ( !$this->install_plugins( $type, $parent ) ) {
				JError::raiseWarning( 21, JText::_( 'COM_API_ERROR_INSTALLING_PLUGINS' ) );
			}
		}
	}

	function install_plugins( $type, $parent )
	{
		// Get an installer instance
		$installer = new JInstaller(); // Cannot use the instance that is already created, no!
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$plgs_path = $parent->getParent()->getPath('source') . '/admin/extensions/plugins';
		$returns = array();
		$enable = array();
		$new_in_version = array(
			'api/api', 'api/categories', 'api/content', 'api/core', 'api/language', 'api/menus',
			'api/users', 'system/api'
			);

		if ( !JFolder::exists( $plgs_path ) ) {
			return true;
		}

		// Loop through plugin types
		$plg_types = JFolder::folders( $plgs_path );
		foreach ( $plg_types as $plg_type ) {
			// Loop through plugins
			$plugins = JFolder::folders( $plgs_path .'/'. $plg_type );
			foreach ( $plugins as $plugin ) {
				$p_dir = $plgs_path .'/'. $plg_type .'/'. $plugin .'/';

				// Install the package
				if (!$installer->install($p_dir)) {
					// There was an error installing the package
					JError::raiseWarning( 21, JTEXT::sprintf( 'COM_API_PLUGIN_INSTALL_ERROR',
						$plg_type . '/' . $plugin ) );
					$returns[] = false;
				} else {
					// Package installed sucessfully
					$app->enqueueMessage( JTEXT::sprintf( 'COM_API_PLUGIN_INSTALL_SUCCESS',
						$plg_type . '/' . $plugin ) );
					$returns[] = true;

					// Maybe auto enable?
					if ( 'install' == $type || in_array($plg_type.'/'.$plugin, $new_in_version) ) {
						$enable[] = "(`folder` = '{$plg_type}' AND `element` = '{$plugin}')";
					}
				}
			}
		}

		// Run query
		if ( !empty( $enable ) ) {
			$db->setQuery( "UPDATE #__extensions
				SET `enabled` = 1
					WHERE ( " . implode( ' OR ', $enable ) . " ) AND `type` = 'plugin'" );

			if ( !$db->query() ) {
				JError::raiseWarning( 1, JText::_('COM_API_ERROR_ENABLING_PLUGINS') );

				return false;
			}
		}

		return !in_array( false, $returns, true );
	}
}

<?php
/**
 * Installation script for API SEF Plugin
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

/**
 * Installation class for API SEF Plugin
 */
class PlgSystemApiSefInstallerScript
{
	/**
	 * Called after install/update/uninstall
	 *
	 * @param   string  $type    The action being performed
	 * @param   object  $parent  The class calling this method
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		if ($type === 'install' || $type === 'update')
		{
			$this->enablePlugin();
		}
	}

	/**
	 * Enable the plugin after installation
	 *
	 * @return  void
	 */
	private function enablePlugin()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->update('#__extensions')
			->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('element') . ' = ' . $db->quote('apisef'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));

		$db->setQuery($query);
		$db->execute();
	}
}

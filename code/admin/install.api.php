<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

$db = & JFactory::getDBO();
$install_status = new JObject();
$install_source = $this->parent->getPath('source');

$condtion = array(0 => '\'api\'');
$condtionatype = join(',',$condtion);
if(JVERSION >= '1.6.0')
{
$query = "SELECT element FROM #__extensions WHERE  folder in ($condtionatype)";
}
else
{
$query = "SELECT element FROM #__plugins WHERE folder in ($condtionatype)";
}

$db->setQuery($query);
$status = $db->loadResultArray();

echo JText::_('<br/><br/><span style="font-weight:bold;">Installing API plugins:</span>');

//install users for joomla 2.5 plugin and publish it
	$installer = new JInstaller;
	$result = $installer->install($install_source.'/part_info');
	if (!in_array("part_info", $status)) {
		if(JVERSION >= '1.6.0')
		{
			$query = "UPDATE #__extensions SET enabled=1 WHERE element='part_info' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		else
		{
			$query = "UPDATE #__plugins SET published=1 WHERE element='part_info' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		echo ($result)?JText::_('<br/><span style="font-weight:bold; color:green;">part_info plugin installed and published
				</span>'):JText::_('<br/><span style="font-weight:bold; color:red;">part_info plugin not installed</span>');
	}
	else
	{
		echo JText::_('<br/><span style="font-weight:bold; color:green;">part_info API plugin installed </span>'); 	
	}
	
	//product hierachy
	$installer = new JInstaller;
	$result = $installer->install($install_source.'/product_hierarchy');
	if (!in_array("product_hierarchy", $status)) {
		if(JVERSION >= '1.6.0')
		{
			$query = "UPDATE #__extensions SET enabled=1 WHERE element='product_hierarchy' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		else
		{
			$query = "UPDATE #__plugins SET published=1 WHERE element='product_hierarchy' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		echo ($result)?JText::_('<br/><span style="font-weight:bold; color:green;">product_hierarchy plugin installed and published
				</span>'):JText::_('<br/><span style="font-weight:bold; color:red;">product_hierarchy plugin not installed</span>');
	}
	else
	{
		echo JText::_('<br/><span style="font-weight:bold; color:green;">product_hierarchy API plugin installed </span>'); 	
	}








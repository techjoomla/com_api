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

//install redshop for joomla 1.7 plugin and publish it
	$installer = new JInstaller;
	$result = $installer->install($install_source.DS.'redshop');
	if (!in_array("redshop", $status)) {
		if(JVERSION >= '1.6.0')
		{
			$query = "UPDATE #__extensions SET enabled=1 WHERE element='redshop' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		else
		{
			$query = "UPDATE #__plugins SET published=1 WHERE element='redshop' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		echo ($result)?JText::_('<br/><span style="font-weight:bold; color:green;">Redshop plugin installed and published
				</span>'):JText::_('<br/><span style="font-weight:bold; color:red;">Redshop plugin not installed</span>');
	}
	else
	{
		echo JText::_('<br/><span style="font-weight:bold; color:green;">Redshop plugin installed </span>'); 	
	}
	



//install socialads for joomla 1.7 and publish it
	$installer = new JInstaller;
	$result = $installer->install($install_source.DS.'socialads');
	if (!in_array("socialads", $status)) {
		if(JVERSION >= '1.6.0')
		{
			$query = "UPDATE #__extensions SET enabled=1 WHERE element='socialads' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		else
		{
			$query = "UPDATE #__plugins SET published=1 WHERE element='socialads' AND folder='api'";
			$db->setQuery($query);
			$db->query();
		}
		echo ($result)?JText::_('<br/><span style="font-weight:bold; color:green;">Virtuemart plugin installed and published
				</span>'):JText::_('<br/><span style="font-weight:bold; color:red;">Virtuemart plugin not installed</span>');
		
	}
	else
		echo JText::_('<br/><span style="font-weight:bold; color:green;">Virtuemart plugin installed</span>'); 	







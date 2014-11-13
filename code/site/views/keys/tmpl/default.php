<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/
defined('_JEXEC') or die('Restricted access');
?>

<h1 class="componentheading"><?php echo JText::_('COM_API_REGISTERED_KEYS');?></h1>

<table width="100%" cellpadding="0" cellspacing="0" class="list">
	<tr>
		<th class="sectiontableheader" align="left"><?php echo JText::_('COM_API_DOMAIN');?></th>
		<th class="sectiontableheader" align="left"><?php echo JText::_('COM_API_KEY');?></th>
		<th class="sectiontableheader"><?php echo JText::_('COM_API_ENABLED');?></th>
		<th class="sectiontableheader">&nbsp;</th>
	</tr>
	<?php for($i=0; $i<count($this->tokens); $i++) :
		$t		= $this->tokens[$i];
		$class 	= $i%2 ? 'sectiontableentry2' : 'sectiontableentry1';
		$img	= $t->state ? 'tick.png' : 'publish_x.png';
		$edit_link 		= JRoute::_('index.php?option=com_api&view=keys&layout=edit&id='.$t->id);
		$delete_link 	= JRoute::_('index.php?option=com_api&c=keys&task=delete&id='.$t->id.'&'.$this->session_token.'=1');
		$canChange	= $this->user->authorise('core.edit.state',	'com_api');
	?>
		<tr class="<?php echo $class;?>">
			<td class="api_table_domain"><a href="<?php echo $edit_link;?>"><?php echo $t->domain;?></a></td>
			<td class="api_table_key"><?php echo $t->hash;?></td>
			<td class="api_table_published" align="center">
				<?php echo JHtml::_('jgrid.published', $t->state, $i, 'keys.', $canChange, 'cb'); ?>
			</td>
			<td class="api_table_delete">
				<?php if ($this->can_register) : ?>
					<a href="<?php echo $delete_link;?>">Delete</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endfor; ?>
</table>

<?php if ($this->can_register) : ?>
	<a class="api_new_token" href="<?php echo $this->new_token_link;?>"><?php echo JText::_('COM_API_NEW_KEY');?></a>
<?php endif; ?>

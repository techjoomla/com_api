<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die('Restricted access');
?>

<fieldset class='adminform'>
    <legend><?php echo $this->row->id ? 'Edit' : 'New';?> Category</legend>
		<form action="index.php" method="post" name='adminForm'>
		<table width='100%' cellpadding='5' cellspacing='0' class='admintable form-validate'>
			<tr>
				<td class="key"><?php echo JText::_('COM_API_USER');?></td>
				<td>
					<?php echo JHTML::_('list.users', 'user_id', $this->row->user_id, false, null, 'name', false); ?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_API_DOMAIN');?></td>
				<td>
					<input type="text" class="inputbox api_form_input" name="domain" size="55" value="<?php echo $this->row->domain;?>" />
					<?php echo JHTML::tooltip(JText::_('COM_API_DOMAIN_TOOLTIP'), JText::_('COM_API_DOMAIN')); ?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_API_PUBLISHED');?></td>
				<td>
					<fieldset class="radio">
						<?php echo JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', $this->row->published ); ?>
					</fieldset>
				</td>
			</tr>
			<?php if ($this->row->hash) : ?>
			<tr>
				<td class="key"><?php echo JText::_('COM_API_KEY');?></td>
				<td><?php echo $this->row->hash; ?></td>
			</tr>
			<?php endif; ?>
		</table>
		<input type='hidden' name='id' id='id' value='<?php echo $this->row->id;?>' />
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="c" id="c" value="key" />
		<input type="hidden" name="ret" id="ret" value="<?php echo $this->return;?>" />
		<input type="hidden" name="option" id="option" value="<?php echo $this->option;?>" />
		<?php echo JHTML::_('form.token'); ?>
		</form>
</fieldset>
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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<script type="text/javascript">
<?php if(JVERSION >= '1.6.0'){ ?>
	Joomla.submitbutton = function(action){
<?php } else { ?>
	function submitbutton( action ) {
<?php } ?>

		if(action=='add' || action=='edit')
		{
			document.adminForm.view.value = 'key';
		}
		else
		{
			document.adminForm.view.value = 'keys';
			
		}Joomla.submitform(action);
	return;
	
 }		
</script>
<form action="" method="post" name="adminForm" id="adminForm" class="form-validate">
<table cellpadding='4' cellspacing='0' border='0' width='100%' class='adminlist'>
	<thead>
		<tr>
			<th width="20">#</th>
			<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<th width="30"><?php echo JHTML::_('grid.sort',   JText::_('COM_API_ID'), 'k.id', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?></th>
			<th><?php echo JHTML::_('grid.sort',   JText::_('COM_API_DOMAIN'), 'k.domain', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?></th>
			</th>
			<th><?php echo JText::_('COM_API_USER'); ?></th>
			<th><?php echo JText::_('COM_API_KEY');?></th>
			<th>
				<?php //echo JHTML::_( 'grid.sort', JText::_('COM_API_PUBLISHED'),'k.published', $this->lists['order_Dir'], $this->lists['order']); ?>
				<?php echo JHTML::_('grid.sort',   JText::_('COM_API_PUBLISHED'), 'k.published', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?>
			</th>
	</tr>
	</thead>
	<tbody>
	<?php 
	//print_r($this->rows);
	$count 	= count($this->rows);
	for($i=0; $i<$count; $i++) :
		$row = $this->rows[$i];
		$class = $i%2 ? 'row0' : 'row1';
		?>
		<tr class="<?php echo $class;?>">
			<td><?php echo $this->pagination->getRowOffset($i);?></td>
			<td><?php echo JHTML::_('grid.id', $i, $row->id );?></td>
			<td><?php echo $row->id; ?></td>
			<td>
				<a href="<?php echo $row->admin_link;?>">
					<?php echo $row->domain; ?>
				</a>
			</td>
			<td><?php echo $row->name." (".$row->username.")"; ?></td>
			<td><?php echo $row->hash;?></td>
			<td><?php echo JHTML::_('grid.published', $row, $i ); ?></td>
		</tr>
	<?php endfor; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='15'>
			<?php 
			echo $this->pagination->getListFooter(); 
			 echo $this->pagination->getLimitBox(); 
			?>
			</td>
		</tr>
	</tfoot>
</table>
<input type='hidden' name='task' value='' />
<input type='hidden' name='controller' value='key' />
<input type='hidden' name='view' value='keys' />
<input type='hidden' name='option' value='<?php echo $this->option;?>' />
<input type='hidden' name='boxchecked' value='0' />
<input type="hidden" name="filter_order" value="<?php echo $this->model->getState('filter.order');?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->model->getState('filter.order_dir');?>" />
<?php echo JHTML::_('form.token'); ?>
</form>

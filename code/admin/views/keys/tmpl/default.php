<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die( 'Restricted access' );
?>
<form action='index.php' method='post' name='adminForm'>
<table cellpadding='4' cellspacing='0' border='0' width='100%' class='adminlist'>
	<thead>
		<tr>
			<th width="20">#</th>
			<th width="20">&nbsp;</th>
			<th width="30"><?php echo JHTML::_('grid.sort',   JText::_('COM_API_ID'), 'k.id', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?></th>
			<th><?php echo JHTML::_('grid.sort',   JText::_('COM_API_DOMAIN'), 'k.domain', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?></th>
			</th>
			<th><?php echo JHTML::_('grid.sort',   JText::_('COM_API_USER'), 'u.name', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?></th>
			<th><?php echo JText::_('COM_API_KEY');?></th>
			<th><?php echo JHTML::_('grid.sort',   JText::_('COM_API_PUBLISHED'), 'k.published', $this->model->getState('filter.order_dir'), $this->model->getState('filter.order') ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
	$count 	= count($this->rows);
	for($i=0; $i<$count; $i++) :
		$row = $this->rows[$i];
		$class = $i%2 ? 'row0' : 'row1';
		?>
		<tr class="<?php echo $class;?>">
			<td><?php echo $this->pagination->getRowOffset($i);?></td>
			<td><?php echo $row->checked;?></td>
			<td><?php echo $row->id; ?></td>
			<td>
				<a href="<?php echo $row->admin_link;?>">
					<?php echo $row->domain; ?>
				</a>
			</td>
			<td><?php echo $row->name." (".$row->username.")"; ?></td>
			<td><?php echo $row->hash;?></td>
			<td><?php echo $row->published_html; ?></td>
		</tr>
	<?php endfor; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='12'><?php echo $this->pagination->getListFooter();?></td>
		</tr>
	</tfoot>
</table>
<input type='hidden' name='task' value='' />
<input type='hidden' name='c' value='key' />
<input type='hidden' name='view' value='keys' />
<input type='hidden' name='option' value='<?php echo $this->option;?>' />
<input type='hidden' name='boxchecked' value='0' />
<input type="hidden" name="filter_order" value="<?php echo $this->model->getState('filter.order');?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->model->getState('filter.order_dir');?>" />
<?php echo JHTML::_('form.token'); ?>
</form>
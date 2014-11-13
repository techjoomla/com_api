<?php
/**
 * @version    SVN: <svn_id>
 * @package    Api
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

// No direct access.
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_api/assets/css/api.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_tjfields');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tjfields&task=countries.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'countryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>

<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;

		if (order !== '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<?php
if (! empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<div class="<?php echo COM_APIS_WRAPPER_CLASS; ?> api-keys">
	<form
		action="<?php echo JRoute::_('index.php?option=com_api&view=keys'); ?>"
		method="post" name="adminForm" id="adminForm">

		<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">

		<?php else : ?>
			<div id="j-main-container">
			<?php endif; ?>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" name="filter_search" id="filter_search"
					placeholder="<?php echo JText::_('COM_API_KEYS_SEARCH_FILTER'); ?>"
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
					class="hasTooltip"
					title="<?php echo JText::_('COM_API_KEYS_SEARCH_FILTER'); ?>" />
				</div>

				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip"
					title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip"
					title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
					onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>

				<?php if (JVERSION >= '3.0') : ?>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit" class="element-invisible">
							<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
						</label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>

					<div class="btn-group pull-right hidden-phone hidden-tablet">
						<label for="directionTable" class="element-invisible">
							<?php echo JText::_('JFIELD_ORDERING_DESC'); ?>
						</label>
						<select name="directionTable" id="directionTable"
							class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
							<option value="asc"
								<?php
									if ($listDirn == 'asc')
									{
										echo 'selected="selected"';
									}
								?>>
									<?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?>
							</option>
							<option value="desc"
								<?php
								if ($listDirn == 'desc')
								{
									echo 'selected="selected"';
								}
								?>>
									<?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?>
							</option>
						</select>
					</div>

					<div class="btn-group pull-right hidden-phone hidden-tablet">
						<label for="sortTable" class="element-invisible">
							<?php echo JText::_('JGLOBAL_SORT_BY'); ?>
						</label>
						<select name="sortTable" id="sortTable" class="input-medium"
							onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
							<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
						</select>
					</div>
				<?php endif; ?>
				<div class="btn-group pull-right hidden-phone">
					<?php
					echo JHtml::_('select.genericlist', $this->publish_states, "filter_state", 'class="input-medium" size="1" onchange="document.adminForm.submit();" name="filter_state"', "value", "text", $this->state->get('filter.state'));
					?>
				</div>
			</div>

			<div class="clearfix"> </div>

			<?php if (empty($this->items)) : ?>
				<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo JText::_('COM_API_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else : ?>
				<table class="table table-striped" id="keyList">
					<thead>
						<tr>
							<th width="1%" class="hidden-phone">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<?php if (isset($this->items[0]->state)): ?>
								<th width="1%" class="nowrap center">
									<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_API_KEYS_USERID', 'a.userid', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_API_KEYS_DOMAIN', 'a.domain', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo JHtml::_('grid.sort',  'COM_API_KEYS_HASH', 'a.hash', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_API_KEYS_LAST_USED', 'a.last_used', $listDirn, $listOrder); ?>
							</th>
							<?php if (isset($this->items[0]->id)): ?>
								<th width="1%" class="nowrap center hidden-phone">
									<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->items as $i => $item) :
						$canCreate  = $user->authorise('core.create',     'com_api');
						$canEdit    = $user->authorise('core.edit',       'com_api');
						$canCheckin = $user->authorise('core.manage',     'com_api');
						$canChange  = $user->authorise('core.edit.state', 'com_api');
						?>

						<tr class="row<?php echo $i % 2; ?>">
							<td class="center hidden-phone">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<?php if (isset($this->items[0]->state)): ?>
								<td class="center">
									<?php echo JHtml::_('jgrid.published', $item->state, $i, 'keys.', $canChange, 'cb'); ?>
								</td>
							<?php endif; ?>
							<td>
								<a href="<?php echo 'index.php?option=com_api&task=key.edit&id='.(int) $item->id; ?>"><?php echo $item->userid; ?></a>
							</td>
							<td>
								<?php if (isset($item->checked_out) && $item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'keys.', $canCheckin); ?>
								<?php endif; ?>
								<?php echo $item->domain; ?>
							</td>
							<td><?php echo $item->hash; ?></td>
							<td><?php echo ($item->last_used == '0000-00-00 00:00:00') ? JText::_('JNEVER') : $item->last_used; ?></td>
							<td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php if (JVERSION >= '3.0'): ?>
					<?php echo $this->pagination->getListFooter(); ?>
				<?php else: ?>
					<div class="pager">
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

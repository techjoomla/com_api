<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

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

<div class="<?php echo COM_APIS_WRAPPER_CLASS; ?> api-logs">
	<form
		action="<?php echo JRoute::_('index.php?option=com_api&view=logs'); ?>"
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
					placeholder="<?php echo JText::_('COM_API_LOGS_SEARCH_FILTER'); ?>"
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
					class="hasTooltip"
					title="<?php echo JText::_('COM_API_LOGS_SEARCH_FILTER'); ?>" />
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
			</div>

			<div class="clearfix"> </div>

			<?php if (empty($this->items)) : ?>
				<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo JText::_('COM_API_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else : ?>
			<table class="table table-striped" id="logsList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class='left' width="10%">
							<?php echo JHtml::_('grid.sort',  'COM_API_KEYS_HASH', 'a.hash', $listDirn, $listOrder); ?> /
							<?php echo JHtml::_('grid.sort',  'COM_API_LOGS_USER', 'u.name', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_API_LOGS_IP_ADDRESS', 'a.ip_address', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo JHtml::_('grid.sort',  'COM_API_LOGS_TIME', 'a.time', $listDirn, $listOrder); ?>
						</th>
						<th class='left' width="15%"><?php echo JText::_('COM_API_LOGS_REQUEST'); ?></th>
						<th class='left' width="25%"><?php echo JText::_('COM_API_LOGS_POST_DATA'); ?></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center hidden-phone">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td>
								<a href="index.php?option=com_api&view=logs&filter_search=<?php echo $item->hash; ?>"><?php echo $item->hash; ?></a>
								<?php if ($item->name) : ?>
								<br />
								<a href="index.php?option=com_api&view=logs&filter_search=uid:<?php echo $item->uid; ?>"><?php echo $item->name; ?></a></td>
								<?php else : echo JText::_('UNASSIGNED_HASH'); ?>
								<?php endif; ?>
							<td>
								<a href="index.php?option=com_api&view=logs&filter_search=ip:<?php echo $item->ip_address; ?>"><?php echo $item->ip_address; ?></a>
							</td>
							<td><?php echo $item->time; ?></td>
							<td><div class="request_container"><?php echo implode('&#8203;&', explode('&', $item->request)); ?></div></td>
							<td><?php echo $item->post_data; ?></td>
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


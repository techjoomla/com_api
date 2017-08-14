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

if (version_compare(JVERSION, "3.0.0", "ge"))
{
	JHtml::_('behavior.formvalidation');
}

JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_api/assets/css/api.css');
?>

<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function()
	{

	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'key.cancel')
		{
			Joomla.submitform(task, document.getElementById('key-form'));
		}
		else
		{
			if (task != 'key.cancel' && document.formvalidator.isValid(document.id('key-form')))
			{
				Joomla.submitform(task, document.getElementById('key-form'));
			}
			else
			{
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<div class="<?php echo COM_APIS_WRAPPER_CLASS; ?> api-key">
	<form action="<?php echo JRoute::_('index.php?option=com_api&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="key-form" class="form-validate">
		<div class="form-horizontal">
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<fieldset class="adminform">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('userid'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('userid'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('hash'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('hash'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('domain'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('domain'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('per_hour'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('per_hour'); ?></div>
						</div>

						<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
						<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
						<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

						<?php
						if(empty($this->item->created_by))
						{
							?>
							<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />
							<?php
						}
						else
						{
							?>
							<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
							<?php
						}
						?>
						<input type="hidden" name="jform[last_used]" value="<?php echo $this->item->last_used; ?>" />
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

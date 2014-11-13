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

JFactory::getDocument()->addScriptDeclaration("
	function submitbutton(pressbutton) {
		if (pressbutton == 'save') {
			var domain = document.adminForm.domain.value;
			var regex_sanitize = /(http|https|ftp):\/\//i
			var sanitized = domain.replace(regex_sanitize, '');
			var regex_validate = /^([0-9a-z-_\.]+\.+[0-9a-z\.])+|localhost$/i;
			if (regex_validate.test(sanitized) == false) {
				alert('".JText::_("COM_API_INVALID_DOMAIN_MSG")."');
				return false;
			}
		}
		submitform(pressbutton);
	}
");

?>

<h1 class="componentheading"><?php echo JText::_('COM_API_COMPONENT_HEADING');?></h1>
<h2 class="contentheading"><?php echo $this->key->id ? JText::_('COM_API_EDIT_KEY_PAGE_TITLE') : JText::_('COM_API_NEW_KEY_PAGE_TITLE');?></h2>
<form action="index.php" method="post" name="adminForm" class="api_key_form">
	<p>
		<label class="api_form_label" for="domain"><?php echo JText::_('COM_API_DOMAIN');?>:</label>
		<input type="text" class="inputbox api_form_input" name="domain" size="55" value="<?php echo $this->key->domain;?>" />
		<?php echo JHTML::tooltip(JText::_('COM_API_DOMAIN_TOOLTIP'), JText::_('COM_API_DOMAIN')); ?>
	</p>
	<?php if ($this->key->hash) : ?>
		<p>
			<label class="api_form_label"><?php echo JText::_('COM_API_KEY');?>:</label>
			<span class="api_form_key"><?php echo $this->key->hash;?></span>
		</p>
	<?php endif; ?>
	<p>
		<input type="submit" name="submit" value="Submit" onclick="return submitbutton('save');" />
		<input type="submit" name="cancel" value="Cancel" onclick="return submitbutton('cancel');" />
	</p>
	<input type="hidden" name="option" id="option" value="com_api" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="id" id="id" value="<?php echo $this->key->id;?>" />
	<input type="hidden" name="c" id="c" value="keys" />
	<input type="hidden" name="return" value="<?php echo $this->return;?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>

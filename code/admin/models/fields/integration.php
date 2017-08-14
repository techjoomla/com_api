<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.pane');
jimport('joomla.application.component.helper');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');

/**
 * Class for custom Integration element
 *
 * @since  1.0.0
 */
class JFormFieldIntegration extends JFormField
{
	/**
	 * Function to genarate html of custom element
	 *
	 * @return  HTML
	 *
	 * @since  1.0.0
	 */
	public function getInput()
	{
		return $this->fetchElement($this->name, $this->value, $this->element, $this->options['controls']);
	}

	/**
	 * Function to genarate html of custom element
	 *
	 * @param   STRING  $name          Name of the element
	 * @param   STRING  $value         Default value of the element
	 * @param   STRING  $node          asa
	 * @param   STRING  $control_name  asda
	 *
	 * @return  HTML
	 *
	 * @since  1.0.0
	 */
	public function fetchElement($name, $value, $node, $control_name)
	{
		$communitymainfile = JPATH_SITE . '/components/com_community/libraries/core.php';
		$esfolder = JPATH_SITE . '/components/com_easysocial';

		$jsString =	"<script>
					function checkIfExtInstalled(selectBoxName, extention)
					{
						jQuery('#grpCategoriesField').empty();
						jQuery('#grpCategoriesField').html('<div class=\'alert alert warning \'>" . JText::_('NO_SOCIAL_GROUPS_CAT_FOUND') . "</div>');

						var flag = 0;
						if (extention == 'jomsocial')
						{
							";

								if (!JFile::exists($communitymainfile))
								{
									$jsString .= " flag = 1";
								}

							$jsString .= "
						}
						else if (extention == 'easysocial')
						{
							";

								if (!JFolder::exists($esfolder))
								{
									$jsString .= " flag = 1";
								}

							$jsString .= "
						}

						if (flag == 1)
						{
								var extentionName = jQuery('#jformsocial_integration').val();
								alert(extentionName+' not installed');
								jQuery('#jformsocial_integration').val('joomla');
								jQuery('select').trigger('liszt:updated');
						}
					}

				</script>";
		echo   $jsString;

		$options[] = JHTML::_('select.option', 'joomla', JText::_('COM_API_JOOMLA'));
		$options[] = JHTML::_('select.option', 'jomsocial', JText::_('COM_API_JOMSOCIAL'));
		$options[] = JHTML::_('select.option', 'easysocial', JText::_('COM_API_EASYSOCIAL'));

		$fieldName = $name;

		return JHtml::_('select.genericlist',
												$options, $fieldName,
						'class="inputbox tjlmsintegration btn-group" onchange="checkIfExtInstalled(this.name, this.value)" ',
						'value', 'text', $value, $control_name . $name
						);
	}
}

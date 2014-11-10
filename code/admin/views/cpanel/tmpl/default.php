<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/
defined('_JEXEC') or die;

$template = JFactory::getApplication()->getTemplate(true)->template;
?>
<div id="cpanel">
<?php
for ($i=0; $i<count($this->views); $i++) : 
	$view = $this->views[$i];

	if ( 'cpanel' == $view['view'] ) {
		continue;
	}

	$link = 'index.php?option='.$this->option.'&view='.$view['view'];
	$count	= isset($this->modified[$view['view']]) ? $this->modified[$view['view']] : '';
	?>
	<div style="float:left;">
		<div class="icon">
			<a href="<?php echo $link;?>">
				<img src='templates/<?php echo $template; ?>/images/header/icon-48-generic.png' alt='<?php echo $view['name'];?>' />
				<span><?php echo $view['name'];?></span>
				<?php if ($count) : ?>
					<span class="modified_count"><?php echo $count; ?></span>
				<?php endif; ?>
			</a>
		</div>
	</div>
<?php endfor; ?>
</div>

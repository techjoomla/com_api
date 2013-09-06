<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
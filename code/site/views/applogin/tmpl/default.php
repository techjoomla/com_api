<?php
/**
 * @package    API
 * @copyright  Copyright (C) 2009-2020 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API)
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;

$base_url = Uri::base();
$formToken = Session::getFormToken();
$url = base64_encode($base_url.'tjlogin-registration');

?>

<script type="text/javascript">

	window.addEventListener('load', function () {
		parent.postMessage(
			'<?php echo json_encode($this->keyObj); ?>',
			'*'
		);
	});
	window.addEventListener("message",function(e) {
		let msgData = JSON.parse(e.data);
		if(msgData.message=="user_registered_in_app")
		{
			window.location = "<?php echo $base_url;?>?option=com_users&task=user.logout&<?php echo $formToken;?>=1&return=<?php echo $url;?>";
			//window.location ="<?php //echo $base_url;?>/tjlogin-registration";
		}
	}, false);

</script>

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

use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class for a Applogin Controller
 *
 * @since  2.5.2
 */
class ApiControllerApplogin extends ApiController
{
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \InputFilter::clean()}.
	 *
	 * @return  \BaseController  A \BaseController object to support chaining.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		parent::display();
	}
}

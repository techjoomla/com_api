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

jimport('joomla.plugin.plugin');

class SocialadsApiResourceSummary extends ApiResource
{
	public function get()
	{   
	    
		//$this->plugin->setResponse( ( $result) );*/		
		
	}

	public function post()
	{
		//query to get daily, weekly, mohthly & yearly sales
		$db = JFactory::getDBO();
		
		require_once(dirname(__FILE__).DS.'helper.php');
		$current_date = JRequest::getVar('current_date');
						
		//for log record
		jimport('joomla.error.log');
		$options = array(
    			'format' => "{DATE}\t{TIME}\t{USER_ID}\t{COMMENT}\t{CDATE}\t{}"
						);

		$log = &JLog::getInstance('com_api.log.php');
		$log->setOptions($options);
		$user = &JFactory::getUser();
		$userId = $user->get('id');
		$log->addEntry(array('user_id' => $userId, 'comment' => 'This is the comment','cdate' => $current_date));
		

		//set offset & date for server
		$config =& JFactory::getConfig();
        $offset = $config->getValue('config.offset');	
  		$offset = '';
  		$current_date= & JFactory::getDate($current_date,$offset);
  		$current_date = $current_date->toFormat('%F %T');
		
		//daily data
   		$day_start = date_create($current_date)->format('Y-m-d');       
   		$day_start .= ' 00:00:00';
   			
   		$daily = Sale_Data::total($day_start,$current_date);
		$daily = Sale_Data::compress($daily);		
		if(!$daily){ $daily = 0;}
		
			
		list($year,$month, $day) = split('[/.-]',$current_date );		
		
		//weekly data
		$db = JFactory::getDBO();
		$query ="SELECT WEEKDAY('".$current_date."')"; 
		$db->setQuery( $query );
		$wstart = $db->loadResult();
		$wday = $day - $wstart;
		$wdate = $year."-".$month."-".$wday; 
		$weekly = Sale_Data::total($wdate,$current_date);
		$weekly = Sale_Data::compress($weekly);	
		
		if(!$weekly){ $weekly = 0;}		
		
		// monthly data
		               
		$mdate = $year."-".$month."-01 00:00:00";
		$monthly = Sale_Data::total($mdate,$current_date);
		$monthly = Sale_Data::compress($monthly);
		
		if(!$monthly){ $monthly = 0;}	
		
		//quarterly data	
				
		$query = "SELECT QUARTER('".$current_date."')";
		$db->setQuery( $query );
		$res=$db->loadResult();
				
		$query = "SELECT YEAR('".$current_date."')";
		$db->setQuery( $query );
		$year=$db->loadResult();
		
		switch($res)
		{
		  case 1:$qrt=$year."-01-01";   break;
		  case 2:$qrt=$year."-04-01";   break;
		  case 3:$qrt=$year."-07-01";   break;
		  case 4:$qrt=$year."-10-01";   break;
		}
		
		$quarterly = Sale_Data::total($qrt,$current_date);
		$quarterly = Sale_Data::compress($quarterly);
		if(!$quarterly){$quarterly=0;}
		
		// calender Yearly data
		
		$ydate = $year."-01-01 00:00:00";
		$yearly = Sale_Data::total($ydate,$current_date);
		$yearly = Sale_Data::compress($yearly);
		if(!$yearly){ $year = 0;}
		
		// financial Yearly data
		 
		$fdate = $year."-4-1 00:00:00";            
		
		$fyearly = Sale_Data::total($fdate,$current_date);
		$fyearly = Sale_Data::compress($fyearly);
		if(!$fyearly){ $fyear = 0;}
		
		// Currency
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$currency=$socialads_config['currency'];
         
		
		switch($currency)
		{
			case "INR" :	   $currency = "₹";break;	
			case "USD" :   	   $currency = "$";break;
			case "GBP"||"UKP": $currency = "£";break;  
			case "JPY" :	   $currency = "¥";break;
			case "ITL" :	   $currency = "£";break;
			case "EUR" :       $currency = "€";break;
			case "CNY" :       $currency = "¥";break;
			case "ZAR" :	   $currency = "R";break;
		    case "AUD" : 	   $currency = "$";break;	
		}
		
		//send result
		
		$result = array();
		$result['summary'] = array("daily"=>$daily, "weekly"=>$weekly, "monthly"=>$monthly,"quarterly"=>$quarterly, "financial_yearly"=>$fyearly,"calender_yearly"=>$yearly);
		$result['attribs'] = array("currency"=>$currency);
				
		$this->plugin->setResponse( ( $result) );		 
	
		}
	
}

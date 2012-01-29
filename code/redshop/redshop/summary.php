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

class RedshopApiResourceSummary extends ApiResource
{
	public function get()
	{
		//set offset & date for server
		$config =& JFactory::getConfig();
        $offset = $config->getValue('config.offset');
        $dt=date($_SERVER['REQUEST_TIME']);
        $odate= & JFactory::getDate($dt,$offset);
        $date = $odate->toFormat('%F %T');
             	
		// Write query to get daily, weekly, mohthly & yearly sales
		$db = JFactory::getDBO();
		
		//Daily data BETWEEN(CONVERT(int(10),DATE(time()),20) AND time()) 
		$query = "SELECT SUM(order_total)
		               FROM #__redshop_orders 
		               WHERE DATE(FROM_UNIXTIME(mdate))=$date
		               AND order_status='s' AND order_payment_status='Paid'";
		               	
		$db->setQuery( $query );
		$daily=$db->loadResult();
		if(!$daily){$daily=0;}
		$this->compress($daily);
					
		//weekly data BETWEEN((time()-((date_format(DATE(time()),'%w')*24*60*60)+time()-DATE(time()))) AND time())
		//DATE(DATE_SUB(CURDATE(),INTERVAL (DATE_FORMAT(CURDATE(),'%w')-1) DAY))
		$query = "SELECT SUM(order_total) 
		               FROM #__redshop_orders AS 
		               WHERE DATE(FROM_UNIXTIME(mdate)) 
		               BETWEEN DATE(DATE_SUB('".$date."', INTERVAL WEEKDAY('".$date."') day)) AND '".$date."' 
		               AND order_status='s' AND order_payment_status='Paid'"; 
		/*  SELECT SUM(a.product_final_price) 
		               FROM #__redshop_order_item AS a 
		               WHERE DATE(FROM_UNIXTIME(a.mdate)) 
		               BETWEEN DATE(date_sub(CURDATE(), interval WEEKDAY(CURDATE()) day)) AND CURDATE() 
		               AND a.order_id IN (SELECT order_id FROM #__redshop_orders 
		               WHERE order_status='s' AND order_payment_status='Paid')    */        
		               	
		$db->setQuery( $query );
		$weekly=$db->loadResult();
		if(!$weekly){$weekly=0;}
		$this->compress($weekly);
		
		//monthly data BETWEEN((time()-(((date_format(DATE(time()),'%e')-1)24*60*60)+time()-DATE(time()))) AND time())
		$query = "SELECT SUM(order_total) As sale
		               FROM #__redshop_orders 
		               WHERE DATE(FROM_UNIXTIME(mdate)) 
		               BETWEEN DATE(DATE_SUB('".$date."',INTERVAL (DATE_FORMAT('".$date."','%e')-1) DAY)) AND '".$date."' 
		               AND order_status='s' AND order_payment_status='Paid'";  
		               	
		$db->setQuery( $query );
		$monthly=$db->loadResult();
		if(!$monthly){$monthly=0;}
		$this->compress($monthly);
		
		//quarterly sale
		$query = "SELECT QUARTER('".$date."' ) FROM #__redshop_order_item";
		$db->setQuery( $query );
		$res=$db->loadResult();
				
		$query = "SELECT YEAR('".$date."' ) FROM #__redshop_order_item";
		$db->setQuery( $query );
		$year=$db->loadResult();
		
		if($res==1 OR $res==2)
		{
		if($res==1)
		$qstart=$year."-01-01";
		else 
		$qstart=$year."-04-01";
		}
		else
		{
		if($res==3)
		$qstart=$year."-07-01";
		else 
		$qstart=$year."-10-01";
		}
		
		$query = "SELECT SUM(order_total) As sale
		               FROM #__redshop_orders 
		               WHERE DATE(FROM_UNIXTIME(mdate)) 
		               BETWEEN '".$qstart."' AND '".$date."' 
		               AND order_status='s' AND order_payment_status='Paid'";  
		               
		$db->setQuery( $query );
		$quarterly=$db->loadResult();
		if(!$quarterly){$quarterly=0;}
		$this->compress($quarterly);
		
		//yearly data BETWEEN((time()-(((date_format(DATE(time()),'%j')-1)24*60*60)+time()-DATE(time()))) AND time())
		$query = "SELECT SUM(order_total) As sale
		               FROM #__redshop_orders 
		               WHERE DATE(FROM_UNIXTIME(mdate)) 
		               BETWEEN DATE(DATE_SUB('".$date."',INTERVAL (DATE_FORMAT('".$date."','%j')-1) DAY)) AND '".$date."' 
		               AND order_status='s' AND order_payment_status='Paid'";  
		               
		$db->setQuery( $query );
		$yearly=$db->loadResult();
		if(!$yearly){$yearly=0;}
		$this->compress($yearly);
		
		// financial Yearly data
		 
		$fdate = DATE("Y")."-4-1 00:00:00";            
		  
 	    $query = "SELECT SUM(order_total) As sale 
		       		FROM #__redshop_orders 
				    WHERE DATE_SUB('".$date."',INTERVAL (DATEDIFF('".$date."','".$fdate."'))DAY)
		            <= DATE(FROM_UNIXTIME(mdate)) AND order_status='s' AND order_payment_status='Paid'";
		            
		$db->setQuery( $query );
		$fyearly=$db->loadResult();
		if(!$fyearly){ $fyearly = 0;}
		$this->compress($fyearly);
		
		//Currency
		$query="SELECT order_item_currency FROM #__redshop_order_item";              	
		$db->setQuery( $query );
		$currency=$db->loadResult();
		
		//summary
		 $result = array();	

        $result['summary'] = array("daily"=>$daily,"weekly"=>$weekly,"monthly"=>$monthly,"quarterly"=> $quarterly,
        						   "calender_yearly"=> $yearly,"financial_yearly"=>$fyearly);
        $result['attribs'] = array("currency"=>$currency);
         
		$this->plugin->setResponse( $result );
		
		//$this->plugin->setResponse( 'Guru Katre' );
	}

	public function post()
	{
		//query to get daily, weekly, mohthly & yearly sales
		$db = JFactory::getDBO();
		
		require_once(dirname(__FILE__).DS.'helper.php');
		$current_date = JRequest::getVar('current_date');
							
		//for log record
		/*jimport('joomla.error.log');
		$options = array(
    			'format' => "{DATE}\t{TIME}\t{USER_ID}\t{COMMENT}\t{CDATE}\t{}"
						);

		$log = &JLog::getInstance('com_api.log.php');
		$log->setOptions($options);
		$user = &JFactory::getUser();
		$userId = $user->get('id');
		$log->addEntry(array('user_id' => $userId, 'comment' => 'This is the comment','cdate' => $current_date));
		*/

		//set offset & date for server
		$config =& JFactory::getConfig();
        $offset = $config->getValue('config.offset');	
  		//$offset = '';
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
		//Currency
		$query="SELECT order_item_currency FROM #__redshop_order_item";              	
		$db->setQuery( $query );
		$currency=$db->loadResult();
		
		//send result
		
		$result = array();
		$result['summary'] = array("daily"=>$daily, "weekly"=>$weekly, "monthly"=>$monthly,"quarterly"=>$quarterly, "financial_yearly"=>$fyearly,"calender_yearly"=>$yearly);
		$result['attribs'] = array("currency"=>$currency);
				
		$this->plugin->setResponse( ( $result) );	
	}

	public function put()
	{	
		$app = JFactory::getApplication();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$context = 'com_content.edit.article';

		// Fake parameters
		$values	= (array) $app->getUserState($context.'.id');
		array_push($values, (int) $data['id']);
		$values = array_unique($values);
		$app->setUserState($context.'.id', $values);
		if ( !JRequest::getInt( 'id' ) ) {
			$_POST['id'] = $data['id'];
			$_REQUEST['id'] = $data['id'];
		}

		// Simply call post as Joomla will just save an article with an id
		$this->post();

		$response = $this->plugin->get( 'response' );
		if ( isset( $response->success ) && $response->success ) {
			JResponse::setHeader( 'status', 200, true );
			$response->code = 200;
			$this->plugin->setResponse( $response );
		}
	}
	
}

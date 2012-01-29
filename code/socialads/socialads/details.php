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

//include(JURI::base());
jimport('joomla.plugin.plugin');

class SocialadsApiResourceDetails extends ApiResource
{
	public function get()
	{		
		
	}

	public function post()
	{    	
			$db = JFactory::getDBO();
	   		
	   		require_once(dirname(__FILE__).DS.'helper.php');
				
			//get date from app
			$startdate = JRequest::getVar('startdate');
			$enddate = JRequest::getVar('enddate');
			
			$startdate .= ' 00:00:00';
			$enddate .= ' 23:59:59';
			
			$sdate = $startdate;
			$edate = $enddate;
			
			//get offset value					
			$config =& JFactory::getConfig();
            $offset = $config->getValue('config.offset');
            
            $startdate= & JFactory::getDate($startdate,$offset);
            $enddate= & JFactory::getDate($enddate,$offset);
            $startdate = $startdate->toFormat('%F %T');                                
            $enddate = $enddate->toFormat('%F %T');
 										
			//query for product details
			
						
		  $query = "SELECT a.ad_id,a.ad_title AS product_name,SUM(b.ad_amount)AS product_sales 
		  				FROM #__ad_data AS a,#__ad_payment_info AS b WHERE a.ad_id 
					 	IN (SELECT c.ad_id FROM #__ad_payment_info AS c
		             	WHERE status='1' AND b.mdate 
		             	BETWEEN '".$startdate."'AND '".$enddate."')
		             	AND a.ad_id = b.ad_id
		             	GROUP BY a.ad_title ORDER BY product_sales DESC";
		    
		  	$db->setQuery( $query );  
			$details['data'] =array($db->loadObjectList());
						
			$i = 0;
	    	while(count($details['data'][0])>=$i)
	    	{	
	    	  $value = $details['data'][0][$i]->product_sales;
	    	  $value = number_format($value, 2, '.', '');
	    	  	
			  if($details['data'][0][$i]->product_sales > 9999)
			  {
			  $value = $details['data'][0][$i]->product_sales/1000;
			  $value = number_format($value, 2, '.', '');
			  $value .='K';
			  $details['data'][0][$i]->product_sales = $value;
			 
			  } 
			  
			  $i++;
			}
	        
		    $total = Sale_Data::total($startdate,$enddate); 
		    $projected_sale = Sale_Data::projected_sale($sdate,$edate,$total);
		    $total = Sale_Data::compress($total);
		    $projected_sale = Sale_Data::compress($projected_sale);
		    		    		   		    	
			$details['total'] =array("total"=>$total);	
		    $details['projected_sale'] =array("projected_sale"=>$projected_sale);  	
      					 
		    $this->plugin->setResponse( $details ); 										
		
			//$this->plugin->setResponse( 'This is a post request.' );
	}

	
}

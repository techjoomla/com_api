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

class TiendaApiResourceDetails extends ApiResource
{
	public function get()
	{
			$startdate	= JRequest::getVar('startdate');
        	$startdate .= '00:00:00';
			$enddate	= JRequest::getVar('enddate');
        	$enddate .= ' 23:59:59';
			$config =& JFactory::getConfig();
            $offset = $config->getValue('config.offset');
            $startdate= & JFactory::getDate($startdate,$offset);
            $enddate= & JFactory::getDate($enddate,$offset);
           	$startdate = $startdate->toFormat('%F %T');  
            $enddate = $enddate->toFormat('%F %T');
            
		$db = JFactory::getDBO();
		
		$query = "SELECT a.orderitem_sku AS product_sku,a.orderitem_name AS product_name,SUM(a.orderitem_quantity)AS 						   quantity,SUM(a.orderitem_final_price)AS product_sales
					   FROM #__tienda_orderitems AS a WHERE a.order_id IN(
					   SELECT c.order_id
		               FROM #__tienda_orders AS c
		               WHERE DATE(c.modified_date)
		               BETWEEN '$startdate'  AND '$enddate'
		               AND order_state_id='5')
		               GROUP BY a.orderitem_sku ORDER BY product_sales DESC";
		               
		               /*"SELECT a.orderitem_sku AS product_sku,a.orderitem_name AS product_name,
		               SUM(a.orderitem_quantity)AS quantity,
					   SUM(a.orderitem_final_price)AS product_sales 
					   FROM #__tienda_orderitems AS a WHERE a.order_id IN(
					   SELECT c.order_id
		               FROM #__tienda_orders AS c
		               WHERE DATE(c.modified_date)
		               BETWEEN '$startdate'  AND '$enddate'
		               AND c.order_id IN (SELECT order_id FROM #__tienda_orders 
		               WHERE order_state_id='5'))
		               GROUP BY a.orderitem_sku ORDER BY product_sales DESC";*/
		               	
		$db->setQuery( $query );
		$details['data'] =array($db->loadObjectList());
		
		//$daily=json_encode($daily);
		
		$query ="SELECT SUM(order_total)
		               FROM #__tienda_orders 
		               WHERE DATE(modified_date) BETWEEN '".$startdate."'AND '".$enddate."'
		               AND order_state_id='5'";
		               
		$db->setQuery( $query ); 
		$total =(int)$db->loadResult();
		
		if($total>9999)
	   			{
	     			$total = $total/1000;
	     			$total .='K'; 
	     		}
	     	elseif(!$total)	 
				{ $total=0;}
		$details['total'] =array("total"=>$total);
		$this->plugin->setResponse( $details );

		
						
	/*	//weekly data
		
		case "weekly":$query = "SELECT a.orderitem_sku,a.orderitem_name,SUM(a.orderitem_quantity*a.orderitem_final_price)AS sale 
					   FROM #__tienda_orderitems AS a, WHERE a.order_id IN(
					   SELECT c.order_id
		               FROM #__tienda_orders AS c
		               WHERE DATE(c.modified_date)
		               BETWEEN DATE(DATE_SUB(CURDATE(),INTERVAL DATE_FORMAT(CURDATE(),'%w') DAY)) AND CURDATE() 
		               AND c.order_id IN (SELECT order_id FROM #__tienda_orders 
		               WHERE order_state_id='5'))
		               GROUP BY a.orderitem_sku ";
		                
		                 
		               	
		$db->setQuery( $query );
		$weekly['data'] =array($db->loadObjectList());
		//$weekly=($weekly = $db->loadObjectList())?$weekly:array();
		$weekly=json_encode($weekly);
		$this->plugin->setResponse( $weekly );
		break;
		
		//monthly data
		case "monthly":$query = "SELECT a.orderitem_sku,a.orderitem_name,SUM(a.orderitem_quantity*a.orderitem_final_price)AS sale 
					   FROM #__tienda_orderitems AS a, WHERE a.order_id IN(
					   SELECT c.order_id
		               FROM #__tienda_orders AS c
		               WHERE DATE(c.modified_date)
		               BETWEEN DATE(DATE_SUB(CURDATE(),INTERVAL (DATE_FORMAT(CURDATE(),'%e')-1) DAY)) AND CURDATE()
		               AND c.order_id IN (SELECT order_id FROM #__tienda_orders 
		               WHERE order_state_id='5'))
		               GROUP BY a.orderitem_sku ";
		               	
		$db->setQuery( $query );
		$monthly['data'] =array($db->loadObjectList());
		//$monthly=($monthly = $db->loadObjectList())?$monthly:array();
		$monthly=json_encode($monthly);
		$this->plugin->setResponse( $monthly );
		break;
		
		//yearly data
		case "yearly":$query = "SELECT a.orderitem_sku,a.orderitem_name,SUM(a.orderitem_quantity*a.orderitem_final_price)AS sale 
					   FROM #__tienda_orderitems AS a WHERE a.order_id IN(
					   SELECT c.order_id
		               FROM #__tienda_orders AS c
		               WHERE DATE(c.modified_date)
		               BETWEEN DATE(DATE_SUB(CURDATE(),INTERVAL (DATE_FORMAT(CURDATE(),'%j')-1) DAY)) AND CURDATE()
		               AND c.order_id IN (SELECT order_id FROM #__tienda_orders 
		               WHERE order_state_id='5'))
		               GROUP BY a.orderitem_sku "; 
		                
		               	               
		$db->setQuery( $query );
		$yearly['data'] =array($db->loadObjectList());
		//$yearly=($yearly = $db->loadObjectList())?$yearly:array();      
        $yearly=json_encode($yearly);
		$this->plugin->setResponse( $yearly );
		}*/
		
		//$this->plugin->setResponse( 'Guru Katre' );
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
		$query = "SELECT a.orderitem_sku AS product_sku,a.orderitem_name AS product_name,SUM(a.orderitem_quantity)AS 						   quantity,SUM(a.orderitem_final_price)AS product_sales
					   FROM #__tienda_orderitems AS a WHERE a.order_id IN(
					   SELECT c.order_id
		               FROM #__tienda_orders AS c
		               WHERE DATE(c.modified_date)
		               BETWEEN '$startdate'  AND '$enddate'
		               AND order_state_id='5')
		               GROUP BY a.orderitem_sku ORDER BY product_sales DESC";
		               
		                        	
		$db->setQuery( $query );
		$details['data'] =array($db->loadObjectList());
		$i = 0;
                   while(count($details['data'][0])>=$i)
                   {                        
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
	}

	public function delete( $id = null )
	{
		// Include dependencies
		jimport('joomla.application.component.controller');
		jimport('joomla.form.form');
		jimport('joomla.database.table');

		require_once JPATH_ADMINISTRATOR . '/components/com_content/controllers/articles.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_content/models/article.php';
		JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_content/models/forms/' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_content/tables/' );

		// Fake parameters
		$_POST['task'] = 'trash';
		$_REQUEST['task'] = 'trash';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);
		$controller = new ContentControllerArticles();
		try {
			$controller->execute('trash');
		} catch ( JException $e ) {
			$success = false;
			$controller->set('messageType', 'error');
			$controller->set('message', $e->getMessage() );
		}

		if ( $controller->getError() ) {
			$response = $this->getErrorResponse( 400, $controller->getError() );
		} elseif ( 'error' == $controller->get('messageType') ) {
			$response = $this->getErrorResponse( 400, $controller->get('message') );
		} else {
			$response = $this->getSuccessResponse( 200, $controller->get('message') );
		}

		$this->plugin->setResponse( $response );
	}
}

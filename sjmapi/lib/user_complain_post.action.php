<?php
class user_complain_post{
	public function index()
	{
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$root = array();
		
		$root['page_title'] = '提交投诉';
		
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		
		$root['return'] = 1;		
		if($user_id>0)
		{
			$root['user_login_status'] = 1;	
			$order_id = intval($GLOBALS['request']['order_id']);
                        
                        $sql = "SELECT 
                                d.`sub_name` AS deal_name,
                                d.`service_time`,
                                d.`current_price` AS deal_price,
                                d.`id` AS deal_id,
                                d.`icon`,
                                o.`id` AS order_id,
                                o.`total_price`,
                                o.`create_time`,
                                o.`is_get_bonus`,
                                o.`technician_id`,
                                doi.`number` 
                              FROM
                                fanwe_deal_order o 
                                LEFT JOIN fanwe_deal_order_item doi 
                                  ON o.`id` = doi.`order_id` 
                                LEFT JOIN fanwe_deal d 
                                  ON doi.`deal_id` = d.`id` 
                                  WHERE o.`id` = ".$order_id;
                        
                        $order = $GLOBALS['db']->getRow($sql);
                        $order['user_id'] = $user_id;
                        $root['order']=$order;
		}
		else
		{
			$root['user_login_status'] = 0;		
		}		
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
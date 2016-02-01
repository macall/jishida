<?php
class verify_delivery{
	public function index()
	{
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;
	
		if($user_id>0)
		{
			$id = intval($GLOBALS['request']['id']);
			$user_id = intval($user_id);
			require_once APP_ROOT_PATH."system/model/deal_order.php";
			$order_table_name = DB_PREFIX."deal_order";
			
			$delivery_notice = $GLOBALS['db']->getRow("select n.* from ".DB_PREFIX."delivery_notice as n left join ".$order_table_name." as o on n.order_id = o.id where n.order_item_id = ".$id." and o.user_id = ".$user_id." and is_arrival = 0 order by delivery_time desc");
			if($delivery_notice)
			{
				require_once APP_ROOT_PATH."system/model/deal_order.php";
				$res = confirm_delivery($delivery_notice['notice_sn'],$id);
				if($res)
				{
					$data['status'] = true;
					$data['info'] = "确认成功";
					output($data);
				}
				else
				{
					$data['status'] = 0;
					$data['info'] = "确认失败";
					output($data);
				}
			}
			else
			{
				$data['status'] = 0;
				$data['info'] = "订单未发货";
				output($data);
			}
		}
	}
}
?>
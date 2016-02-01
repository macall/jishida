<?php
class start_service{
	public function index(){
		$id=intval($GLOBALS['request']['id']);		
		if($id == ''){
			$root['status'] = 0;
			$root['info'] = '订单号不能为空';
		}
		
		/*设置发货开始*/
		$silent = intval($_REQUEST['silent']);
		$order_id = $id;
		$order_items = $GLOBALS['db']->getAll("select id from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
		
		foreach($order_items as $k=>$v){
			$order_deals[]=$v['id'];
		}
		$delivery_sn = $_REQUEST['delivery_sn'];
		$express_id = intval($_REQUEST['express_id']);
		$memo = $_REQUEST['memo'];
		
		if(!$order_deals)
		{
			
		}
		else
		{
			$deal_names = array();
			
			foreach($order_deals as $order_deal_id)
			{
				$deal_info =$GLOBALS['db']->getRow("select d.*,doi.id as doiid from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_order_item as doi on doi.deal_id = d.id where doi.id = ".$order_deal_id);
				$deal_name = $deal_info['sub_name'];
				array_push($deal_names,$deal_name);
				$rs = make_delivery_notice($order_id,$order_deal_id,$delivery_sn,$memo,$express_id);
				
				if($rs)
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1,is_arrival = 0 where id = ".$order_deal_id);
					update_balance($order_deal_id,$deal_info['id']);
				}
				
			}
			
			$deal_names = implode(",",$deal_names);
			
			//开始同步订单的发货状态
			$order_deal_items = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
			foreach($order_deal_items as $k=>$v)
			{
				if($GLOBALS['db']->getOne("select is_delivery from ".DB_PREFIX."Deal where id = ".$v['deal_id'])==0) //无需发货的商品
				{
					unset($order_deal_items[$k]);
				}		
			}
			
			
			$delivery_deal_items = $order_deal_items;
			foreach($delivery_deal_items as $k=>$v)
			{
				if($v['delivery_status']==0) //未发货去除
				{
					unset($delivery_deal_items[$k]);
				}				 
			}

			if(count($delivery_deal_items)==0&&count($order_deal_items)!=0)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 0 where id = ".$order_id); //未发货
			}
			elseif(count($delivery_deal_items)>0&&count($order_deal_items)!=0&&count($delivery_deal_items)<count($order_deal_items))
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 1 where id = ".$order_id); //部分发
			}
			else
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2 where id = ".$order_id); //全部发
			}		
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set update_time = ".time().",is_refuse_delivery=0 where id = ".$order_id); //全部发
			
			$refund_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item where (refund_status = 1 or is_arrival = 2) and order_id = ".$order_id);
			$coupon_item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where refund_status = 1 and order_id = ".$order_id);
			if($refund_item_count==0&&$coupon_item_count==0)
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set refund_status = 0,is_refuse_delivery=0 where id = ".$order_id);
			//查询快递名
			$express_name =$GLOBALS['db']->getAll("select name from ".DB_PREFIX."Express where id = ".$express_id);
			require_once APP_ROOT_PATH."system/model/deal_order.php";
			order_log("发货成功".$express_name.$delivery_sn.$_REQUEST['memo'],$order_id);	
			update_order_cache($order_id);
			distribute_order($order_id);
			
		}
		/*设置发货结束*/
		
		
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",array("service_start_time"=>time()),"UPDATE","id=".intval($id)); // 更新服务时间为当前时间
		if($result){
			$root['status'] = 1;
			$root['info'] = '服务开始';
		}
		output($root);
	}
}
?>

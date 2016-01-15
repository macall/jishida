<?php
class auto_order{
	public function index()
	{//file_put_contents("../auto_order.txt",print_r($GLOBALS['request']));
		$root = array();
		$root['return'] = 1;
		$root['order_id'] = 0;
		
		//localhost/o2o/sjmapi/index.php?act=auto_order&location_id=21&money=10&email=fanwe&pwd=123456&r_type=2
		//门店ID
		$supplier_location_id = intval($GLOBALS['request']['location_id']);
		//下单金额
		$money = floatval($GLOBALS['request']['money']);
		
		if ($money <= 0){
			$root['info'] = '不是有效的金额';
			output($root);
			exit;
		}
		
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		if ($user_id > 0){
			$root['user_login_status'] = 1;
			$sql = "select * from  ".DB_PREFIX."supplier_location where is_auto_order = 1 and id = ".$supplier_location_id;
			$supplier_location = $GLOBALS['db']->getRow($sql);
			if ($supplier_location){
				$root['status'] = 1;
				$supplier_id = intval($supplier_location['supplier_id']);
				
				//验证成功
				//开始生成订单
				$now = get_gmtime();
				$order['type'] = 0; //普通订单
				$order['user_id'] = $user_id;
				$order['user_name'] = $user['user_name'];
				$order['create_time'] = $now;
				$order['total_price'] = $money;  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费
				$order['pay_amount'] = 0;
				$order['pay_status'] = 0;  //新单都为零， 等下面的流程同步订单状态
				$order['delivery_status'] = 5;
				$order['order_status'] = 0;  //新单都为零， 等下面的流程同步订单状态
				$order['return_total_score'] = 0;  //结单后送的积分
				$order['return_total_money'] = 0;  //结单后送的现金
				$order['memo'] = '自主下单金额为:'.format_price($money);
				/*
				$order['region_lv1'] = 0;
				$order['region_lv2'] = 0;
				$order['region_lv3'] = 0;
				$order['region_lv4'] = 0;
				$order['address']	=	'';
				$order['mobile']	=	'';
				$order['consignee']	=	'';
				$order['zip']	=	'';
				*/
				$order['deal_total_price'] = $money;   //团购商品总价
				$order['discount_price'] = 0;
				$order['delivery_fee'] = 0;
				$order['ecv_money'] = 0;
				$order['account_money'] = 0;
				$order['ecv_sn'] = '';
				$order['delivery_id'] = 0;
				$order['payment_id'] = 0;
				$order['payment_fee'] = 0;				
				$order['bank_id'] = 0;
				$order['is_auto_order'] = 1;//门店自主下单标识
					
				do
				{
					$order['order_sn'] = to_date(get_gmtime(),"Ymdhis").rand(10,99);									
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'INSERT','','SILENT');
					$order_id = intval($GLOBALS['db']->insert_id());
				}while($order_id==0);
				
				if ($order_id > 0){
					//判断该门店是否有：自主下单商品 is_shop = 3
					$sql = "select * from ".DB_PREFIX."deal where is_shop = 3 and supplier_id =".$supplier_id." and account_id = ". $supplier_location['id'];
					$deal = $GLOBALS['db']->getRow($sql);
					if (!$deal){
						$deal = array();
						$deal['name'] = '门店['.$supplier_location['name'].']自主下单商品,状态为：无效;不在前台展示,请误更改状态及删除.';
						$deal['sub_name'] = $supplier_location['name'];
						$deal['supplier_id'] = $supplier_location['supplier_id'];
						$deal['account_id'] = $supplier_location['id'];
						$deal['icon'] = $supplier_location['preview'];
						$deal['img'] = $supplier_location['preview'];

						$deal['origin_price'] = 0;
						$deal['current_price'] = 0;
						
						$deal['is_coupon'] = 1;
						
						$deal['is_delivery'] = 0;
						$deal['is_effect'] = 0;//失效;本产品并不需要在前台展示

						$deal['deal_type'] = 0;//发券类型 0按件发券;1按单发券
						$deal['brief'] = $deal['name']; //团购简介

						$deal['buy_type'] = 0;//0:普通团购;2:在线订购;3:秒杀抢团;
						$deal['is_shop'] = 3;//0:团购;1:商品; 2:现金券列表; 3:门店自主下单商品;
						
						$deal['create_time'] = $now;
						$deal['update_time'] = $now;
												
						$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,'INSERT','','SILENT');
						$deal_id = intval($GLOBALS['db']->insert_id());
						$deal['id'] = $deal_id;
					}
					
					$deal_id = intval($deal['id']);
					
					$sql = "select count(*) from ".DB_PREFIX."deal_location_link where deal_id = $deal_id and location_id = ". $supplier_location['id'];
					if ($GLOBALS['db']->getOne($sql) == 0){
						//给商品设置上：支持的门店						
						$deal_location_link = array();
						$deal_location_link['deal_id'] = $deal_id;
						$deal_location_link['location_id'] = $supplier_location['id'];
						$GLOBALS['db']->autoExecute(DB_PREFIX."deal_location_link",$deal_location_link,'INSERT','','SILENT');
					}
					
					
					$goods_item = array();
					$goods_item['deal_id'] = $deal_id;
					$goods_item['number'] = 1;
					$goods_item['unit_price'] = $money;
					$goods_item['total_price'] = $money;
					$goods_item['name'] = $supplier_location['name'];
					$goods_item['sub_name'] = $supplier_location['name'];
					$goods_item['attr'] = '0';
					$goods_item['verify_code'] = md5($deal_id."_0");;
					$goods_item['order_id'] = $order_id;
					$goods_item['return_score'] = $deal['return_score'];
					$goods_item['return_total_score'] = $deal['return_total_score'];
					$goods_item['return_money'] = $deal['return_money'];
					$goods_item['return_total_money'] = $deal['return_total_money'];
					$goods_item['buy_type']	=	$deal['buy_type'];
					$goods_item['attr_str']	=	$deal['attr_str'];					
					$goods_item['balance_unit_price'] = 0;
					$goods_item['balance_total_price'] = 0;
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_item",$goods_item,'INSERT','','SILENT');
				}
				
				$root['order_id'] = $order_id;
				
				$root['info'] = '订单已生成,请及时支付';
			}else{
				$root['info'] = '商家不存在或该商家不支持自主下单';
			}
		}else{
			$root['user_login_status'] = 0;
			$root['info'] = '请先登陆';
		}
		


		
		output($root);
	}
}
?>
<?php
class my_order_detail{
	public function index()
	{
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
	
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;
	
		if($user_id>0)
		{
			$root['user_login_status'] = 1;		
			$order_id = intval($GLOBALS['request']['id']);
			require_once APP_ROOT_PATH."system/model/deal_order.php";
			$order_table_name = get_user_order_table_name($user_id);
			
			//echo $order_id; exit;
			$sql = "select o.*,r1.name as r1_name, r2.name as r2_name, r3.name as r3_name, r4.name as r4_name from ".$order_table_name." as o ".
				   "left outer join ".DB_PREFIX."delivery_region as r1 on r1.id = o.region_lv1 ".
					"left outer join ".DB_PREFIX."delivery_region as r2 on r2.id = o.region_lv2 ".
					"left outer join ".DB_PREFIX."delivery_region as r3 on r3.id = o.region_lv3 ".
					"left outer join ".DB_PREFIX."delivery_region as r4 on r4.id = o.region_lv4 ".
			" where o.is_delete = 0 and o.user_id = {$user_id} and o.id = {$order_id} limit 1";
			
			$order_info = $GLOBALS['db']->getRow($sql);
			
			if($order_info)
			{
				$root = get_order_goods($order_info);
				$root['return'] = 1;
							
				$root['user_login_status'] = 1;		
				$deliveryAddr = array();
				$deliveryAddr['consignee'] = $order_info['consignee'];//联系人姓名
				$deliveryAddr['delivery'] = $order_info['r1_name'].$order_info['r2_name'].$order_info['r3_name'].$order_info['r4_name'];
				$deliveryAddr['region_lv1'] = $order_info['region_lv1'];//国家
				$deliveryAddr['region_lv2'] = $order_info['region_lv2'];//省
				$deliveryAddr['region_lv3'] = $order_info['region_lv3'];//城市
				$deliveryAddr['region_lv4'] = $order_info['region_lv4'];//地区/县
				
				$deliveryAddr['delivery_detail'] = $order_info['address'];//详细地址
				$deliveryAddr['phone'] = $order_info['mobile'];//手机号码
				$deliveryAddr['postcode'] = $order_info['zip'];//邮编
			
				$root['deliveryAddr'] = $deliveryAddr;
				$root['content'] = $order_info['memo'];//订单备注
				
				$root['send_mobile'] = $user['mobile'];//团购券手机
				
				$root['tax_title'] = $order_info['tax_title'];//发票抬头
				$root['tax_id'] = 0;//发票内容
				
				$root['deliver_time_id'] = 0;//配送日期ID 默认没有这个参数，所以填0
				$root['delivery_list'] = $GLOBALS['m_config']['delivery_list'];
				
				//$default_payment_id = $GLOBALS['m_config']['select_payment_id'];
				if($GLOBALS['request']['payment_id'])
					$default_payment_id = intval($GLOBALS['request']['payment_id']);
				else{
					$default_payment_id = $order_info['payment_id'];
				}
				//$default_payment_id = $order_info['payment_id'];
				$root['payment_id'] = $default_payment_id;//支付方式
				
				$root['order_parm'] = init_order_parm($GLOBALS['m_config']);
				$root['order_parm']['has_ecv'] = 0;  //订单付款不支持代金券
				$root['evc_sn'] = '';//优惠券序号
				$root['evc_pwd'] = '';//优惠券序号
		
			
				$order_item_table_name = get_user_order_item_table_name($user_id);
				$goods_list = $GLOBALS['db']->getAll("select * from ".$order_item_table_name." where order_id = ".$order_id);
				$ids = array();
				$cart_ids = array();
				foreach($goods_list as $cart_goods)
				{
					array_push($ids,$cart_goods['deal_id']);
					array_push($cart_ids,$cart_goods['id']);
					
				}
				$ids_str = implode(",",$ids);
				$cart_ids_str = implode(",",$cart_ids);
				
				$is_delivery = intval($GLOBALS['db']->getOne("select is_delivery from ".DB_PREFIX."deal where is_delivery = 1 and id in (".$ids_str.")"));
				if($is_delivery==0)
				{
					$delivery_id = 0;
					$root['order_parm']['has_delivery'] = 0;
				}
				else
				{
					
					if($GLOBALS['request']['delivery_id'])
						$delivery_id = intval($GLOBALS['request']['delivery_id']);
					else{
						$delivery_id = $order_info['delivery_id'];//配送方式;
					}
					//$delivery_id = $order_info['delivery_id'];
					$root['order_parm']['has_delivery'] = 1;
				}
				$root['delivery_id'] = $delivery_id;//配送方式
				
				$has_coupon = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_coupon = 1 and id in (".$ids_str.")"));
				if($has_coupon == 0){
					$root['order_parm']['has_moblie'] = 0;
					$root['order_parm']['has_mcod'] = 1;
				}else{
					$root['order_parm']['has_moblie'] = 1;					
					$root['order_parm']['has_mcod'] = 0; //有团购券商品,不能做：货到付款
				}
				
				foreach($root['order_parm']['payment_list'] as $k=>$v)
				{
					if ($v['code'] == 'Mcod' && $root['order_parm']['has_mcod'] ==0){
						unset($root['order_parm']['payment_list'][$k]);
					}
				}
								
				$forbid_payment =	$GLOBALS['db']->getAll("select payment_id from ".DB_PREFIX."deal_payment where deal_id in (".$ids_str.")");
				foreach($forbid_payment as $forbid_payment_item)
				{
					foreach($root['order_parm']['payment_list'] as $k=>$v)
					{
						if($v['id']==$forbid_payment_item['payment_id'])
						{
							unset($root['order_parm']['payment_list'][$k]);
						}
					}
				}
				
				
				
				$forbid_delivery =	$GLOBALS['db']->getAll("select delivery_id from ".DB_PREFIX."deal_delivery where deal_id in (".$ids_str.")");
				foreach($forbid_delivery as $forbid_delivery_item)
				{
					foreach($root['order_parm']['delivery_list'] as $k=>$v)
					{
						if($v['id']==$forbid_payment_item['delivery_id'])
						{
							unset($root['order_parm']['delivery_list'][$k]);
						}
					}
				}
				
				//数组按顺序排序
				$payment_list = array();
				foreach($root['order_parm']['payment_list'] as $k=>$v)
				{
					$payment_list[] = $v;
				}
				$root['order_parm']['payment_list'] = $payment_list;
				
				$delivery_list = array();
				foreach($root['order_parm']['delivery_list'] as $k=>$v)
				{
					$delivery_list[] = $v;
				}
				$root['order_parm']['delivery_list'] = $delivery_list;
								
				$region4_id = intval($order_info['region_lv4']);
				$region3_id = intval($order_info['region_lv3']);
				$region2_id = intval($order_info['region_lv2']);
				$region1_id = intval($order_info['region_lv1']);
				
				if ($region4_id==0)
				{
					if ($region3_id==0)
					{
						if ($region2_id==0)
						{
							$region_id = $region1_id;
						}
						else
						$region_id = $region2_id;
					}
					else
					$region_id = $region3_id;
				}
				else
				$region_id = $region4_id;
				require_once APP_ROOT_PATH."system/model/cart.php";
				
				
				
				//订单的显示参数
				if($order_info['pay_status'] == 0)
				$root['has_cancel'] = 1;
				else
				$root['has_cancel'] = 0;
				
				$delivery_notice =  $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_notice where order_item_id in (".$cart_ids_str.") order by delivery_time desc limit 1");
				if($delivery_notice)
				{					
					$express = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."express where id = ".intval($delivery_notice['express_id']));
					$express['config'] = unserialize($express['config']);
					$root['kd_com'] = $express['config']['app_code'];
					if($root['kd_com'])
					$root['kd_sn'] = $delivery_notice['notice_sn'];
				}
				
				
				if($order_info['pay_status']!=2)
				{
					
					if ($root['order_parm']['has_delivery'] == 1){
						$root['has_edit_delivery'] = 1;
					}else{
						$root['has_edit_delivery'] = 0;
					}
					
					
					$root['has_edit_delivery_time'] = 0;
					$root['has_edit_invoice']=0;
					$root['has_edit_ecv'] = 0;
					$root['has_edit_message'] = 1;
					if ($root['order_parm']['has_moblie'] == 1){
						$root['has_edit_moblie'] = 1;
					}else{
						$root['has_edit_moblie'] = 0;
					}
					
					$GLOBALS['user_info']['id'] = $user_id;
					$account_pay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Account'");
					if($account_pay)
					$data = count_buy_total($region_id,$delivery_id,$default_payment_id,$account_money=0,$all_account_money=1,'','',$goods_list,$order_info['account_money'],$order_info['ecv_money']); 
					else 
					$data = count_buy_total($region_id,$delivery_id,$default_payment_id,$account_money=0,$all_account_money=0,'','',$goods_list,$order_info['account_money'],$order_info['ecv_money']); 
					//print_r($data);exit;
					if($data['pay_price']==0)
					{
						if($data['account_money']>0||$data['ecv_money']>0)
						{
							$root['has_pay'] = 1;
							$root['use_user_money'] = $data['account_price'];
						}
						else{								
							$root['has_pay'] = 0;
							$root['use_user_money'] = 0;
						}						
						$root['pay_money'] = 0;//还需要支付金额						
					}
					else
					{
						$root['has_pay'] = 1;
						$root['pay_money'] = $data['pay_price'];//还需要支付金额
						$root['use_user_money'] = 0;
					}
					
					if ($root['has_pay'] == 0){						
						$root['has_edit_delivery'] = 0;
						$root['has_edit_delivery_time'] = 0;
						$root['has_edit_invoice']=0;
						$root['has_edit_ecv'] = 0;
						$root['has_edit_message'] = 0;
						$root['has_edit_moblie'] = 0;
					}
					
					
					$root['feeinfo'] = getFeeItem($data);	
				}
				else
				{
					$root['has_pay'] = 0;
					$root['has_edit_delivery'] = 0;
					$root['has_edit_delivery_time'] = 0;
					$root['has_edit_invoice']=0;
					$root['has_edit_ecv'] = 0;
					$root['has_edit_message'] = 0;
					$root['has_edit_moblie'] = 0;
					
					
					$data['pay_total_price'] = $order_info['total_price'];
					$data['return_total_score'] = $order_info['return_total_score'];
					$data['total_price'] = $order_info['deal_total_price'];
					$data['delivery_fee'] = $order_info['delivery_fee'];
					$data['account_money'] = $order_info['account_money'];
					$data['ecv_money'] = $order_info['ecv_money'];
					
					$root['pay_money'] = 0;//还需要支付金额
					$root['use_user_money'] = 0;
					$root['feeinfo'] = getFeeItem($data);

					//有团购券,再显示团购券列表
					$couponlist = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where user_id = {$user_id} and order_id = ".$order_id);
					foreach($couponlist as $k=>$v)
					{
						$list[$k]['couponSn'] = $v['sn'];
						$list[$k]['couponPw'] = $v['password'];
						$list[$k]['qrcode'] = str_replace('sjmapi', '', get_domain().gen_qrcode($v['password']));
					}
					
					$root['couponlist'] = $couponlist;
				}	
				
			}else{
				$root['return'] = 0;
				$root['info'] = "订单不存在.";
			}			
		}
		else
		{
			$root['user_login_status'] = 0;		
		}		
		$root['pay_status'] = $order_info['pay_status'];
		$root['page_title'] ='订单明细';
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
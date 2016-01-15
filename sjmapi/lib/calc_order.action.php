<?php
class calc_order{
	public function index()
	{
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;
		$root['first_calc'] = $GLOBALS['request']['first_calc'];		
		if($user_id>0)
		{
			$root['user_login_status'] = 1;		
				$region4_id = intval($GLOBALS['request']['region_lv4']);
				$region3_id = intval($GLOBALS['request']['region_lv3']);
				$region2_id = intval($GLOBALS['request']['region_lv2']);
				$region1_id = intval($GLOBALS['request']['region_lv1']);
				
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
			
			$order_id = intval($GLOBALS['request']['id']);
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
			
			$root['order_parm'] = init_order_parm($GLOBALS['m_config']);
			//$delivery_id = $GLOBALS['m_config']['delivery_id'];
			$delivery_id = $GLOBALS['request']['delivery_id'];
			$payment_id = intval($GLOBALS['request']['payment_id']);
			$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);
			
				$ids = array();
				foreach($goods_list as $cart_goods)
				{
					array_push($ids,$cart_goods['deal_id']);
				}
				$ids_str = implode(",",$ids);
				
				$forbid_delivery =	$GLOBALS['db']->getAll("select delivery_id from ".DB_PREFIX."deal_delivery where deal_id in (".$ids_str.")");
				foreach($forbid_delivery as $forbid_delivery_item)
				{
					foreach($root['order_parm']['delivery_list'] as $k=>$v)
					{
						if($v['id']==$forbid_delivery_item['delivery_id'])
						{
							unset($root['order_parm']['delivery_list'][$k]);
						}
					}
				}	

				$has_coupon = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_coupon = 1 and id in (".$ids_str.")"));
				if($has_coupon == 0){
					$root['order_parm']['has_moblie'] = 0;
					$root['order_parm']['has_mcod'] = 1;
				}else{
					$root['order_parm']['has_moblie'] = 1;
					$root['order_parm']['has_mcod'] = 0; //有团购券商品,不能做：货到付款
				}
				
				//下单时需要绑定手机号码
				if (intval($GLOBALS['m_config']['order_has_bind_mobile']) == 1){
					//前面已经绑定手机号码了,这时不能再修改手机号码
					$root['order_parm']['has_moblie'] = 0;
				}
				
				foreach($root['order_parm']['payment_list'] as $k=>$v)
				{
					if ($v['code'] == 'Mcod' && $root['order_parm']['has_mcod'] ==0){
						unset($root['order_parm']['payment_list'][$k]);
					}
				}		
				
				
				$has_delivery = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_delivery = 1 and id in (".$ids_str.")"));
				if(!$has_delivery)
					$root['order_parm']['has_delivery'] = 0;
				else
					$root['order_parm']['has_delivery'] = 1;
								
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
			
			//开始计算订单
			$GLOBALS['user_info']['id'] = $user_id;
			$account_pay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Account'");
			if($account_pay)
				$data = count_buy_total($region_id,$delivery_id,$payment_id,$account_money=0,$all_account_money=1,'','',$goods_list,$order_info['account_money'],$order_info['ecv_money']); 
			else 
				$data = count_buy_total($region_id,$delivery_id,$payment_id,$account_money=0,$all_account_money=0,'','',$goods_list,$order_info['account_money'],$order_info['ecv_money']); 

			$root['feeinfo'] = getFeeItem($data);
			$root['use_user_money'] = $data['account_money'];
			$root['pay_money'] = $data['pay_price'];
			$root['info'] = '';//"订单已重新计算";
			$root['status'] = 1;
			
			//end 计算订单
		}
		else
		{
			$root['user_login_status'] = 0;		
			$root['status'] = 0;
		}		
	
		output($root);
	}
}
?>
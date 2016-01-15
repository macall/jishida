<?php
class calc_cart{
	public function index()
	{
		$root = array();
		
		$mobile=trim($GLOBALS['request']['mobile']);
		$code = strim($GLOBALS['request']['code']);/*验证码*/
		$ref_uid=intval($GLOBALS['request']['ref_uid']);/*邀请id*/
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		if (!empty($mobile)){
			
			if(!check_mobile($mobile))
			{
				$root['status'] = 2;
				$root['info'] = "请输入正确的手机号码";
				output($root);
			}
			
			//print_r($GLOBALS['request']);
			if($code=='')
			{
				$root['info']="请输入验证码!";
				$root['status'] = 2;
				output($root);
			}
			
			$db_code = $GLOBALS['db']->getRow("select id,code,add_time from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '$mobile' order by id desc");
			//print_r($db_code['code']);
			//$root['code']="select id,code,add_time from ".DB_PREFIX."sms_mobile_verify where status=0 and mobile_phone = '$mobile' and type=0 order by id desc";
			if($db_code['code'] != $code)
			{
				$root['info']="请输入正确的验证码!";
				$root['status'] = 2;
				output($root);
			}
	
			
			
			$new_time=get_gmtime();
			if(($new_time-$db_code['add_time']) > 60*30)/*30分钟失效*/
			{
				$root['info']="验证码已失效,请重新获取!";
				$root['status'] = 2;
				$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify  where mobile_phone = ".$mobile."");
				output($root);
			}
			
			//$GLOBALS['db']->query("update ".DB_PREFIX."sms_mobile_verify set status = 1 where id=".$db_code['id']."");
			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where id=".$db_code['id']."");
				
			
			$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '$mobile'");
				
			require_once(APP_ROOT_PATH."/system/model/user.php");
			if (!$user_data){
				//自动注册一个用户;
				$pwd = rand(1111,9999);
				$pwd = md5($pwd);
				
				$user_data = mobile_reg($mobile,$pwd,$ref_uid);
				
												
			}else{				
				$mobile = $user_data['mobile'];
				$pwd = $user_data['user_pwd'];
			}
		
			//检查用户,用户密码
			auto_do_login_user($mobile,$pwd,false);
			$user = $GLOBALS['user_info'];
			$user_id  = intval($user['id']);
			
			if ($user_id > 0){
				$root['mobile_user_id'] = $user_id;
				$root['mobile_user_name'] = $user['user_name'];
				$root['mobile_user_pwd'] = $user['user_pwd'];
			}else{
				$root['info']="用户登陆失败!";
				$root['status'] = 2;
			}
			
		}else{
			//检查用户,用户密码
			$user = $GLOBALS['user_info'];
			$user_id  = intval($user['id']);
		}
		

		
		
		
		$root['return'] = 1;
		$root['first_calc'] = $GLOBALS['request']['first_calc'];	
		
		if($user_id>0)
		{
			//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['user_login_status']	=	1;
			//第一次计算,主要是处理一些初始化参数,比如：默认配送地址
			
			if ($GLOBALS['request']['first_calc']==1){
				$delivery = getUserAddr($user_id,false);
				
				$root['delivery'] = $delivery;
				$delivery_region = array(
				   		'region_lv1'=>intval($delivery['region_lv1']),
				   		'region_lv2'=>intval($delivery['region_lv2']),
				   		'region_lv3'=>intval($delivery['region_lv3']),
				   		'region_lv4'=>intval($delivery['region_lv4'])
				);	
				
				$root['send_mobile'] = $user['mobile'];//默认填上用户手机号码						

				$payment_id = intval($GLOBALS['m_config']['select_payment_id']);//默认支付方式
				//$payment_id = intval($root['order_parm']['select_payment_id']);//默认支付方式
				$delivery_id = intval($GLOBALS['m_config']['delivery_id']);//配送方式;				
			}else{
				$delivery_region = array(
				   		'region_lv1'=>intval($GLOBALS['request']['region_lv1']),
				   		'region_lv2'=>intval($GLOBALS['request']['region_lv2']),
				   		'region_lv3'=>intval($GLOBALS['request']['region_lv3']),
				   		'region_lv4'=>intval($GLOBALS['request']['region_lv4'])
				);						
				if($GLOBALS['request']['payment_id'])
					$payment_id = intval($GLOBALS['request']['payment_id']);
				else{
					$payment_id = intval($GLOBALS['m_config']['select_payment_id']);//默认支付方式
				}
				if($GLOBALS['request']['delivery_id'])
					$delivery_id = intval($GLOBALS['request']['delivery_id']);
				else{
					$delivery_id = intval($GLOBALS['m_config']['delivery_id']);//配送方式;
				}
			}	

			
			if($GLOBALS['request']['from']=="wap"){
				//用户信息
				$cartdata = unserialize(base64_decode($GLOBALS['request']['cartdata']));
				$user_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
				$root['user_info']=$user_info;
				$root['delivery_list'] = $GLOBALS['m_config']['delivery_list'];
				
				//商品信息
				$res = insertCartData($user_id,es_session::id(),$cartdata);
			
				$cart_info = $res['data'];
			
				foreach($cart_info as $k=>$v){
					//查询数据库，获取商品信息
					$deal =$GLOBALS['db']->getRow("select img,max_bought from ".DB_PREFIX."deal where id=".$v['deal_id']);
					//单价*数量=总价
					$cart_info[$k]['current_price_format']=format_price($v['unit_price']);
					$cart_info[$k]['img']=get_abs_img_root($deal['img']);
					$cart_info[$k]['max_bought']=$deal['max_bought'];
					$cart_info[$k]['current_price']=round($v['unit_price'],2);
				}
				$root['cartinfo'] = $cart_info;
			}else{
				$cartdata = $GLOBALS['request']['cartdata'];
				$res = insertCartData($user_id,es_session::id(),$cartdata);
			}				
				
			
			
			if($res['info']!='')
			{
				//不可购买
				$root['info'] = $res['info'];
				$root['status'] = 0;
			}
			else
			{
				
				//可以购买
				$root['status'] = 1;
				//$delivery_id = intval($requestData['delivery_id']);//配送方式;
				if ($delivery_id == 0)
					$delivery_id = intval($GLOBALS['m_config']['delivery_id']);//取系统配置
				
				$root['select_delivery_id'] = $delivery_id;
	
				$ecvSn = strim($GLOBALS['request']['ecv_sn']);//优惠券
				$ecvPassword = strim($GLOBALS['request']['ecv_pwd']);//优惠券密码			   			
			
				require_once APP_ROOT_PATH."system/model/cart.php";
				$region4_id = intval($delivery_region['region_lv4']);
				$region3_id = intval($delivery_region['region_lv3']);
				$region2_id = intval($delivery_region['region_lv2']);
				$region1_id = intval($delivery_region['region_lv1']);
				
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
				
				$goods_list = $res['data']; 
				$GLOBALS['user_info']['id'] = $user_id;
				
				$ids = array();
				foreach($goods_list as $cart_goods)
				{
					array_push($ids,$cart_goods['deal_id']);
				}
				$ids_str = implode(",",$ids);
				
				$is_delivery = intval($GLOBALS['db']->getOne("select is_delivery from ".DB_PREFIX."deal where is_delivery = 1 and id in (".$ids_str.")"));

				if($is_delivery==0)
					$delivery_id = 0;
				
				$root['is_delivery']=$is_delivery;
				
				$account_pay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Account'");
				if($account_pay)
					$data = count_buy_total($region_id,$delivery_id,$payment_id,0,1,$ecvSn,$ecvPassword,$goods_list); 
				else 
					$data = count_buy_total($region_id,$delivery_id,$payment_id,0,0,$ecvSn,$ecvPassword,$goods_list); 
				
				$root['use_user_money'] = floatval($data['account_money']);//使用会员余额支付金额
				$root['pay_money'] = $data['pay_price'];//还需要支付金额
				$root['feeinfo'] = getFeeItem($data);
				$root['order_parm'] = init_order_parm($GLOBALS['m_config']);
				$root['order_parm']['delivery_id'] = $delivery_id;
				$root['order_parm']['payment_id'] = $payment_id;
								
				$ecv_payment_id = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Voucher'"));
				
				//重新为order_parm赋值
				if($ecv_payment_id)
				{
					$forbid_ecv = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_payment where payment_id =".$ecv_payment_id." and deal_id in (".$ids_str.")");
					if($forbid_ecv)
					$root['order_parm']['has_ecv'] = 0;//无优惠券
				}
				else
				$root['order_parm']['has_ecv'] = 0;//无优惠券
				
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
				
				//$root['order_parm']['has_mcod'] = 1;
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
//has_delivery_list				
				//$root['order_parm']['delivery_list'] = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery");
			}		
		}
		else
		{
			//未登录
			$root['user_login_status'] = 0;
		}
		
		$root['page_title'] ='确认订单';
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
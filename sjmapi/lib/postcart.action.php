<?php
//fwb update 2014-08-27
class postcart{
	public function index()
	{
//		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
//		$pwd = strim($GLOBALS['request']['pwd']);//密码
//		
//		//检查用户,用户密码
//		$user = user_check($email,$pwd);
//		$user_id  = intval($user['id']);
//
//		$cartdata = $GLOBALS['request']['cartdata'];
//		$res = insertCartData($user_id,session_id(),$cartdata);
//		
//		$root = array();
//		if($res['info']=='')
//		{
//			$root['return'] = 1;
//			$root['info'] = "提交成功";
//		}
//		else
//		{
//			$root['return'] = 0;
//			$root['info'] = $res['info'];
//		}	

		$root = array();
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$root['is_binding'] = 0;
		$root['user_id'] = $user_id;
		$root['mobile'] = $root['mobile'];
		if ($user_id > 0){			
			$isMobile = preg_match("/^(13\d{9}|14\d{9}|18\d{9}|15\d{9})|(0\d{9}|9\d{8})$/",$user['mobile']);
			if($isMobile)
			{
				$root['is_binding'] = 1;
				$root['mobile'] = $user['mobile'];
			}
		}
		//下单时需要绑定手机号码
		$root['order_has_bind_mobile'] = intval($GLOBALS['m_config']['order_has_bind_mobile']);
		
		
		if($GLOBALS['request']['from']=='wap'){
			
			
			$cartdata = unserialize(base64_decode($GLOBALS['request']['cartdata']));

			//file_put_contents(APP_ROOT_PATH."tmapi/log/postcart1".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($cartdata,true));
			$res = insertCartData($user_id,es_session::id(),$cartdata);	
			//file_put_contents(APP_ROOT_PATH."tmapi/log/postcart2".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($res,true));
				
			$cart_info = $res['data'];
			
			foreach($cart_info as $k=>$v){
				//查询数据库，获取商品信息
				$deal =$GLOBALS['db']->getRow("select img,max_bought,buy_count from ".DB_PREFIX."deal where id=".$v['deal_id']);
				$left_num=$GLOBALS['request']['left_number'];
				$cart_info[$k]['id']=$v['id'];
				$cart_info[$k]['current_price_format']=format_price($v['unit_price']);		
				$cart_info[$k]['img']=get_abs_img_root($deal['img']);
				
				$cart_info[$k]['current_price']=round($v['unit_price'],2);
				if(isset($left_num)){
					$cart_info[$k]['stock']=intval($deal['max_bought'])-intval($deal['buy_count']);
				}else{
					$cart_info[$k]['stock']=$left_num;
				}
				
			}
			$root['postcart_info']=$cart_info;
			
			//统计所有的价格
			$root['cartinfo'] = $GLOBALS['m_config']['yh'];		
			//$root['f_link_data']=get_link_list();	
			//$root['email']=$email;
			//$root['city_name']=$city_name;
			$root['page_title']='提交订单';
		
		}else{
			$root['cartinfo'] = $GLOBALS['m_config']['yh'];	
		}			

		
		output($root);
	}
}
?>
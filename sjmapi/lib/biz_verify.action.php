<?php
class biz_verify{
	public function index()
	{  
		$GLOBALS['lang']=require APP_ROOT_PATH."app/Lang/zh-cn/lang.php";
		 
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
		if($supplier_id > 0)
		{
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			
			require_once  APP_ROOT_PATH."system/model/biz_verify.php";

		//$sn = htmlspecialchars(addslashes(trim($GLOBALS['request']['coupon_sn'])));
				$pwd = htmlspecialchars(addslashes(trim($GLOBALS['request']['coupon_pwd'])));
				//$result = biz_check_coupon($biz_user,null,$pwd);
				$location_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."supplier_location where supplier_id=".$supplier_id." and is_main=1 and is_effect=1");
				$result = biz_check_coupon($biz_user,$pwd,$location_id);

				$root['return'] = 1;
				$root['status'] = $result['status'];
				
				if ($result['status'] == 1){													
					$root['info'] = $result['sub_msg'];
					//可消费的团购券数量，现按单发短信时，只能一次性消费掉所有的团购券
					$root['max_num'] = $result['number'];
					$root['min_num'] = $result['number'];
				}else{					
					$root['info'] = $result['msg'];//'此密码错误,不能接待。请与消费者确认提供的密码是否正确';
					//可消费的团购券数量
					$root['max_num'] = 0;
					$root['min_num'] = 0;
				}			
		}else{			
			$root['return'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商户不存在或密码错误";
		}

		output($root);
		
				
	}

}
?>
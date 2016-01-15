<?php
class biz_use_coupon{
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
			//使用数量
			$use_num = intval($GLOBALS['request']['use_num']);
			if ($use_num <= 0) $use_num = 1;
			
			$pwd = htmlspecialchars(addslashes(trim($GLOBALS['request']['coupon_pwd'])));
			
			
			$result = biz_use_coupon($biz_user,null,$pwd,$use_num);

			
			$root['return'] = 1;
			$root['status'] = $result['status'];
			if ($result['status'] == 1){
				$root['info'] = $result['sub_msg'];
			}else{
				$root['info'] = $result['msg'];
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
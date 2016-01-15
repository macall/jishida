<?php
class biz_login{
	public function index()
	{ 			
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$root['page_title'] = '商户登陆';
		$root['city_name']=$city_name;
		if(empty($email)||empty($pwd)){
			$root['return'] = 0;
			output($root);
			exit;
		}
			
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
		if($supplier_id > 0)
		{
			$root['return'] = 1;
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商家登陆成功";		
			
			$root['supplier_id'] = $biz_user['supplier_id'];
			$root['supplier_name'] = $biz_user['name'];
			$root['account_name'] = $biz_user['account_name'];
			
			$root['biz_email'] = $email;
			$root['biz_pwd'] = $biz_user['account_password'];;
			
			$root['status'] = 1;
			$root['biz_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆			
		}
		else
		{
			$err="商户不存在或密码错误";
			$root['return'] = 0;
			$root['status'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['biz_login_status'] = 0;
			$root['info'] = $err;		
			$root['uid'] = 0;
			$root['user_name'] = $email;
		}
		
		
		
		output($root);	
	}
}
?>
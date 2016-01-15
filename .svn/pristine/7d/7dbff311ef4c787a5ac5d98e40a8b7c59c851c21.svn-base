<?php
class biz_input_page{
	public function index()
	{  		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$root['page_title'] = '验证及消费登记';
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
		if($supplier_id > 0)
		{
			$root['return'] = 1;
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商家登陆成功";		
			
			$root['status'] = 1;
			$root['biz_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆			
		}
		else
		{
			$root['info'] = "请先登陆";
			$root['return'] = 0;
			$root['status'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['biz_login_status'] = 0;		
			$root['uid'] = 0;
			$root['user_name'] = $email;
		}
		
		$root['city_name']=$city_name;
		output($root);		
	}
}
?>
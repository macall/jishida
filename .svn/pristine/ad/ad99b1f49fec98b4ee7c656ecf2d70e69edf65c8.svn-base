<?php
class login_ajaxtest{
	public function index()
	{  
		$user_name_or_email=strim($GLOBALS['request']['email']);
		$user_pwd=strim($GLOBALS['request']['pwd']);
		$result = array();
		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where (user_name='".$user_name_or_email."' or email = '".$user_name_or_email."') and is_delete = 0");	
		if(empty($user_data))
		{			
			
			$result['data'] = "账号不存在";
			$result['user_name_or_email'] = $user_name_or_email;
			$result['user_pwd'] = $user_pwd;
			
			output($result);
		}
		else
		{
			$result['user'] = $user_data;
			if($user_data['user_pwd'] != md5($user_pwd.$user_data['code'])&&$user_data['user_pwd']!=$user_pwd)
			{
				
				$result['data'] = "账号密码错误";
				output($result);
			}
			else
			{
				header("location:index.php?ctl=login&email='.$user_name_or_email.'&pwd='.$user_pwd.'");
			}
		}
	}
}
?>
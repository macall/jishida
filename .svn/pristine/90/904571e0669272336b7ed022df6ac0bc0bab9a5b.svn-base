<?php
class register_do_phone{
	public function index()
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		$mobile=trim($GLOBALS['request']['mobile']);
		$pwd = strim($GLOBALS['request']['password']);
		
		if($mobile == '')
		{
			$root['status'] = 0;
			$root['info'] = '手机号码不能为空';
			output($root);
		}
				
		if(!check_mobile($mobile))
		{
			$root['status'] = 0;
			$root['info'] = "请输入正确的手机号码";
			output($root);
		}
		
		if(strlen($pwd)<4)
		{
			$root['status'] = 0;
			$root['info']	="密码不能低于四位";
			output($root);
		}
		
		$db_code = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."sms_mobile_verify where status=1 and mobile_phone = '$mobile' and type=0 order by id desc");
		if(!$db_code)
		{
			$root['status'] = 0;
			$root['info']	="手机号码未通过验证";
			output($root);
		}
				
		$root = mobile_reg($mobile,$pwd);
		
		
		output($root);
	}
}
?>
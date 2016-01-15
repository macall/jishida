<?php
class register_do_phone{
	public function index()
	{
//		require_once APP_ROOT_PATH."system/libs/user.php";
		$mobile=trim($GLOBALS['request']['mobile']);
		$pwd = strim($GLOBALS['request']['password']);
                $password_confirm = strim($GLOBALS['request']['password_confirm']);
                $gender = intval($GLOBALS['request']['gender']);
		$sms_verify = intval($GLOBALS['request']['sms_verify']);
                
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
                
                if($pwd!=$password_confirm)
		{
			$root['status'] = 0;
			$root['info'] = "您两次输入的密码不匹配";
			output($root);
		}
                
                if($sms_verify=="")
		{
			$root['status'] = 0;
			$root['info']	=	"请输入收到的验证码";
			output($root);
		}
		
		$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE add_time <=".(NOW_TIME-SMS_EXPIRESPAN);
		$GLOBALS['db']->query($sql);
		
		$mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$mobile."'");
		
		if($mobile_data['code']!=$sms_verify)
		{
			$root['status'] = 0;
			$root['info']	=  "验证码错误";
			output($root);
		}
		
//		$db_code = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."sms_mobile_verify where status=1 and mobile_phone = '$mobile' and type=0 order by id desc");
//		if(!$db_code)
//		{
//			$root['status'] = 0;
//			$root['info']	="手机号码未通过验证";
//			output($root);
//		}
				
		$root = mobile_reg($mobile,$pwd,$gender);
		
		output($root);
	}
}
?>
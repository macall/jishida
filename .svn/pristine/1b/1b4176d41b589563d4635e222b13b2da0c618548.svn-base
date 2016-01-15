<?php
class do_binding_mobile{
	public function index()
	{
		//$old_mobile = strim($GLOBALS['request']['old_mobile']);/*原绑定手机号码*/
		$new_mobile = strim($GLOBALS['request']['new_mobile']);/*新绑定手机号码*/
		$code = strim($GLOBALS['request']['code']);/*验证码*/
		//print_r($GLOBALS['request']);
		$root = array();
		$root['return'] = 1;
		$root['status'] =0;/*0:绑定失败1:成功2:已经被他人绑定了*/
		$isNewMobile = preg_match("/^(13\d{9}|14\d{9}|18\d{9}|15\d{9})|(0\d{9}|9\d{8})$/",$new_mobile);
		if(!$isNewMobile)
		{
			$root['info']="请输入正确新手机号码!";
			output($root);
		}
		if($code=='')
		{
			$root['info']="请输入验证码!";
			output($root);
		}
		 
		$binding_user_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where mobile = '$new_mobile'");
		if(intval($binding_user_id) >0)
		{
			$root['info']="该手机号码已注册,请直接使用该手机号码登陆";
			$root['status'] =2;
			output($root);

		}
		
		$db_code = $GLOBALS['db']->getRow("select code,add_time from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '$new_mobile' order by id desc");
		//print_r($db_code['code']);
		if($db_code['code'] != $code)
		{
			$root['info']="请输入正确的验证码!";
			output($root);
		}
		$new_time=get_gmtime();
		if(($new_time-$db_code['add_time']) > 60*30)/*30分钟失效*/
		{
			$root['info']="验证码已失效,请重新获取!";
			$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = ".$new_mobile."");
			output($root);
		}
		
	
		
		/*检查用户,用户密码*/
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;		
		if($user_id>0)
		{
			$root['user_login_status'] = 1;	
			/*
			$isMobile = preg_match("/^(13\d{9}|14\d{9}|18\d{9}|15\d{9})|(0\d{9}|9\d{8})$/",$user['mobile']);	
			if($isMobile)
			{
				if($old_mobile != $user['mobile'])
				{
					$root['info']="原手机号码输入错误!";
					output($root);
				}	
			}
			*/
			$re=$GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '$new_mobile' where id=".$user_id);
			
			if($re)
			{
				$root['info']="绑定成功!";
				$root['status'] =1;
				$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = ".$new_mobile."");
				
			}else{
				$root['info']="绑定失败!";
			}
		}
		else
		{
			$root['user_login_status'] = 0;		
		}		
	
		output($root);
	}
}
?>
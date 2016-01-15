<?php
class register_verify_code{
	public function index()
	{
		$mobile=trim($GLOBALS['request']['mobile']);
		$code = strim($GLOBALS['request']['code']);/*验证码*/
		$ref_uid=intval($GLOBALS['request']['ref_uid']);/*邀请id*/
		$is_register = strim($GLOBALS['request']['is_register']);//0:仅验证;1:除验证外,如果用户不存在,则直接创建一个新用户,客户端自动登陆;
		
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
				
		//print_r($GLOBALS['request']);
		if($code=='')
		{
			$root['info']="请输入验证码!";
			$root['status'] = 0;
			output($root);
		}
		$db_code = $GLOBALS['db']->getRow("select id,code,add_time from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '$mobile' order by id desc");
		//print_r($db_code['code']);
		if($db_code['code'] != $code)
		{
			$root['info']="请输入正确的验证码!";
			$root['status'] = 0;
			output($root);
		}
		$new_time=get_gmtime();
		if(($new_time-$db_code['add_time']) > 60*30)/*30分钟失效*/
		{
			$root['info']="验证码已失效,请重新获取!";
			$root['status'] = 0;
			$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify  where mobile_phone = ".$mobile."");
			output($root);
		}
		
		//$GLOBALS['db']->query("update ".DB_PREFIX."sms_mobile_verify set status = 1 where id=".$db_code['id']."");
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where id=".$db_code['id']."");
				
		//is_register 0:仅验证;1:除验证外,如果用户不存在,则直接创建一个新用户,客户端自动登陆;
		if($is_register == 1){
			$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile = '$mobile'");
			
			require_once(APP_ROOT_PATH."/system/model/user.php");
			if (!$user_data){
				//自动注册一个用户;
				$pwd = rand(1111,9999);
			
				$user_data = mobile_reg($mobile,$pwd,$ref_uid);
			
				$pwd = md5($pwd);
			}else{
				$mobile = $user_data['mobile'];
				$pwd = $user_data['user_pwd'];
			}
			//检查用户,用户密码
			auto_do_login_user($mobile,$pwd,false);
			$user = $GLOBALS['user_info'];
			$user_id  = intval($user['id']);
				
			if ($user_id > 0){
				$root['return'] = 1;
				$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
				$root['info'] = "用户登陆成功";
				$root['uid'] = $user['id'];
				$root['user_name'] = $user['user_name'];
				$root['user_email'] = $user['email'];
				$root['user_money'] = $user['money'];
				$root['mobile'] = $user['mobile'];
				$root['user_pwd'] = $user['user_pwd'];
				$root['user_money_format'] = format_price($user['money']);//用户金额
	
				$root['home_user']['user_avatar'] = get_abs_img_root(get_muser_avatar($user['id'],"big"));
				$root['user_avatar'] = get_abs_img_root(get_muser_avatar($user['id'],"big"));				
			}else{
				$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
				$root['info']="用户登陆失败!";
				$root['status'] = 1;
			}
			
		}else{
			$root['info']="验证成功";
		}
		
		$root['status'] = 1;
		output($root);
	}
}
?>
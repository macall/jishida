<?php

class register{
	public function index()
	{
		$email = strim($GLOBALS['request']['email']);//邮箱
		$pwd = strim($GLOBALS['request']['password']);//密码
		$user_name = strim($GLOBALS['request']['user_name']);//用户名
		$gender = intval($GLOBALS['request']['gender']);
		$ref_uid = intval($GLOBALS['request']['ref_uid']);
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		

		if(strlen($pwd)<4)
		{
			$root['return'] = 0;
			$root['info']	=	"密码不能低于四位";
		}
		else
		{
			$user_data['email'] = $email;
			$user_data['user_name'] = $user_name;
			$user_data['user_pwd'] = $pwd;
			$user_data['sex'] = $gender;
			if($ref_uid)
				$user_data['pid']=$ref_uid;
			else 
				$user_data['pid']=0;
			
			$res = save_user($user_data);
			
			if($res['status'] == 1)
			{
				$root['return'] = 1;
				$root['status'] = 1;// fwb add 2014-08-27
				$root['info']	=	"注册成功";
				$root['uid'] = $res['data'];
				$root['id'] = $res['data'];
				$root['user_name'] = $user_name;
				$root['user_email'] = $email;	
				$root['user_avatar'] = get_abs_img_root(get_muser_avatar($root['uid'],"big"));		
				$root['user_pwd'] = $res['user_pwd'];	
				
			}
			else
			{
				$error = $res['data'];	
				if(!$error['field_show_name'])
				{
						$error['field_show_name'] = $GLOBALS['lang']['USER_TITLE_'.strtoupper($error['field_name'])];
				}	
				if($error['error']==EMPTY_ERROR)
				{
					$error_msg = sprintf($GLOBALS['lang']['EMPTY_ERROR_TIP'],$error['field_show_name']);
				}
				if($error['error']==FORMAT_ERROR)
				{
					$error_msg = sprintf($GLOBALS['lang']['FORMAT_ERROR_TIP'],$error['field_show_name']);
				}
				if($error['error']==EXIST_ERROR)
				{
					$error_msg = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$error['field_show_name']);
				}
				$root['return'] = 0;
				$root['status'] = 0;// fwb add 2014-08-27
				$root['info']	=	$error_msg;
			}
		}
		
		
	if(strim($GLOBALS['request']['sina_id'])!='')
	{
		$root['login_type'] = "Sina";
	}
	if(strim($GLOBALS['request']['tencent_id'])!='')
	{
		$root['login_type'] = "Tencent";
	}
	if(strim($GLOBALS['request']['qq_id'])!='')
	{
		$root['login_type'] = "Qq";
	}
	$root['page_title'] ='注册';
	$root['city_name']=$city_name;
	output($root);
		
	}
}
?>
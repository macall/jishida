<?php
class fasongduanxin
{
	public function index()
	{
		
		$mobile=strim($GLOBALS['request']['user_phoneNum']);
		//print_r($email);echo"<br />";print_r($pwd);exit;
		
		//检查用户,用户密码
		$user_return = $GLOBALS['user_info'];
		$user = $user_return;
		$user_id  = intval($user['id']);
		//print_r($user_id);exit;
		if($mobile =='')
			$mobile=$user['mobile'];
		if($user_id==0)
		{
			$root['status']=0;
			$root['user_login_status'] = 0;		
		}
		else
		{
			$youhui_id = intval($GLOBALS['request']['id']);
			require_once APP_ROOT_PATH."system/model/youhui.php";
			$youhui_info = get_youhui($youhui_id);
			
			$result = download_youhui(intval($youhui_info['id']),$user_id);
			
			if($result['status']>=0)
			{
				if($result['status']==YOUHUI_OUT_OF_STOCK||$result['status']==YOUHUI_USER_OUT_OF_STOCK)
				{
					$root['status'] = 0;
					$root['info'] = $result['info'];
					
				}
				else if($result['status']==YOUHUI_DOWNLOAD_SUCCESS)
				{
					if(app_conf("SMS_ON")==1&&$result['log']['mobile']!=""&&$youhui_info['is_sms']==1)
					{
						//发送短信
						send_youhui_log_sms($result['log']['id']);
					}

					$root['status'] = 1;
					$root['info'] = $result['info'];
				}
				else
				{
					$root['status'] = 0;
					$root['info'] = $result['info'];
				}
			}
			else
			{
				$root['status'] = 0;
				$root['info'] = $result['info'];
			}
		}
		output($root);
	}
}
?>
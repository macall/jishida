<?php
class binding_mobile{
	public function index()
	{
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;		
		if($user_id>0)
		{
			$root['user_login_status'] = 1;
			$root['is_binding'] = 0;
			$isMobile = preg_match("/^(13\d{9}|14\d{9}|18\d{9}|15\d{9})|(0\d{9}|9\d{8})$/",$user['mobile']);	
			if($isMobile)
			{
				$root['is_binding'] = 1;
				$root['mobile'] = $user['mobile'];
			}
		}
		else
		{
			$root['user_login_status'] = 0;		
		}
		$root['page_title']="手机绑定";
		//下单时需要绑定手机号码
		$root['order_has_bind_mobile'] = intval($GLOBALS['m_config']['order_has_bind_mobile']);
		output($root);
	}
}
?>
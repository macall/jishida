<?php
class biz_feedback
{
	public function index()
	{
		
		$root = array();		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
		
		if($supplier_id > 0)
		{
		
			$title = strim($GLOBALS['request']['title']);//前台，老板，财务，其它
			$content = strim($GLOBALS['request']['content']);//意见反馈
			$contact = strim($GLOBALS['request']['contact']);//联系方式
			
	
			$reply_data = array();
			$reply_data['title'] = $title;
			$reply_data['content'] = $content;
			$reply_data['create_time'] = get_gmtime();
			$reply_data['rel_table'] = 'feedback';
			$reply_data['rel_id'] = 0;
			$reply_data['user_id'] = 0;
			$reply_data['pid'] = 0;
			$reply_data['is_effect'] = 1;
			$reply_data['city_id'] = 0;
			$reply_data['is_buy'] = 0;
			$reply_data['contact_name'] = 0;
			$reply_data['contact'] = $contact;
			$reply_data['point'] = 0;
			$reply_data['is_read'] = 0;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."message",$reply_data);
			$msg_id = intval($GLOBALS['db']->insert_id());
			
			if ($msg_id > 0){
				$root['return'] = 1;
				$root['info'] = "反馈成功";				
			}else{
				$root['return'] = 0;
				$root['info'] = "反馈失败";				
			}
		}else{			
			$root['return'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商户不存在或密码错误";
		}
		
		output($root);
	}
}
?>
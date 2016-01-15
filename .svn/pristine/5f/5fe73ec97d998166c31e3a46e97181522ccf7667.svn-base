<?php
class biz_tuan_msg_reply
{
	public function index()
	{

		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
	
		$id = intval($GLOBALS['request']['id']);//消息id
		$deal_id = intval($GLOBALS['request']['deal_id']);//团购商品id
		$admin_reply = strim($GLOBALS['request']['admin_reply']);//回复内容 
		
		if (empty($admin_reply)){
			$root['return'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "回复不能为空";
		}else{
			if($supplier_id > 0)
			{
				$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
					
				$sql = "update ".DB_PREFIX."message set admin_reply = '".$admin_reply."',admin_id=$supplier_id,update_time=".get_gmtime()." where id = ".$id." and rel_id = ".$deal_id." and rel_table = 'deal' and pid = 0 and is_buy = 1";
				//echo $sql;exit;
				//$root['sql'] = $sql;
				$GLOBALS['db']->query($sql);
				$root['return'] = 1;
			}else{
				$root['return'] = 0;
				$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
				$root['info'] = "商户不存在或密码错误";
			}			
		}
		
		

		output($root);
	}
}
?>
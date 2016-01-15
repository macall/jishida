<?php
class biz_tuan_msg_read
{
	public function index()
	{

		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
	
		$deal_id = intval($GLOBALS['request']['deal_id']);//团购商品id
		
		if($supplier_id > 0)
		{	 		
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			
			$sql = "update ".DB_PREFIX."message set is_read = 1 where is_read = 0 and rel_id = ".$deal_id." and rel_table = 'deal' and pid = 0 and is_buy = 1";
			//$root['sql'] = $sql;
			$GLOBALS['db']->query($sql);
			$root['return'] = 1;
		}else{			
			$root['return'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商户不存在或密码错误";
		}
		output($root);
	}
}
?>
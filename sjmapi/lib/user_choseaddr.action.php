<?php
class user_choseaddr{
	public function index()
	{
		$id =intval($GLOBALS['request']['id']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);		
		$sql="update ".DB_PREFIX."user_consignee set is_default=(case id when $id then 1 else 0 end) where user_id=$user_id";
		$GLOBALS['db']->query($sql);	
		$root = array();
		$root['return'] = 1;
		$root['page_title'] = '预约地址';
		output($root);
	}
}
?>
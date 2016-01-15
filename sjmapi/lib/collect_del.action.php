<?php
class collect_del{
	public function index(){
		$id = intval($GLOBALS['request']['id']); //当前分页
		
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;		
		if($user_id>0)
		{
			$root['user_login_status'] = 1;
			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_collect where id = ".$id." and user_id = ".intval($user_id));
			if($GLOBALS['db']->affected_rows())
			{
				$root['status'] = 1;
			}
			else
			{
				$root['status'] = 0;
			}
			
		}
		else{
			$root['user_login_status'] = 0;
			$root['status'] = 0;
		}
		output($root);
	}
}
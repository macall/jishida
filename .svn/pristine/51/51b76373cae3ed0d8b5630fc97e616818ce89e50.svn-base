<?php
class pwd{
	public function index()
	{
/*
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		if (isset($GLOBALS['request']['old_pwd'])){
			$pwd = strim($GLOBALS['request']['old_pwd']);//密码
		}else{
			$pwd = strim($GLOBALS['request']['pwd']);//密码
		}
	*/	
		
		
		$new_pwd = strim($GLOBALS['request']['newpassword']);//新密码
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['page_title']='修改密码';	
		$root['user_name'] = $user['user_name'];
		$root['city_name']=$city_name;
		if($user_id>0)
		{
			$root['user_login_status'] = 1;	
				
			if (strlen($GLOBALS['request']['newpassword']) < 4){
				$root['return'] = 0;
				$root['info'] = "注册密码不能少于4位";
				
				
				output($root);
			}
			
			$new_pwd = md5($new_pwd);
			$sql = "update ".DB_PREFIX."user set user_pwd = '".$new_pwd."' where id = {$user_id}";
			$GLOBALS['db']->query($sql);
			
			$root['return'] = 1;
			$root['uid'] = $user_id;
			$root['email'] = $user['email'];
			$root['user_name'] = $user['user_name'];
			$root['user_pwd'] = $new_pwd;
			$root['user_avatar'] = get_abs_img_root(get_muser_avatar($user['id'],"big"));
				
			$root['info'] = "密码更新成功!";
	
			
		}
		else
		{
			$root['return'] = 0;
			$root['user_login_status'] = 0;		
			$root['info'] = "原始密码不正确";
		}		
		
		output($root);
	}
}
?>
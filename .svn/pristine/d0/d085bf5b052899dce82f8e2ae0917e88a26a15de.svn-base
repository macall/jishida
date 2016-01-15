<?php
class sendmsg
{
	public function index()
	{
		$root = array();
		require_once APP_ROOT_PATH."system/model/user.php";
		
		$username = strim($GLOBALS['request']['user_name']);		
		if(strim($GLOBALS['request']['from']=='wap')){
			$root['message']=htmlspecialchars(addslashes(trim($GLOBALS['request']['message'])));
			$root['name']=$GLOBALS['request']['user_name'];
			$root['da']=$GLOBALS['user_info'];
		}
		
		if($GLOBALS['user_info'])
		{
			$user_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where user_name = '".$username."'");
			$content = htmlspecialchars(addslashes(trim($GLOBALS['request']['message'])));
			send_user_msg("",$content,intval($GLOBALS['user_info']['id']),$user_id,get_gmtime());
			$root['return'] = 1;
			$root['info']="发送成功";
		}
		else
		{
			$root['return'] = 0;
			$root['info'] = "请先登录";
		}		
		output($root);
		
	}
}
?>
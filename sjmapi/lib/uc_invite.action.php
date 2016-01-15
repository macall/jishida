<?php
class uc_invite
{
	public function index()
	{
		
		$root = array();
		$root['return'] = 1;
		
		$page = intval($GLOBALS['request']['page']); //分页
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$page=$page==0?1:$page;
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
			
		$url = get_domain().APP_ROOT;
		$share_url=str_replace("sjmapi", "wap", $url);
		if($user_id)
		$share_url .= "?r=".base64_encode(intval($user_id));
		$root['share_url']=$share_url;	
		$root['city_name']=$city_name;
		$root['page_title']="邀请链接";//fwb 2014-08-27
		output($root);
	}
	
	
}
?>
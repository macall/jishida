<?php
class collect_list{
	public function index(){
		
		$page = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称

		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
		
		$root = array();
		$root['return'] = 1;	
			
		if($user_id>0)
		{
			$root['user_login_status'] = 1;
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;
			$result = get_collect_list($limit,$user_id);
			
			$root['collect_list'] = $result['list'];
			
			$page_total = ceil($result['count']/$page_size);
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
			
		}
		else{
			$root['user_login_status'] = 0;
		}
		$root['page_title']="收藏夹";
		$root['city_name']=$city_name;
		output($root);
	}
}
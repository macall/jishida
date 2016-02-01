<?php
class tech_dp_list{
	public function index()
	{
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$type ="tech";	
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$tech_id = $user_id;
		require_once APP_ROOT_PATH."system/model/tech.php";
		$tech_info = get_tech($tech_id);
		$relate_data_name = $tech_info['name'];
		

		$page = intval($GLOBALS['request']['page']);/*分页*/
		$city_name = strim($GLOBALS['request']['city_name']);//城市分类ID
		
		$root = array();
		$root['return'] = 1;
		
		$page=$page==0?1:$page;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		
		require_once APP_ROOT_PATH."system/model/review.php";
		require_once APP_ROOT_PATH."system/model/user.php";
		$message_re = get_dp_list($limit,$param=array("deal_id"=>$deal_id,"youhui_id"=>$youhui_id,"event_id"=>$event_id,"location_id"=>$location_id,"tech_id"=>$tech_id,"tag"=>""),"","");
		$root['message_list']=$message_re['list'];
		
		if(count($message_re['list'])>0)
		{
			$sql = "select count(*) from ".DB_PREFIX."supplier_location_dp where  ".$message_re['condition'];
			$message_re['count'] = $GLOBALS['db']->getOne($sql);
		}
		
		$root['message_count']=$message_re['count'];		

		

		
		$page_total = ceil($message_re['count']/$page_size);
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		
		
		$root['type'] = $type;
		$root['id']=$id;
		$root['page_title']="点评列表";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
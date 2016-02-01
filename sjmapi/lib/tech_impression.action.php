<?php
class tech_impression{
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
		$sql="select dp.*,u.user_name from fanwe_deal_order_item i,fanwe_deal_order o,fanwe_supplier_location_dp dp left join fanwe_user u on dp.user_id=u.id where dp.deal_id>0 and i.dp_id=dp.id and i.order_id=o.id and o.technician_id=$user_id";
		$message_list=$GLOBALS['db']->getAll($sql);
		$root['message_list']=$message_list;
		
		if(count($message_list)>0)
		{
			$message_list['count'] = count($message_list);
		}		
		$root['message_count']=$message_list['count'];
		
		$page_total = ceil($message_list['count']/$page_size);
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		
		
		$root['type'] = $type;
		$root['id']=$id;
		$root['page_title']="点评列表";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
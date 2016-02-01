<?php
class my_order_list{
	public function index()
	{
		$pay_status=intval($GLOBALS['request']['pay_status']);/*0:未付款 、部分,2:已付款*/
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;
		
		if($user_id>0)
		{
			require_once APP_ROOT_PATH."system/model/deal_order.php";
			$order_table_name = get_user_order_table_name($user_id);
			
			$root['user_login_status'] = 1;	
			if($pay_status==3){
				
			}elseif($pay_status ==2)	
				$pay_status_and=" and pay_status=2 ";
			else    
				$pay_status_and=" and pay_status  in(0,1) ";
			//$nowPage = intval($GLOBALS['request']['page']); //当前分页
			$nowPage = intval($GLOBALS['request']['page'])?intval($GLOBALS['request']['page']):1; //当前分页
			$totalRows = intval($GLOBALS['request']['totalRows']); //总记录数	
			$pageRows = PAGE_SIZE;//每页显示记录数	
			
			$limit = (($nowPage-1)*$pageRows).",".$pageRows;
			
			if ($totalRows == 0){		
				$totalRows = $GLOBALS['db']->getOne("select count(*) from ".$order_table_name." where user_id = ".$user_id." and type = 0 and is_delete = 0 {$pay_status_and}");	
			
			}
			$totalPages = ceil($totalRows / $pageRows); //总页数
		
			//$root = array();
			
			$root['totalPages'] = $totalPages; //总页数
			$root['pageRows'] = $pageRows; //页记录数
			$root['nowPage'] = $nowPage; //当前页
			$root['totalRows'] = $totalRows;//总记录数
		
			$list = $GLOBALS['db']->getAll("select * from ".$order_table_name." where user_id = ".$user_id." and type = 0 and is_delete = 0 {$pay_status_and} order by create_time desc limit ".$limit);		
			
			$root['return'] = 1;
		
			$orderlist = array();
			foreach($list as $item)
			{
				$orderlist[] = get_order_goods($item);
			}
			
			$root['item'] = $orderlist;
			if($pay_status <2)
				$root['not_pay_count'] = $totalRows;
			
			$root['page'] = array("page"=>$nowPage,"page_total"=>$totalPages,"page_size"=>PAGE_SIZE);		
		}
		else
		{
			$root['user_login_status'] = 0;		
		}
		$root['city_name']=$city_name;
	   $root['page_title'] = '订单列表';	
		output($root);
	}
}
?>
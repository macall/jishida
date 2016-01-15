<?php
class dp_list{
	public function index()
	{
		
		$type = strim($GLOBALS['request']['type']);
		$id = intval($GLOBALS['request']['id']);	
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$deal_id = 0;
		$youhui_id = 0;
		$location_id = 0;
		$event_id = 0;	
		if($type=="deal")
		{
			$deal_id = $id;
			require_once APP_ROOT_PATH."system/model/deal.php";
			$deal_info = get_deal($deal_id);
			$relate_data_name = $deal_info['name'];
		}
		elseif($type=="supplier")
		{
			$location_id = $id;
			require_once APP_ROOT_PATH."system/model/supplier.php";
			$location_info = get_location($location_id);
			$relate_data_name = $location_info['name'];
		}
		elseif($type=="youhui")
		{
			$youhui_id = $id;
			require_once APP_ROOT_PATH."system/model/youhui.php";
			$youhui_info = get_youhui($youhui_id);
			$relate_data_name = $youhui_info['name'];
		}
		elseif($type=="event")
		{
			$event_id = $id;
			require_once APP_ROOT_PATH."system/model/event.php";
			$event_info = get_event($event_id);
			$relate_data_name = $event_info['name'];
		}

		$page = intval($GLOBALS['request']['page']);/*分页*/
		$city_name = strim($GLOBALS['request']['city_name']);//城市分类ID
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		
		$root = array();
		$root['return'] = 1;
		
		$page=$page==0?1:$page;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		
// 		$message_re=m_get_message_list($limit," m.rel_table = 'deal' and m.rel_id=".$tuan_id." and m.is_buy = 1",0);/*购买评论*/
		require_once APP_ROOT_PATH."system/model/review.php";
		require_once APP_ROOT_PATH."system/model/user.php";
		$message_re = get_dp_list($limit,$param=array("deal_id"=>$deal_id,"youhui_id"=>$youhui_id,"event_id"=>$event_id,"location_id"=>$location_id,"tag"=>""),"","");
		foreach($message_re['list'] as $k=>$v){
			$message_re['list'][$k]['width']= ($v['point'] / 5) * 100;
			$message_re['list'][$k]['create_time']=to_date($v['create_time']);
			$uinfo = load_user($v['user_id']);
			$message_re['list'][$k]['user_name'] = $uinfo['user_name'];
			foreach($message_re['list'][$k]['images'] as $kk=>$vv)
			{
				$message_re['list'][$k]['images'][$kk] = get_abs_img_root(get_spec_image($vv,60,60,1));
				$message_re['list'][$k]['oimages'][$kk] = get_abs_img_root($vv);
			}
		}
		$root['message_list']=$message_re['list'];
		
		if(count($message_re['list'])>0)
		{
			$sql = "select count(*) from ".DB_PREFIX."supplier_location_dp where  ".$message_re['condition'];
			$message_re['count'] = $GLOBALS['db']->getOne($sql);
		}
		
		$root['message_count']=$message_re['count'];		
		
		//$deal = get_deal($tuan_id);
		
		$dp_info = load_dp_info(array("deal_id"=>$deal_id,"youhui_id"=>$youhui_id,"event_id"=>$event_id,"location_id"=>$location_id));
		
		$root['name'] = $relate_data_name;
		//星级点评数
		$root['star_1'] = $dp_info['dp_count_1'];
		$root['star_2'] = $dp_info['dp_count_2'];
		$root['star_3'] = $dp_info['dp_count_3'];
		$root['star_4'] = $dp_info['dp_count_4'];
		$root['star_5'] = $dp_info['dp_count_5'];
		$root['star_dp_width_1'] = $dp_info['avg_point_1_percent'];
		$root['star_dp_width_2'] = $dp_info['avg_point_2_percent'];
		$root['star_dp_width_3'] = $dp_info['avg_point_3_percent'];
		$root['star_dp_width_4'] = $dp_info['avg_point_4_percent'];
		$root['star_dp_width_5'] = $dp_info['avg_point_5_percent'];
		
		$buy_dp_sum = 0.0;
// 		$buy_dp_group = $GLOBALS['db']->getAll("select point,count(*) as num from ".DB_PREFIX."message where rel_id = ".$tuan_id." and rel_table = 'deal' and pid = 0 and is_buy = 1 group by point");
// 		foreach($buy_dp_group as $dp_k=>$dp_v)
// 		{
// 			$star = intval($dp_v['point']);
// 			if ($star >= 1 && $star <= 5){
// 				$root['star_'.$star] = $dp_v['num'];				
// 				$buy_dp_sum += $star * $dp_v['num'];
// 				$root['star_dp_width_'.$star] = (round($dp_v['num']/ $message_re['count'],1)) * 100;
// 			}
// 		}
		
		//点评平均分
		$root['buy_dp_sum']= $dp_info['dp_count'];
		$root['buy_dp_avg'] = $dp_info['avg_point'];
		$root['buy_dp_width'] = ( $dp_info['avg_point'] / 5) * 100;		
		
		$page_total = ceil($message_re['count']/$page_size);
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
	
		
		$root['allow_dp'] = 0;//0:不允许点评;1:允许点评
		//判断用户是否购买了这个商品
		if ($user_id > 0){
// 			$sql = "select count(*) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.deal_id = ".intval($tuan_id)." and do.user_id = ".intval($user_id)." and do.pay_status = 2";
// 			//$root['sql'] = $sql;
// 			if($GLOBALS['db']->getOne($sql)>0)
// 			{
// 				$root['allow_dp'] = 1;
// 			}

			$dp_status = check_dp_status($user_id,array("deal_id"=>$deal_id,"youhui_id"=>$youhui_id,"event_id"=>$event_id,"location_id"=>$location_id));
		
			if($dp_status['status'])
				$root['allow_dp'] = 1;
		}
		
		
		$root['type'] = $type;
		$root['id']=$id;
		$root['page_title']="点评列表";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
<?php
class merchantitem
{
	public function index()
	{
		//print_r($GLOBALS['request']);
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);

		$id = intval($GLOBALS['request']['id']);
		$city_id = intval($GLOBALS['request']['city_id']);
		
		$act_2 = $GLOBALS['request']['act_2'];//子操作 空:没子操作; dz:设置打折提醒
		if ($act_2 != '' && $user_id == 0){
			$root['act_2'] = $act_2;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			output($root);
		}
		
		
		$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		$pi = 3.14159265;  //圆周率
		$r = 6378137;  //地球平均半径(米)
	
		
		
		$sql = "select a.id,a.name,a.avg_point,a.address,a.api_address,a.supplier_id,a.tel,a.dp_count,a.avg_point,a.supplier_id as brand_id,a.brief,a.preview as logo,a.xpoint,a.ypoint,a.route,a.youhui_count,a.event_count,(select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.supplier_location_id = a.id and dp.status = 1) as comment_count, c.name as city_name, 
		(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((a.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((a.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (a.xpoint * $pi) / 180 ) ) * $r) as distance 
		  from ".DB_PREFIX."supplier_location as a ".			 			   
					   " left outer join ".DB_PREFIX."deal_city as c on c.id = a.city_id ".
						"where a.id = $id ";	
		
		//file_put_contents(APP_ROOT_PATH. "sjmapi/log/sql_".strftime("%Y%m%d%H%M%S",time()).".txt",$sql);
		$list = $GLOBALS['db']->getRow($sql);
		
		$root = m_merchantItem($list);
		
		//is_auto_order 1:手机自主下单;消费者(在手机端上)可以直接给该门店支付金额
		$sql = "select is_auto_order from  ".DB_PREFIX."supplier_location where id = ".$id;
		$is_auto_order = $GLOBALS['db']->getOne($sql);
		$root['is_auto_order'] = intval($is_auto_order); 
		//$root['is_auto_order'] = 0;
		
		//其它门店
		$sql = "select a.id,a.name,a.avg_point,a.address,a.api_address,a.supplier_id,a.tel,a.dp_count,a.avg_point,a.supplier_id as brand_id,a.brief,a.preview as logo,a.xpoint,a.ypoint,a.route,a.youhui_count,a.event_count,(select count(*) from ".DB_PREFIX."supplier_location_dp as dp where dp.supplier_location_id = a.id and dp.status = 1) as comment_count, c.name as city_name,
		(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((a.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((a.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (a.xpoint * $pi) / 180 ) ) * $r) as distance
		from ".DB_PREFIX."supplier_location as a ".
				" left outer join ".DB_PREFIX."deal_city as c on c.id = a.city_id ".
						"where a.id != $id and a.supplier_id =".intval($root['supplier_id']);		
		//$root['ss'] = $sql;
		$other_supplier_location = $GLOBALS['db']->getAll($sql);
		if ($other_supplier_location === false){
			$root['other_supplier_location'] = array();
		}else{
			foreach($other_supplier_location as $k=>$v)
			{
				$other_supplier_location[$k]['logo'] = get_abs_img_root($v['logo']);
			}
			$root['other_supplier_location'] = $other_supplier_location;
		}
		
		
		
	
		/*门店团购*/		
		$time=get_gmtime();
		$t_where="where b.location_id=".$list['id']." and a.is_shop=0 and a.is_effect=1 and a.is_delete=0  and ((".$time.">= a.begin_time or a.begin_time = 0) and (".$time."< a.end_time or a.end_time = 0)) and a.buy_status <> 2";
		$g_where="where b.location_id=".$list['id']." and a.is_shop=1 and a.is_effect=1 and a.is_delete=0  and ((".$time.">= a.begin_time or a.begin_time = 0) and (".$time."< a.end_time or a.end_time = 0)) and a.buy_status <> 2";
		$y_where ="where a.is_effect = 1 and b.location_id=".$list['id']." and ((".$time.">= a.begin_time or a.begin_time = 0) and (".$time."< a.end_time or a.end_time = 0))";
		
		if($city_id==0)
		{
			require_once APP_ROOT_PATH."system/model/city.php";
			$city = City::locate_city();
			$city_id = $city['id'];
		}
		if($city_id>0)
		{			
			$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
			if($ids)
			{
				$t_where .= " and a.city_id in (".implode(",",$ids).")";
				$g_where .= " and a.city_id in (".implode(",",$ids).")";
				$y_where .= " and a.city_id in (".implode(",",$ids).")";
			}
		}
		
		$tuan_list=$GLOBALS['db']->getAll("select a.brief,a.auto_order,a.id,a.name,a.sub_name,a.origin_price,a.current_price,a.img,a.buy_count,a.discount from ".DB_PREFIX."deal as a left join ".DB_PREFIX."deal_location_link as b on b.deal_id=a.id ".$t_where." order by a.sort desc,a.id desc");
		$tuan_count=$GLOBALS['db']->getOne("select count(a.id) from ".DB_PREFIX."deal as a left join ".DB_PREFIX."deal_location_link as b on b.deal_id=a.id ".$t_where."");
		foreach($tuan_list as $k=>$v)
		{
			$tuan_list[$k]['origin_price']=round($v['origin_price'],2);
			$tuan_list[$k]['current_price']=round($v['current_price'],2);
			
			if($v['origin_price']>0&&floatval($v['discount'])==0) //手动折扣
			$tuan_list[$k]['save_price'] =round($v['origin_price'] - $v['current_price'],2);			
			else
			$tuan_list[$k]['save_price'] = round($v['origin_price']*((10-$v['discount'])/10),2);
			
			if($v['origin_price']>0&&floatval($v['discount'])==0)
			{
				$tuan_list[$k]['discount'] = round(($v['current_price']/$v['origin_price'])*10,2);					
			}
			$tuan_list[$k]['discount'] = round($tuan_list[$k]['discount'],2);
			
			$tuan_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'],140,85,0));
			if (empty($v['brief'])){
				$tuan_list[$k]['brief'] = $v['name'];
				$tuan_list[$k]['name'] = $v['sub_name'];
			}
			
		}
		
		if ($tuan_list === false){
			$root['tuan_list']= array();
		}else{
			$root['tuan_list']=$tuan_list;
		}
				
		$root['tuan_count']=$tuan_count;
		/*门店商品*/
		
		$goods_list=$GLOBALS['db']->getAll("select a.brief,a.id,a.is_hot,a.name,a.sub_name,a.origin_price,a.current_price,a.img,a.buy_count,a.discount from ".DB_PREFIX."deal as a left join ".DB_PREFIX."deal_location_link as b on b.deal_id=a.id ".$g_where." order by a.sort desc,a.id desc");

		foreach($goods_list as $k=>$v)
		{
			$goods_list[$k]['origin_price']=round($v['origin_price'],2);
			$goods_list[$k]['current_price']=round($v['current_price'],2);
			
			if($v['origin_price']>0&&floatval($v['discount'])==0) //手动折扣
			$goods_list[$k]['save_price'] =round($v['origin_price'] - $v['current_price'],2);			
			else
			$goods_list[$k]['save_price'] = round($v['origin_price']*((10-$v['discount'])/10),2);
			
			if($v['origin_price']>0&&floatval($v['discount'])==0)
			{
				$goods_list[$k]['discount'] = round(($v['current_price']/$v['origin_price'])*10,2);					
			}
			$goods_list[$k]['discount'] = round($goods_list[$k]['discount'],2);
			$goods_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'],140,85,0));
			
			if (empty($v['brief'])){
				$goods_list[$k]['brief'] = $v['name'];
				$goods_list[$k]['name'] = $v['sub_name'];
			}
		}
		
		
		if ($goods_list === false){
			$root['goods_list']= array();
		}else{
			$root['goods_list']=$goods_list;
		}
		
		/*优惠券*/
		
		$youhui_list=$GLOBALS['db']->getAll("select a.id,a.supplier_id as merchant_id,a.begin_time,a.youhui_type,a.total_num,a.end_time,a.name as title,a.list_brief as content,a.icon as merchant_logo,a.create_time,a.address as api_address,a.view_count,a.print_count,a.sms_count from ".DB_PREFIX."youhui as a left join ".DB_PREFIX."youhui_location_link as b on b.youhui_id=a.id ".$y_where." order by a.sort desc,a.id desc");
		$sql = "select a.id,a.supplier_id as merchant_id,a.begin_time,a.youhui_type,a.total_num,a.end_time,a.name as title,a.list_brief as content,a.icon as merchant_logo,a.create_time,a.address as api_address,a.view_count,a.print_count,a.sms_count from ".DB_PREFIX."youhui as a left join ".DB_PREFIX."youhui_location_link as b on b.youhui_id=a.id ".$y_where." order by a.sort desc,a.id desc";
		$youhui_list=$GLOBALS['db']->getAll($sql);

		foreach($youhui_list as $k=>$v)
		{
			$youhui_list[$k]['merchant_logo'] = get_abs_img_root(get_spec_image($v['merchant_logo'],140,85,0));
			$youhui_list[$k]['down_count'] = $youhui_list[$k]['sms_count'] + $youhui_list[$k]['print_count'];
			$youhui_list[$k]['begin_time']=to_date($v['begin_time'],"Y-m-d").'至'.to_date($v['end_time'],"Y-m-d");
		}
		
		if ($youhui_list === false){
			$root['youhui_list']= array();
		}else{
			$root['youhui_list']=$youhui_list;
		}
		
		
		
		
		/*门店评论*/
// 		$comment_list=$GLOBALS['db']->getAll("select a.id,a.content,a.point,a.avg_price,a.create_time,b.id as user_id,b.user_name from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$list['id']." and a.status = 1 limit 10");
// 		$comment_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$list['id']." and a.status = 1");
// 		foreach($comment_list as $k=>$v)
// 		{
// 			$comment_list[$k]['avg_price']=round($v['avg_price'],1);
// 			$comment_list[$k]['time']=pass_date($v['create_time']);
// 			$comment_list[$k]['width']=$v['avg_point'] > 0 ? ($v['avg_point'] / 5) * 90 : 0;
// 		}
		
// 		if ($comment_list === false){
// 			$root['comment_list']= array();
// 		}else{
// 			$root['comment_list']=$comment_list;
// 		}
		
// 		$root['comment_count']=$comment_count;

		require_once APP_ROOT_PATH."system/model/review.php";
		require_once APP_ROOT_PATH."system/model/user.php";
		$message_re = get_dp_list(3,$param=array("deal_id"=>0,"youhui_id"=>0,"event_id"=>0,"location_id"=>$list['id'],"tag"=>""),"","");
		
		foreach($message_re['list'] as $k=>$v)
		{
			$message_re['list'][$k]['width'] = ($v['point'] / 5) * 100;
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
		
		
		if ($act_2 == "dz"){
			
				$sql = "select uid from  ".DB_PREFIX."supplier_dy where uid = $user_id and supplier_id = ".$list['brand_id'];
				if (intval($GLOBALS['db']->getOne($sql) > 0)) {
					//已经设置打折提醒，则取消
					$sql = "delete from ".DB_PREFIX."supplier_dy where uid = $user_id and supplier_id = ".$list['brand_id'];
					$GLOBALS['db']->query($sql);
				}else{
					//没设置，则设置
					$merchant_dy = array(
						 						'uid' => $user_id,
						 						'supplier_id' => $list['brand_id']
					);
					$GLOBALS['db']->autoExecute(DB_PREFIX."supplier_dy", $merchant_dy, 'INSERT');
				}
		}
		
		$root['is_dy']= $GLOBALS['db']->getOne("select uid from ".DB_PREFIX."supplier_dy where uid = $user_id and supplier_id = ".$list['brand_id']." ");
		
		$root['return'] = 1;
		$root['user_login_status'] = 1;
		$root['page_title'] ="商家详情";
		output($root);
	}
}
?>
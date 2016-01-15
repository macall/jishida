<?php
class merchantlist
{
	public function index()
	{
		//print_r($GLOBALS['request']);
		$root = array();
		$root['return'] = 1;

		/*
		$email = addslashes($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = addslashes($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);		
		$user_id  = intval($user['id']);
		*/
		
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称	
		$city_id = intval($GLOBALS['request']['city_id']);
		$quan_id = intval($GLOBALS['request']['quan_id']);
		$cate_id = intval($GLOBALS['request']['cate_id']);
		$brand_id = intval($GLOBALS['request']['brand_id']);
		$keyword = strim($GLOBALS['request']['keyword']);
	
		

		$page = intval($GLOBALS['request']['page']); //分页
		
		$cata_type_id=intval($GLOBALS['request']['cata_type_id']);//商品二级分类
		$order_type=strim($GLOBALS['request']['order_type']);
		
		
		$is_auto_order = intval($GLOBALS['request']['is_auto_order']);//1:手机自主下单;消费者(在手机端上)可以直接给该门店支付金额
		
		
		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		
		$root['ypoint'] = $ypoint;
		$root['xpoint'] = $xpoint;
		//$root['sql'] = $sql;
		
		//if(!$city_id)
		//{
		//	$city = get_current_deal_city();
		//	$city_id = $city['id'];
		//}
		
		/*输出分类*/
		$bcate_list = getCateList();
		
		/*输出商圈*/
		$quan_list=getQuanList($city_id);
		
		$page=$page==0?1:$page;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
	
		
		if($xpoint>0)/*排序  default 智能（默认），nearby  离我  都 是按距离升级排序*/
		{		
			$pi = 3.14159265;  //圆周率
			$r = 6378137;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((a.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((a.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (a.xpoint * $pi) / 180 ) ) * $r) as distance ";
			if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
			$condition = " and  a.ypoint > $ybottom and a.ypoint < $ytop and a.xpoint > $xleft and a.xpoint < $xright ";
			$orderby = " order by distance asc ";
		}
		else
		{
			$field_append = "";
			$orderby = " order by a.sort desc,a.id desc";
		}
		/*排序*/
		if($GLOBALS['request']['from']=="wap"){
			if($order_type=='avg_point')
				$orderby= " order by  a.avg_point desc,a.sort desc,a.id desc ";
			else
				$orderby= " order by  a.id desc";
		}else{
			if($order_type=='avg_point')/*评价*/
				$orderby= " order by  a.avg_point desc,a.sort desc,a.id desc ";
		}
				
		$sql_count = "select count(*) from ".DB_PREFIX."supplier_location". " as a";
		

		$sql = "select a.id,a.deal_cate_id,a.name,a.avg_point,a.city_id, a.mobile_brief,a.mobile_brief as brief,a.tel,a.preview as logo,a.dp_count as comment_count,a.xpoint,a.ypoint,a.address, a.api_address, 0 as is_dy $field_append from   ".DB_PREFIX."supplier_location as a ";
		
		
		$where = "1 = 1 and a.deal_cate_id > 0 ";
				
		if($city_id>0)
			{			
				$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
				if($ids)
				{
				$where .= " and a.city_id in (".implode(",",$ids).")";
				}
			}
		
		if ($quan_id > 0){
			$sql_q = "select name from ".DB_PREFIX."area where id = ".intval($quan_id);
			$q_name = $GLOBALS['db']->getOne($sql_q);
			$q_name_unicode = str_to_unicode_string($q_name);
			$where .=" and (match(a.locate_match) against('".$q_name_unicode."' IN BOOLEAN MODE))";
			//$where .= " and a.locate_match = $quan_id";
		}
		

		if($cate_id>0)
		{			
			$deal_cate_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$cate_id);			
			$deal_cate_name_unicode = str_to_unicode_string($deal_cate_name);
			$where .= " and (match(a.deal_cate_match) against('".$deal_cate_name_unicode."' IN BOOLEAN MODE)) ";		
		}
		
		if($cate_id>0)
		{
			if($cata_type_id >0)
			{		
				$deal_type_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate_type where id = ".$cata_type_id);
				$deal_type_name_unicode = str_to_unicode_string($deal_type_name);
				
				$where .= " and (match(a.deal_cate_match) against('".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
			}
		}
		
		
		//1:手机自主下单;消费者(在手机端上)可以直接给该门店支付金额
		if ($is_auto_order > 0)
			$where .= " and a.is_auto_order = $is_auto_order";
		
		if ($brand_id > 0)
		  $where .= " and a.supplier_id = $brand_id";
				
		if($keyword){
	   		$GLOBALS['tmpl']->assign("keyword",$keyword);
	   		$kws_div = div_str($keyword);
			foreach($kws_div as $k=>$item)
			{
				$kw[$k] = str_to_unicode_string($item);
			}
			$kw_unicode = implode(" ",$kw);
			//有筛选
			$where .=" and (match(a.name_match,a.locate_match,a.deal_cate_match,a.tags_match) against('".$kw_unicode."' IN BOOLEAN MODE) or name like '%".$keyword."%')";
	  	
	   }
	   
	    $where.=$condition;
		$sql_count.=" where ".$where;
		$sql.=" where ".$where;
		$sql.=$orderby;
		$sql.=" limit ".$limit;		
	
		$total = $GLOBALS['db']->getOne($sql_count);
		$page_total = ceil($total/$page_size);
		
		


		$root['sql'] = $sql;
		$list = $GLOBALS['db']->getAll($sql);
	
		$merchant_list = array();
		$l_ids=array();
		foreach($list as $item){			
			$item = m_merchantItem($item);			
			$merchant_list[] = $item;
			$l_ids[]=$item['id'];
		}
		/*区域*/
		$l_area=$GLOBALS['db']->getAll("select a.name as a_name,b.location_id from ".DB_PREFIX."area as a left join ".DB_PREFIX."supplier_location_area_link as b on a.id=b.area_id where b.location_id in(".implode(',',$l_ids).") and a.pid=0");
		$l_area_var=array();
		foreach($l_area as $k=>$v)
		{
			if(array_key_exists($v['location_id'],$l_area_var))
				$l_area_var[$v['location_id']] .="/".$v['a_name'];
			else
				$l_area_var[$v['location_id']] =$v['a_name'];
		}
		/*小分类*/
		$l_cate_type=$GLOBALS['db']->getAll("select a.name as ct_name,b.location_id from ".DB_PREFIX."deal_cate_type as a left join ".DB_PREFIX."deal_cate_type_location_link as b on a.id=b.deal_cate_type_id where b.location_id in(".implode(',',$l_ids).")");
		$l_cate_type_var=array();
		foreach($l_cate_type as $k=>$v)
		{
			if(array_key_exists($v['location_id'],$l_cate_type_var))
				$l_cate_type_var[$v['location_id']] .="/".$v['ct_name'];
			else
				$l_cate_type_var[$v['location_id']] =$v['ct_name'];
		}
		foreach($merchant_list as $k=>$v)
		{
			if(array_key_exists($v['id'],$l_area_var))
				$merchant_list[$k]['l_area']=$l_area_var[$v['id']];
			else
				$merchant_list[$k]['l_area']='';
			
			if(array_key_exists($v['id'],$l_cate_type_var))
				$merchant_list[$k]['l_cate_type']=$l_cate_type_var[$v['id']];
			else
				$merchant_list[$k]['l_cate_type']='';
				
			$merchant_list[$k]['cate_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id=".$v['deal_cate_id']);
			$merchant_list[$k]['width'] = $v['avg_point'] > 0 ? ($v['avg_point'] / 5) * 75 : 0;
			$merchant_list[$k]['avg_point']=round($v['avg_point'],1);
			
			if (empty($merchant_list[$k]['mobile_brief'])){
				$merchant_list[$k]['mobile_brief'] = $merchant_list[$k]['l_cate_type'].' '.$merchant_list[$k]['l_area'];
			}
		}
		
		$root['item'] = $merchant_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		$root['bcate_list'] = $bcate_list;
		$root['quan_list'] = $quan_list;
		$root['city_id']=$city_id;
		$root['city_name']=$city_name;
		$root['is_auto_order']=$is_auto_order;
		//$root['email']=$email;
		if ($is_auto_order == 1){
			$root['page_title']='门店自主下单列表';
		}else{
			$root['page_title']='商家列表';
		}
		output($root);
	}
}
?>
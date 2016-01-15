<?php
class mapsearch{
	public function index(){
		
		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		$type = intval($GLOBALS['request']['type']); //-1:全部，0：优惠券；1：活动；2：团购；3：代金券；4：商家		
		//print_r($GLOBALS['request']);
		$pi = 3.14159265;  //圆周率
		$r = 6378137;  //地球平均半径(米)
		$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((s.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((s.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (xpoint * $pi) / 180 ) ) * $r) as distance ";
		$condition = "  ypoint > $ybottom and ypoint < $ytop and xpoint > $xleft and xpoint < $xright ";
		$limit = 10;
		
		
		$youhui_list = array();
		if($type==-1||$type==0)
		{
			//查优惠
			$now = get_gmtime();			
			$sql = "select y.id,y.name,y.icon,s.address,s.ypoint,s.xpoint,s.`name` as supplier_name  from ".DB_PREFIX."youhui y ";
			$sql .= " LEFT OUTER JOIN ".DB_PREFIX."youhui_location_link lk on lk.youhui_id = y.id ";
			$sql .= " LEFT OUTER JOIN ".DB_PREFIX."supplier_location s on s.id = lk.location_id ";
			$sql .= " where y.is_effect = 1 and y.begin_time<".$now." and (y.end_time = 0 or y.end_time > ".$now.") and s.ypoint > $ybottom and s.ypoint < $ytop and s.xpoint > $xleft and s.xpoint < $xright ";
			$sql.=" limit $limit ";			
			
			$root['sql_0'] = $sql;
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $item){	
				$item['icon'] = get_abs_img_root($item['icon']);	
				$item['type'] = 0;		
				$item['distance'] = 0;// round($item['distance']);
				$youhui_list[] = $item;
			}			
		}
		
		$event_list = array();
		if($type==-1||$type==1)
		{
			//查活动
			$now = get_gmtime();
			$sql = "select y.id,y.name,y.icon,y.address,y.ypoint,y.xpoint, s.`name` as supplier_name  from ".DB_PREFIX."event y ";			
			$sql .= " LEFT OUTER JOIN ".DB_PREFIX."supplier s on s.id = y.supplier_id ";
			$sql .= " where y.is_effect = 1 and y.event_begin_time<".$now." and (y.event_end_time = 0 or y.event_end_time > ".$now.") and y.ypoint > $ybottom and y.ypoint < $ytop and y.xpoint > $xleft and y.xpoint < $xright ";
			$sql.=" limit $limit ";
			$list = $GLOBALS['db']->getAll($sql);
			
			$root['sql_1'] = $sql;
			//$res = m_search_event_list($limit,0,0,$condition, " distance asc ",$field_append);	
			foreach($list as $item){	
				$item['icon'] = get_abs_img_root($item['icon']);	
				$item['type'] = 1;		
				$item['distance'] = 0;//round($item['distance']);
				$event_list[] = $item;
			}
		}
		
		$tuan_list = array();
		if($type==-1||$type==2)
		{
			//查团购
			//$res = m_get_deal_list($limit,0,0,array(DEAL_ONLINE),$condition,"distance asc",0,$field_append);
			
			$now = get_gmtime();
			$sql = "select y.id,y.sub_name as name,y.img as icon,s.address,s.ypoint,s.xpoint,s.`name` as supplier_name  from ".DB_PREFIX."deal y ";
			$sql .= " LEFT OUTER JOIN ".DB_PREFIX."deal_location_link lk on lk.deal_id = y.id ";
			$sql .= " LEFT OUTER JOIN ".DB_PREFIX."supplier_location s on s.id = lk.location_id ";
			$sql .= " where y.buy_type = 0 and y.buy_status <> 2 and y.publish_wait = 0 and y.is_shop = 0 and (".$now.">= y.begin_time or y.begin_time = 0) and (".$now."< y.end_time or y.end_time = 0) and s.ypoint > $ybottom and s.ypoint < $ytop and s.xpoint > $xleft and s.xpoint < $xright ";
			$sql .=" limit $limit ";
			
			$root['sql_2'] = $sql;
			$list = $GLOBALS['db']->getAll($sql);
			
			foreach($list as $item){	
				$item['icon'] = get_abs_img_root($item['icon']);	
				$item['type'] = 2;		
				$item['distance'] = round($item['distance']);
				$tuan_list[] = $item;
			}
		}
		
		$daijin_list = array();
		/*
		if($type==-1||$type==3)
		{
			//查代金
			$res = m_search_youhui_list($limit,0,$condition," distance asc ",0,$field_append);
			foreach($res['list'] as $item){	
				$item['icon'] = get_abs_img_root($item['icon']);	
				$item['type'] = 3;		
				$item['distance'] = round($item['distance']);
				$daijin_list[] = $item;
			}
		}
		*/
		
		$merchant_list = array();
		if($type==-1||$type==4)
		{
			//查商家
			$sql = "select id,supplier_id,name as supplier_name, mobile_brief as name, address,preview as icon,xpoint,ypoint from   ".DB_PREFIX."supplier_location  ";
			$sql .=" where is_effect=1 and ".$condition;
			$sql .=" limit ".$limit;
			$list = $GLOBALS['db']->getAll($sql);
			
			$root['sql_4'] = $sql;
			
			foreach($list as $item){	
				$item['icon'] = get_abs_img_root($item['icon']);	
				$item['type'] = 4;
				if (empty($item['name'])){
					$item['name'] = $item['address'];
				}
               // $item['id']=$item['supplier_id'];
				$item['distance'] = 0;//round($item['distance']);
				$merchant_list[] = $item;
			}
		}
		
		
		$root['youhui_list'] = $youhui_list;
		$root['event_list'] = $event_list;
		$root['tuan_list'] = $tuan_list;
		$root['daijin_list'] = $daijin_list;
		$root['merchant_list'] = $merchant_list;
		
		
// 		if($type==-1)
// 		{
// 			//$result_list = array_merge($youhui_list,$event_list,$tuan_list,$dianjin_list,$merchant_list); 
// 			$root['youhui_list'] = $youhui_list;
// 			$root['event_list'] = $event_list;
// 			$root['tuan_list'] = $tuan_list;
// 			$root['dianjin_list'] = $dianjin_list;
// 			$root['merchant_list'] = $merchant_list;
// 		}
// 		elseif($type=0)
// 		{
// // 			$result_list= $youhui_list;
// 			$root['youhui_list'] = $youhui_list;
// 		}
// 		elseif($type=1)
// 		{
// // 			$result_list= $event_list;
// 			$root['event_list'] = $event_list;
// 		}
// 		elseif($type=2)
// 		{
// // 			$result_list= $tuan_list;
// 			$root['tuan_list'] = $tuan_list;
// 		}
// 		elseif($type=3)
// 		{
// // 			$result_list= $dianjin_list;
// 			$root['dianjin_list'] = $dianjin_list;
// 		}
// 		elseif($type=4)
// 		{
// // 			$result_list= $merchant_list;
// 			$root['merchant_list'] = $merchant_list;
// 		}
// // 		if($result_list)
// // 		$root['item'] = $result_list;
// // 		else
// // 		$root['item'] = array();
		output($root);
		
	}
}
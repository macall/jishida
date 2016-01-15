<?php
class goodsdesc{
	public function index(){
		require_once APP_ROOT_PATH.'system/model/deal.php';
		/**

		 * has_attr: 0:无属性; 1:有属性
		 * 有商品属性在要购买时，要选择属性后，才能购买
		 
		 * change_cart_request_server: 
		 * 编辑购买车商品时，需要提交到服务器端，让服务器端通过一些判断返回一些信息回来(如：满多少钱，可以免运费等一些提示)
		 * 0:提交，1:不提交；
		 
		 * image_attr_a_id_{$attr_a_id} 图片列表，可以根据属性ID值，来切换图片列表;默认为：0
		 * limit_num: 库存数量
		 
		 */
		$id = intval($GLOBALS['request']['id']);//商品ID
		
		$user = $GLOBALS['user_info'];
		$user_id = intval($user['id']);
		$is_collect = 0;
		if ($user_id > 0){
			$sql2 = "select count(*) from ".DB_PREFIX."deal_collect where deal_id = ".$id." and user_id=".$user_id;
			if($GLOBALS['db']->getOne($sql2)>0){
				$is_collect = 1;
			}
		}
		
		
		$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		
		$item = get_deal($id);
		
		//$item['origin_price_format'] = format_price2($item['origin_price']);
		//$item['current_price_format'] = format_price2($item['current_price']);
		//$item['save_price_format'] = format_price2($item['save_price']);
		
		$root = getGoodsArray($item);
		
	
	
		//$message_re=m_get_message_list(3," m.rel_table = 'deal' and m.rel_id=".$id." and m.is_buy = 1");/*购买评论*/
		require_once APP_ROOT_PATH."system/model/review.php";
		require_once APP_ROOT_PATH."system/model/user.php";
		$message_re = get_dp_list(3,$param=array("deal_id"=>$id,"youhui_id"=>0,"event_id"=>0,"location_id"=>0,"tag"=>""),"","");
		
		foreach($message_re['list'] as $k=>$v)
		{
			$message_re['list'][$k]['width'] = ($v['point'] / 5) * 100;
			if($v['point']>0){
				$str="";
				for($i=1;$i<=$v['point'];$i++){
					$str.="★";
				}
				$message_re['list'][$k]['xing']=$str;
			}
			$message_re['list'][$k]['reply_time_format']=date("Y-m-d H:i:s",$v['reply_time']);
			
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
				
		$pi = 3.14159265;  //圆周率
		$r = 6378137;  //地球平均半径(米)
		$root['distance']=ACOS(SIN(($ypoint * $pi) / 180 ) *SIN(($item['supplier_address_info']['ypoint'] * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS(($item['supplier_address_info']['ypoint'] * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - ($item['supplier_address_info']['xpoint'] * $pi) / 180 ) ) * $r; 
	
		$root['return'] = 1;
		$images = array();
		//image_attr_1_id_{$attr_1_id} 图片列表，可以根据属性ID值，来切换图片列表
		$sql = "select img from ".DB_PREFIX."deal_gallery where deal_id = ".intval($id)." order by sort asc";
		$list = $GLOBALS['db']->getAll($sql);
	
		$gallery = array();
		$big_gallery = array();
		foreach($list as $k=>$image){
			$gallery[] = get_abs_img_root(get_spec_image($image['img'],460,280,1));
			$big_gallery[] = get_abs_img_root(get_spec_image($image['img'],0,0,0));	
		}
		$root['gallery'] = $gallery;
		$root['big_gallery'] = $big_gallery;

		

		//支持的门店列表;
		$sql = "select id,name,address,tel,xpoint,ypoint,supplier_id from ".DB_PREFIX."supplier_location where id in (select location_id from ".DB_PREFIX."deal_location_link where deal_id = ".$id.")";	
		$supplier_location_list = $GLOBALS['db']->getAll($sql);						
		foreach($supplier_location_list as $k=>$sl){
			$supplier_location_list[$k]['distance']=ACOS(SIN(($ypoint * $pi) / 180 ) *SIN(($sl['ypoint'] * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS(($sl['ypoint'] * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - ($sl['xpoint'] * $pi) / 180 ) ) * $r;
		}
		
		$root['supplier_location_list'] = $supplier_location_list;
		
		//其它团购
		//if($GLOBALS['request']['from']=="wap"){
						
			$time = get_gmtime();
			$time_condition = '  and is_shop = 0 ';
			$time_condition.=' and ('.$time.'>=begin_time or begin_time = 0 ) and ('.$time.'< end_time or end_time = 0) and buy_type<>2 and is_recommend=1';
			$time_condition.=' and id<>'.$id;
			/*
			if($item['cate_id']>0)
			{
				$ids = load_auto_cache("deal_sub_parent_cate_ids",array("cate_id"=>$item['cate_id']));
				$time_condition .= " and cate_id in (".implode(",",$ids).")";
			
			}
			
			if($item['city_id']==0)
			{
				$city = get_current_deal_city();
				$city_id = $city['id'];
				$time_condition .= " and city_id in (".implode(",",$item['city_id']).")";
			}
			if($item['city_id']>0)
			{
				$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$item['city_id']));
				if($ids)
				{
					$time_condition .= " and city_id in (".implode(",",$ids).")";
			
				}
			}
			*/
			$dealsql= "select * from ".DB_PREFIX."deal where is_effect = 1 and  is_delete = 0 ".$time_condition." order by sort desc,id desc limit 4";
			
			$deal_other = $GLOBALS['db']->getAll($dealsql);
			$root['deal_other']=$deal_other;
			
			/*门店评论*/
			/*
			$comment_list=$GLOBALS['db']->getAll("select a.id,a.content,a.point,a.avg_price,a.create_time,b.id as user_id,b.user_name from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$root['supplier_location_id']." and a.status = 1");
			$comment_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$root['supplier_location_id']." and a.status = 1");
			$count_point=0;
			foreach($comment_list as $k=>$v)
			{
				$comment_list[$k]['avg_price']=round($v['avg_price'],2);
				$comment_list[$k]['time']=pass_date($v['create_time']);
				$count_point+=$v['point'];
			}
			$root['comment_list']=$comment_list;
			$score=round($count_point/$comment_count,2);
			$width = $score > 0 ? ($score / 5) * 100 : 0;
			$root['point']=$score;
			$root['width']=$width;
			$root['comment_count']=$comment_count;
			*/
			/*商品评论*/
			/*
			$comment_list=$GLOBALS['db']->getAll("select a.id,a.content,a.point,a.avg_price,a.create_time,b.id as user_id,b.user_name from ".DB_PREFIX."deal as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$root['supplier_location_id']." and a.status = 1");
			$comment_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$root['supplier_location_id']." and a.status = 1");
			$count_point=0;
			foreach($comment_list as $k=>$v)
			{
				$comment_list[$k]['avg_price']=round($v['avg_price'],2);
				$comment_list[$k]['time']=pass_date($v['create_time']);
				$count_point+=$v['point'];
			}
			$root['comment_list']=$comment_list;
			$score=round($count_point/$comment_count,2);
			$width = $score > 0 ? ($score / 5) * 100 : 0;
			$root['point']=$score;
			$root['width']=$width;
			$root['comment_count']=$comment_count;
			*/
			
			
			//购买点评数量
			$comment_count = $root['message_count'];// intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_id = ".$id." and rel_table = 'deal' and pid = 0 and is_buy = 1"));
				
			$buy_dp_sum = 0.0;
			$buy_dp_group = $GLOBALS['db']->getAll("select point,count(*) as num from ".DB_PREFIX."message where rel_id = ".$id." and rel_table = 'deal' and pid = 0 and is_buy = 1 group by point");
			foreach($buy_dp_group as $dp_k=>$dp_v)
			{
				$star = intval($dp_v['point']);
				if ($star >= 1 && $star <= 5){					
					$buy_dp_sum += $star * $dp_v['num'];
				}
			}
			
			//点评平均分
			$score = round($buy_dp_sum / $comment_count,1);
			$width = $score > 0 ? ($score / 5) * 110 : 0;
			
			$root['point']=$score;
			$root['width']=$width;
			/*
			$root['comment_count']=$comment_count;
			
			$sql = "select m.id,m.content,m.create_time,m.update_time, m.point,m.admin_reply,m.admin_id,u.user_name from ".DB_PREFIX."message m left join fanwe_user u on u.id = m.user_id where m.rel_id = ".$id." and m.rel_table = 'deal' and m.pid = 0 and m.is_buy = 1  order by m.create_time desc limit 0,8";
			$comment_list = $GLOBALS['db']->getAll($sql);			
			$root['comment_list']=$comment_list;
			*/
			
			if($item['is_shop']==0){
				$root['page_title']="团购详情";
			}elseif($item['is_shop']==1){
				$root['page_title']="商品详情";
			}else{
				$root['page_title']="代金券详情";
			}						
		//}
		$root['is_collect']=$is_collect;
		$root['city_name']=$city_name;
		output($root);	
	}
}
?>
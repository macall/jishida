<?php
class index
{
	public function index()
	{
		if($GLOBALS['request']['from']=="wap"){		
		
			require_once "index_wap.action.php";			
			$obj = new index_wap;
			$obj->index();
		}else{
			$root = array();
			$root['return'] = 1;
		
			$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint
			$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint			
			$pi = 3.14159265;  //圆周率
			$r = 6378137;  //地球平均半径(米)
			$root['m_latitude'] = $ypoint;
			$root['m_longitude'] = $xpoint;
			
			$city_id = intval($GLOBALS['request']['city_id']);
			if ($city_id == 0)
				$city_id=intval($GLOBALS['city_id']);
			
			
			
			$adv_list = $GLOBALS['cache']->get("MOBILE_INDEX_ADVS_".intval($city_id));
			
			if($adv_list===false||true)
			{
						$advs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_adv where mobile_type = 0 and city_id in (0,".intval($city_id).") and status = 1 order by sort desc ");
						
						$adv_list = array();
						foreach($advs as $k=>$v)
						{
							$adv_list[$k]['id'] = $v['id'];
							$adv_list[$k]['name'] = $v['name'];
							//$adv_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'],640,120,1));
							$adv_list[$k]['img'] = get_abs_img_root($v['img']);
							$adv_list[$k]['type'] = $v['type'];
							$adv_list[$k]['data'] = $v['data'] = unserialize($v['data']);
							if($v['type'] == 1)
							{
								$tag_count = count($v['data']['tags']);
								$adv_list[$k]['data']['count'] = $tag_count;
							}
							
							if(in_array($v['type'],array(9,10,11,12,13,22))) //列表取分类ID
							{
								if($v['type']==9||$v['type']==12||$v['type']==13||$v['type']==22) //生活服务类
								{
									$adv_list[$k]['data']['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".intval($v['data']['cate_id']));								
								}
								elseif($v['type']==10)  //商城
								{
									$adv_list[$k]['data']['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."shop_cate where id = ".intval($v['data']['cate_id']));						
								}
								elseif($v['type']==11)  //活动
								{
									$adv_list[$k]['data']['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."event_cate where id = ".intval($v['data']['cate_id']));
								}
								$adv_list[$k]['data']['cate_name'] = $adv_list[$k]['data']['cate_name']?$adv_list[$k]['data']['cate_name']:"全部";
							}
							
							if ($adv_list[$k]['data'] === false){
								$adv_list[$k]['data'] = null;
							}
						}
						$GLOBALS['cache']->set("MOBILE_INDEX_ADVS_".intval($city_id),$adv_list,300);
			}
			$root['advs'] = $adv_list;
			
			$indexs_list = $GLOBALS['cache']->get("MOBILE_INDEX_INDEX_".intval($city_id));
			if($indexs_list===false||true)
			{
						$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type=0 and city_id in (0,".intval($city_id).") order by sort desc ");
						$indexs_list = array();
						foreach($indexs as $k=>$v)
						{
							$indexs_list[$k]['id'] = $v['id'];
							$indexs_list[$k]['name'] = $v['name'];
							$indexs_list[$k]['vice_name'] = $v['vice_name'];
							$indexs_list[$k]['desc'] = $v['desc'];
							$indexs_list[$k]['is_hot'] = $v['is_hot'];
							$indexs_list[$k]['is_new'] = $v['is_new'];
							$indexs_list[$k]['img'] = get_abs_img_root($v['img']);
							
							$indexs_list[$k]['type'] = $v['type'];
							$indexs_list[$k]['data'] = $v['data'] = unserialize($v['data']);
							if($v['type'] == 1)
							{
								$tag_count = count($v['data']['tags']);
								$indexs_list[$k]['data']['count'] = $tag_count;
							}
							if(in_array($v['type'],array(9,10,11,12,13,22))) //列表取分类ID
							{
								if($v['type']==9||$v['type']==12||$v['type']==13||$v['type']==22) //生活服务类
								{
									$indexs_list[$k]['data']['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".intval($v['data']['cate_id']));								
								}
								elseif($v['type']==10)  //商城
								{
									$indexs_list[$k]['data']['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."shop_cate where id = ".intval($v['data']['cate_id']));						
								}
								elseif($v['type']==11)  //活动
								{
									$indexs_list[$k]['data']['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."event_cate where id = ".intval($v['data']['cate_id']));
								}
								$indexs_list[$k]['data']['cate_name'] = $indexs_list[$k]['data']['cate_name']?$indexs_list[$k]['data']['cate_name']:"全部";
							}
							
							if ($indexs_list[$k]['data'] === false){
								$indexs_list[$k]['data'] = null;
							}
						}
						$GLOBALS['cache']->set("MOBILE_INDEX_INDEX_".intval($city_id),$indexs_list,300);
			}
			$root['indexs'] = $indexs_list;
			
			
			//推荐商家
			$indexs_supplier = $GLOBALS['cache']->get("MOBILE_INDEX_SUPPLIER_".intval($city_id));
			
			if($indexs_supplier === false)
			{
				
				$sql = "select id,name,preview from ".DB_PREFIX."supplier_location where is_recommend=1  and is_effect=1 ";
				if($city_id>0)
				{
					$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
					if($ids)
					{
						$sql .= " and city_id in (".implode(",",$ids).")";
					}
				}
				
				$sql .= '  order by sort desc limit 3';
				
				$indexs_supplier=$GLOBALS['db']->getAll($sql);
				foreach($indexs_supplier as $k=>$v){
					$indexs_supplier[$k]['preview']=get_abs_img_root(make_img($v['preview'],194,110,1));
				}
					
				$GLOBALS['cache']->set("MOBILE_INDEX_SUPPLIER_".intval($city_id),$indexs_supplier,300);
			}
			$root['supplier_list'] = $indexs_supplier;
			
			//推荐团购
			$indexs_deal = $GLOBALS['cache']->get("MOBILE_INDEX_DEAL_".intval($city_id));
			if($indexs_deal === false)
			{
			
				$now = get_gmtime();
				//buy_type 0普通团购;2在线订购;3秒杀抢团
				$sql = "select id,name,auto_order,sub_name,brief,cate_id,supplier_id,current_price,origin_price,img,begin_time,end_time,buy_type,buy_count ".
						",ypoint,xpoint, 0 as distance "
						." from ".DB_PREFIX."deal where buy_type = 0 and publish_wait = 0 and is_shop=0 and is_recommend=1  and is_effect=1 and buy_status!=2 and begin_time<".$now." and (end_time = 0 or end_time > ".$now.") ";
				
				if($city_id>0)
				{
					$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
					if($ids)
					{
						$sql .= " and city_id in (".implode(",",$ids).")";						
					}
				}
				
				$sql .= ' order by sort desc limit 10';
				
				$indexs_deal=$GLOBALS['db']->getAll($sql);				
				
				foreach($indexs_deal as $k=>$v){
					$indexs_deal[$k]['current_price']=round($v['current_price'],2);
					$indexs_deal[$k]['origin_price']=round($v['origin_price'],2);
				   //$indexs_deal[$k]['img']=get_abs_img_root(make_img($v['img'],108,67,1));
					$indexs_deal[$k]['img']=get_abs_img_root(make_img($v['img'],108,85,1));
					$indexs_deal[$k]['end_time_format']=to_date($v['end_time']);
					$indexs_deal[$k]['begin_time_format']=to_date($v['begin_time']);
					if (empty($v['brief']))
						$indexs_deal[$k]['brief'] = $v['name'];
				}
					
				$GLOBALS['cache']->set("MOBILE_INDEX_DEAL_".intval($city_id),$indexs_deal,300);
			}
			
			$root['deal_list'] = $indexs_deal;
			
			//推荐商品
			$indexs_supplier_deal = $GLOBALS['cache']->get("MOBILE_INDEX_SUPPLIER_DEAL_".intval($city_id));
			if($indexs_supplier_deal === false)
			{
				//buy_type = 0 普通商品;1积分商品
				$sql = "select id,name,is_hot,sub_name,brief,cate_id,supplier_id,current_price,origin_price,img,begin_time,end_time,buy_type,buy_count from ".DB_PREFIX."deal where buy_type = 0 and is_shop=1 and is_recommend=1  and is_effect=1  ";										

				$sql .= ' order by sort desc limit 10';
				$indexs_supplier_deal=$GLOBALS['db']->getAll($sql);
				
				foreach($indexs_supplier_deal as $k=>$v){
					//$indexs_supplier_deal[$k]['img']=get_abs_img_root(make_img($v['img'],310,262,1));
					$indexs_supplier_deal[$k]['img']=get_abs_img_root(make_img($v['img'],108,85,1));
					$indexs_supplier_deal[$k]['current_price']=round($v['current_price'],2);
					$indexs_supplier_deal[$k]['origin_price']=round($v['origin_price'],2);
					if (empty($v['brief']))
						$indexs_supplier_deal[$k]['brief'] = $v['name'];
				}
				$GLOBALS['cache']->set("MOBILE_INDEX_SUPPLIER_DEAL_".intval($city_id),$indexs_supplier_deal,300);
			}
			
			$root['supplier_deal_list'] = $indexs_supplier_deal;
			
			
			
			//推荐活动
			$indexs_event = $GLOBALS['cache']->get("MOBILE_INDEX_EVENT_".intval($city_id));
			if($indexs_event === false||true)
			{
				$now=get_gmtime();
				$sql = "select id,name,icon,event_begin_time,event_end_time"
						.",ypoint,xpoint, 0 as distance "
						." from ".DB_PREFIX."event where  is_effect=1 and event_begin_time<".$now." and (event_end_time = 0 or event_end_time > ".$now.") ";
				
				//is_recommend=1  and
				
				if($city_id>0)
				{
					$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
					if($ids)
					{
						$sql .= " and city_id in (".implode(",",$ids).")";
					}
				}
				
				$sql .= ' order by sort desc limit 10';
				
				$indexs_event=$GLOBALS['db']->getAll($sql);
				
				foreach($indexs_event as $k=>$v){
					$indexs_event[$k]['icon']=get_abs_img_root(make_img($v['icon'],580,215,1));
					$indexs_event[$k]['event_begin_time_format']= to_date($v['event_begin_time']);
					$indexs_event[$k]['event_end_time_format']= to_date($v['event_end_time']);
			
					$indexs_event[$k]['sheng_time_format']= to_date($v['event_end_time']-$now,"d天h小时i分");
				}
				$GLOBALS['cache']->set("MOBILE_INDEX_EVENT_".intval($city_id),$indexs_event,300);
			
			}
			$root['event_list'] = $indexs_event;
			
			
			//推荐优惠券
			$youhui_list=$GLOBALS['cache']->get("MOBILE_YOUHUI_LIST_".intval($city_id));
			if($youhui_list === false){
				$sql = "select id, supplier_id as merchant_id,description,begin_time,youhui_type,total_num,end_time,name as title,list_brief as content,icon as merchant_logo,create_time,xpoint,ypoint,address as api_address,icon as image_1 "
						.",0 as distance "
						." from ".DB_PREFIX."youhui";
			
				$now = get_gmtime();
				$where = " 1 = 1 and is_effect = 1 and begin_time<".$now." and (end_time = 0 or end_time > ".$now.") ";
			
				if(intval($city_id)>0)
				{
					$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>intval($city_id)));
					if($ids)
					{
						$where .= " and city_id in (".implode(",",$ids).")";
					}
				}
			
				$sql.=" where ".$where;
			
				$sql.=" order by sort limit 0,10";
			
				$youhui_list=$GLOBALS['db']->getAll($sql);
				//$root['youhui_list_sql'] = $sql;
				foreach($youhui_list as $k=>$v){
					//$youhui_list[$k]['image_1']=get_abs_img_root($v['image_1']);
					$youhui_list[$k]['image_1']=get_abs_img_root(make_img($v['image_1'],108,85,1));
					$youhui_list[$k]['merchant_logo']=$youhui_list[$k]['image_1'];
					$youhui_list[$k]['begin_time']=to_date($v['begin_time'],"Y-m-d");
				}
			
				$GLOBALS['cache']->set("MOBILE_YOUHUI_LIST_".intval($city_id),$youhui_list,300);
			}
			if ($youhui_list === false){
				$root['youhui_list'] = array();
			}else{
				$root['youhui_list'] = $youhui_list;
			}
			
			
			
			/*首页推荐分类*/
			$indexs_cate = $GLOBALS['cache']->get("MOBILE_INDEX_QUAN_".intval($city_id));
			if($indexs_cate === false)
			{
				$indexs_cate=$GLOBALS['db']->getAll("select id,name,icon_img,recommend from ".DB_PREFIX."deal_cate where recommend=1 and is_delete=0 and is_effect=1 and pid=0 order by sort desc limit 7");
				if($indexs_cate)
				{
					foreach($indexs_cate as $k =>$v)
					{
						$indexs_cate[$k]['icon_img']=get_abs_img_root($v['icon_img']);
					}
				}else
				{
					$indexs_cate=array();
				}
				
			}
			
			if ($indexs_cate === false){
				$root['cates'] = array();
			}else{
				$root['cates'] = $indexs_cate;
			}			
			
			$root['city_id'] = $city_id;
			output($root);
		}
	}
}
?>
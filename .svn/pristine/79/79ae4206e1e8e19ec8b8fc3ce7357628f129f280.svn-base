<?php
class index_wap
{
	public function index()
	{
		
		
		$root = array();
		$root['return'] = 1;
		
		$city_id =intval($GLOBALS['request']['city_id']);
		$city_name =strim($GLOBALS['request']['city_name']);	

		$root['city_id']=$city_id;
		$root['city_name']=$city_name;
		$adv_list = $GLOBALS['cache']->get("WAP_INDEX_ADVS_".intval($city_id));

		//广告列表
		if($adv_list===false)
		{
								
			$sql = " select * from ".DB_PREFIX."m_adv where mobile_type = 1 and city_id in (0,1,".intval($city_id).") and status = 1 order by sort desc ";			
			$advs = $GLOBALS['db']->getAll($sql);
			
			
			$adv_list = array();
			foreach($advs as $k=>$v)
			{
				$adv_list[$k]['id'] = $v['id'];
				$adv_list[$k]['name'] = $v['name'];
				$adv_list[$k]['img'] = get_abs_img_root($v['img']);//get_abs_img_root(get_spec_image($v['img'],640,100,0));
				//$adv_list[$k]['img2'] = get_spec_image($v['img'],640,100,1);
				$adv_list[$k]['type'] = $v['type'];
				$adv_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				
				$adv_list[$k]['url'] = getWebAdsUrl($v['type'],$v['data']);
			}
			$GLOBALS['cache']->set("WAP_INDEX_ADVS_".intval($city_id),$adv_list,300);
		}
		$root['advs'] = $adv_list;
		//$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
		//$root['get_domain'] = $domain;
		//output($root);
		
		//首页菜单列表
		$indexs_list = $GLOBALS['cache']->get("WAP_INDEX_INDEX_".intval($city_id));
		if($indexs_list===false)
		{
			$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 1 and city_id in (0,".intval($city_id).") order by sort desc limit 0,7");
			$indexs_list = array();
			foreach($indexs as $k=>$v)
			{
				$indexs_list[$k]['id'] = $v['id'];
				$indexs_list[$k]['name'] = $v['name'];
				$indexs_list[$k]['icon_name'] = $v['vice_name'];//图标名 http://fontawesome.io/icon/bars/
				$indexs_list[$k]['color'] = $v['desc'];//颜色
				$indexs_list[$k]['img'] = get_abs_img_root($v['img']);
				/*
				$indexs_list[$k]['is_hot'] = $v['is_hot'];
				$indexs_list[$k]['is_new'] = $v['is_new'];
				$indexs_list[$k]['img'] = get_abs_img_root($v['img']);
					
				$indexs_list[$k]['type'] = $v['type'];
				$indexs_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				*/
				
				$indexs_list[$k]['url'] = getWebAdsUrl($v['type'],unserialize($v['data']));
			}
			
			if (count($indexs_list) == 7){
				//更多			
				$more = array();
				$more['id'] = 0;
				$more['name'] = '更多';
				$more['icon_name'] = "fa fa-bars";//http://fontawesome.io/icon/bars/
				$more['color'] = '#45d3bf';
				$url = get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=indexs_more";		
				$more['url'] = str_replace("sjmapi", "wap", $url);
				$indexs_list[count($indexs_list)] = $more;
			}
			
			$GLOBALS['cache']->set("WAP_INDEX_INDEX_".intval($city_id),$indexs_list,300);
		}
		
		$root['indexs'] = $indexs_list;		


		
		
		//推荐商家
		$indexs_supplier = $GLOBALS['cache']->get("WAP_INDEX_SUPPLIER_".intval($city_id));
	
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
				$indexs_supplier[$k]['preview']=get_abs_img_root(get_spec_image($v['preview'],194,118,1));
			}
			
			$GLOBALS['cache']->set("WAP_INDEX_SUPPLIER_".intval($city_id),$indexs_supplier,300);
		}
		$root['supplier_list'] = $indexs_supplier;
		
		//推荐团购
		$indexs_deal = $GLOBALS['cache']->get("WAP_INDEX_DEAL_".intval($city_id));
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
				$indexs_deal[$k]['img']=get_abs_img_root(get_spec_image($v['img'],140,85,1));
				$indexs_deal[$k]['end_time_format']=to_date($v['end_time']);
				$indexs_deal[$k]['begin_time_format']=to_date($v['begin_time']);
				
				if (empty($v['brief'])){
					$indexs_deal[$k]['brief'] = $v['name'];
					$indexs_deal[$k]['name'] = $v['sub_name'];
				}
			}
			
			$GLOBALS['cache']->set("WAP_INDEX_DEAL_".intval($city_id),$indexs_deal,300);
		}
	
		$root['deal_list'] = $indexs_deal;
		
		//推荐商品
		$indexs_supplier_deal = $GLOBALS['cache']->get("WAP_INDEX_SUPPLIER_DEAL_".intval($city_id));
		if($indexs_supplier_deal === false)
		{
			
			//buy_type = 0 普通商品;1积分商品
			$sql = "select id,name,is_hot,sub_name,brief,cate_id,supplier_id,current_price,origin_price,img,begin_time,end_time,buy_type,buy_count from ".DB_PREFIX."deal where buy_type = 0 and is_shop=1 and is_recommend=1  and is_effect=1  ";
			
			
			$sql .= ' order by sort desc limit 10';
			$indexs_supplier_deal=$GLOBALS['db']->getAll($sql);
			
			foreach($indexs_supplier_deal as $k=>$v){
				$indexs_supplier_deal[$k]['img']=get_abs_img_root(get_spec_image($v['img'],140,85,1));
				$indexs_supplier_deal[$k]['current_price']=round($v['current_price'],2);
				$indexs_supplier_deal[$k]['origin_price']=round($v['origin_price'],2);
				
				if (empty($v['brief'])){
					$indexs_supplier_deal[$k]['brief'] = $v['name'];
					$indexs_supplier_deal[$k]['name'] = $v['sub_name'];
				}				
			}			
			$GLOBALS['cache']->set("WAP_INDEX_SUPPLIER_DEAL_".intval($city_id),$indexs_supplier_deal,300);
		}		
		
		$root['supplier_deal_list'] = $indexs_supplier_deal;

//10个商品
			$allgoodslist = $GLOBALS['cache']->get("goodslist_".intval($city_id));
			if($allgoodslist === false)
			{
				//buy_type = 0 普通商品;1积分商品
				$sql = "select id,name,is_hot,sub_name,brief,cate_id,supplier_id,current_price,origin_price,img,begin_time,end_time,buy_type,buy_count,description from ".DB_PREFIX."deal where buy_type = 0 and is_shop=1 and is_recommend=1  and is_effect=1  ";										

				$sql .= ' order by sort desc limit 10';
				$allgoodslist=$GLOBALS['db']->getAll($sql);
				
				foreach($allgoodslist as $k=>$v){
					//$allgoodslist[$k]['img']=get_abs_img_root(make_img($v['img'],310,262,1));
					$allgoodslist[$k]['img']=get_abs_img_root(make_img($v['img'],108,85,1));
					$allgoodslist[$k]['current_price']=round($v['current_price'],2);
					$allgoodslist[$k]['origin_price']=round($v['origin_price'],2);
					if (empty($v['brief']))
						$allgoodslist[$k]['brief'] = $v['name'];
				}
				$GLOBALS['cache']->set("goodslist_".intval($city_id),$allgoodslist,300);
			}
			
			$root['allgoodslistallgoodslist'] = $indexs_supplier_deal;

		//推荐活动
		$indexs_event = $GLOBALS['cache']->get("WAP_INDEX_EVENT_".intval($city_id));
		if($indexs_event === false)
		{
			$now=get_gmtime();
				$sql = "select id,name,icon,event_begin_time,event_end_time"
						.",ypoint,xpoint, 0 as distance "
						." from ".DB_PREFIX."event where is_recommend=1  and is_effect=1 and event_begin_time<".$now." and (event_end_time = 0 or event_end_time > ".$now.") ";
				
				
				
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
				$indexs_event[$k]['icon']=get_abs_img_root(get_spec_image($v['icon'],300,182,1));
				$indexs_event[$k]['event_begin_time_format']= to_date($v['event_begin_time']);
				$indexs_event[$k]['event_end_time_format']= to_date($v['event_end_time']);
				
				$indexs_event[$k]['sheng_time_format']= to_date($v['event_end_time']-$now,"d天h小时i分");
			}
			$GLOBALS['cache']->set("WAP_INDEX_EVENT_".intval($city_id),$indexs_event,300);

		}
		$root['event_list'] = $indexs_event;
				

		//推荐优惠券
		$youhui_list=$GLOBALS['cache']->get("WAP_YOUHUI_LIST_".intval($city_id));
		if($youhui_list === false){
			$sql = "select id, supplier_id as merchant_id,description,begin_time,youhui_type,total_num,end_time,name as title,list_brief as content,icon as merchant_logo,create_time,xpoint,ypoint,address as api_address,icon as image_1 from ".DB_PREFIX."youhui";
				
			$now = get_gmtime();
			$where = "1 = 1 and is_effect = 1 and begin_time<".$now." and (end_time = 0 or end_time > ".$now.") ";
		
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
				$youhui_list[$k]['image_1']=get_abs_img_root(get_spec_image($v['image_1'],140,85,1));
				$youhui_list[$k]['down_count'] = $youhui_list[$k]['sms_count'] + $youhui_list[$k]['print_count'];
				$youhui_list[$k]['begin_time']=to_date($v['begin_time'],"Y-m-d").'至'.to_date($v['end_time'],"Y-m-d");
			}
				
			$GLOBALS['cache']->set("WAP_YOUHUI_LIST_".intval($city_id),$youhui_list,300);
		}
		
		$root['youhui_list'] = $youhui_list;
				
		//推荐商城
		$indexs_shop_cate=$GLOBALS['cache']->get("WAP_INDEX_SHOP_CATE_".intval($city_id));
		if($indexs_shop_cate === false)
		{
			$indexs_shop_cate=$GLOBALS['db']->getAll("select id,name,cate_img from ".DB_PREFIX."shop_cate where recommend=1  and is_effect=1 and pid=0  order by sort desc limit 6");
		
			foreach($indexs_shop_cate as $k=>$v){
				$indexs_shop_cate[$k]['cate_img']=get_abs_img_root($v['cate_img']);
			}
			
			$GLOBALS['cache']->set("WAP_INDEX_SHOP_CATE_".intval($city_id),$indexs_shop_cate,300);
		}		
		$root['shop_cate_list'] = $indexs_shop_cate;
		
		
		
		
		/*首页推荐分类*/
		$indexs_cate = $GLOBALS['cache']->get("WAP_INDEX_QUAN_".intval($city_id));
		
		if($indexs_cate === false)
		{
			$indexs_cate=$GLOBALS['db']->getAll("select id,name,icon_img from ".DB_PREFIX."deal_cate where recommend=1 and is_delete=0 and is_effect=1 and pid=0 order by sort desc limit 4");
			foreach($indexs_cate as $k=>$v){
				$indexs_cate[$k]['icon_img']=get_abs_img_root($v['icon_img']);
			}
				
			$GLOBALS['cache']->set("WAP_INDEX_QUAN_".intval($city_id),$indexs_cate,300);
		}
		$root['cates'] = $indexs_cate;
		
		/*关键字*/
		$indexs_cate_type=$GLOBALS['cache']->get("WAP_INDEX_CATE_TYPE_".intval($city_id));
		if($indexs_cate_type=== false){
			$cate_type_list=$GLOBALS['db']->getAll("select dct.id,dct.name,dctl.cate_id as pid from ".DB_PREFIX."deal_cate_type as dct left join ".DB_PREFIX."deal_cate_type_link as dctl on dctl.deal_cate_type_id=dct.id where dct.is_recommend=1 order by sort desc,id desc limit 8");
				
				
			$GLOBALS['cache']->set("WAP_INDEX_CATE_TYPE_".intval($city_id),$cate_type_list,300);
		}
		$root['cate_type_list'] = $cate_type_list;
		
		$root['page_title'] = $GLOBALS['m_config']['program_title'];
		output($root);
	}
}
?>
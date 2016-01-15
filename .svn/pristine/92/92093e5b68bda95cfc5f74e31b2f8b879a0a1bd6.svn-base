<?php
class index2
{
	public function index2()
	{
		$quan_id = intval($GLOBALS['request']['quan_id']);//商圈ID
		$city_id=intval($GLOBALS['request']['city_id']);
		$city_name = strim($GLOBALS['request']['city_name']);//城市分类ID
		//print_r($GLOBALS['request']);
		$root = array();
		$root['return'] = 1;
		$city_list=$GLOBALS['db']->getAll("select *from ".DB_PREFIX."deal_city where is_effect=1 and is_delete=0 order by id asc");
			
		$root['city_list']=$city_list;
		if($city_id >0)
		{
			/*当前城市商圈*/
			$indexs_quan = $GLOBALS['cache']->get("MOBILE_INDEX2_QUAN_".intval($GLOBALS['city_id']));
			if($indexs_quan === false)
			{
				$indexs_quan=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."area where pid=0 and city_id=".$city_id." order by sort desc");
			}
			
			if($indexs_quan)
				$root['quans'] = $indexs_quan;
			else
				$root['quans'] = array();
				
			$root['quan_id'] = $quan_id;
				/*城市列表*/
				
			/*当前城市热门团购(正在团购 的推荐商品)*/
			$time=get_gmtime();
			$condition=" is_effect = 1 and is_delete = 0 and is_shop = 0 and (".$time.">= begin_time or begin_time = 0) and (".$time."< end_time or end_time = 0) and buy_status <> 2";
			if($city_id==0)
				{
					$city = get_current_deal_city();
					$city_id = $city['id'];
				}
			if($city_id>0)
			{			
				$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
				if($ids)
				{
				$condition .= " and city_id in (".implode(",",$ids).")";
				}
			}
			if($quan_id > 0)
			{
				$ids = load_auto_cache("deal_quan_ids",array("quan_id"=>$quan_id));
				$quan_list = $GLOBALS['db']->getAll("select `name` from ".DB_PREFIX."area where id in (".implode(",",$ids).")");
				$unicode_quans = array();
				foreach($quan_list as $k=>$v){
					$unicode_quans[] = str_to_unicode_string($v['name']);
				}
				$kw_unicode = implode(" ", $unicode_quans);
				$condition .= " and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
			}
	
			$sql = "select id,name,sub_name,icon,origin_price,current_price,buy_count from ".DB_PREFIX."deal where ".$condition." order by is_recommend desc,sort desc limit 20";		
			$deal_list=$GLOBALS['db']->getAll($sql);
			
			$taday_begin=to_timespan(to_date(get_gmtime(),'Y-m-d'));
			$taday_end=$taday_begin*24*60*60;
			foreach($deal_list as $k=>$v)
			{
				$deal_list[$k]['current_price']=round($v['current_price'],2);
				$deal_list[$k]['origin_price']=round($v['origin_price'],2);
				$deal_list[$k]['icon']=get_abs_img_root(get_spec_image($v['icon'],300,181,0));
				if(($v['begin_time']>0) && ($taday_begin<$v['begin_time'] && $v['begin_time']<$taday_end))
				{
					$deal_list[$k]['is_taday']=1;
				}
				else if(($v['begin_time']==0) && ($taday_begin<$v['create_time'] && $v['create_time']<$taday_end))
				{
					$deal_list[$k]['is_taday']=1;
				}else
				{
					$deal_list[$k]['is_taday']=0;
				}
				
				unset($deal_list[$k]['begin_time'],$deal_list[$k]['create_time']);
			}
			if($deal_list)
				$root['deal_list'] =$deal_list;
			else
				$root['deal_list'] =array();
		}
		else
		{
			$root['quans'] = array();
			$root['quan_id'] = 0;
			$root['deal_list'] =array();
		}
		$root['city_name'] = $city_name;
		$root['page_title'] = "选择城市";
		output($root);
	}
}
?>
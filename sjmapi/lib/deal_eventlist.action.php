<?php
//参数:city_id, cate_id, page
class deal_eventlist
{
	public function index()
	{	
		$page = intval($GLOBALS['request']['page']);
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		if($page==0)
		$page = 1;
			
		$page_size = PAGE_SIZE;
	
		$limit = (($page-1)*$page_size).",".$page_size;	

		
		$event_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_event order by sort desc limit ".$limit);
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_event");
		foreach($event_list as $k=>$v)
		{
			$now=get_gmtime();
			$event_list[$k]['end_time'] = $v['event_end_time'];
			$event_list[$k]['url'] = url("shop","deal_event#show",array("id"=>$v['id']));
			$event_list[$k]['event_end_time'] = to_date($v['event_end_time'],'Y-m-d');
			$event_list[$k]['icon'] =get_abs_img_root(make_img($v['icon'],592,215,1));	
			$event_list[$k]['sheng_time_format']=to_date($v['event_end_time']-$now,"d天h小时i分");
			
		}
		$page_total = ceil($count/$page_size);
		$root = array();
		$root['return'] = 1;	
		$root['item'] = $event_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		$root['page_title'] = "活动专题";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
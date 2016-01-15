<?php
class deal_event_show
{
	public function index()
	{		
		$id = intval($_REQUEST['id']);
		
		//分页
				$page_size = 20;
				$page = intval($_REQUEST['p']);
				if($page==0)
				$page = 1;
				$limit = (($page-1)*$page_size).",".$page_size;	
		
				
				$deal_info = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal where deal_event_id = ".$id." order by sort desc limit ".$limit);
				$count= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where deal_event_id = ".$id);
				foreach($deal_info  as $k=>$v)
				{
					$deal_info[$k]['url'] = url("shop","goods",array("id"=>$v['id']));
					$deal_info[$k]['current_price'] = round($v['current_price'],2);
					$deal_info[$k]['origin_price'] = round($v['origin_price'],2);
					$deal_info[$k]['img'] = get_abs_img_root($v['img']);
				}
		
		
		$root = array();
		$root['return'] = 1;	
		$root['item'] = $deal_info;
		$root['page'] = array("page"=>$page,"page_total"=>ceil($count/$page_size));
		
		output($root);
	}
}
?>

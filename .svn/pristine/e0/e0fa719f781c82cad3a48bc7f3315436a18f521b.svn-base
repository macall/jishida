<?php
class group{
	public function index(){
		
		$page = intval($GLOBALS['request']['page']); //分页
		$page=$page==0?1:$page;
		$cate_id = intval($GLOBALS['request']['cate_id']);
		$city_id = intval($GLOBALS['request']['city_id']);
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		
		/*输出分类*/
		$bigcate_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."topic_group_cate where is_effect=1 order by sort asc");
		
		if($cate_id>0)
		$cate_condition = " and cate_id = ".$cate_id;
		
		$sql = " select * from ".DB_PREFIX."topic_group where is_effect = 1 $cate_condition order by sort desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."topic_group where is_effect = 1 $cate_condition ";
		
		$list = $GLOBALS['db']->getAll($sql);
		$count = $GLOBALS['db']->getOne($sql_count);
		foreach($list as $k=>$v){
			$list[$k]['icon']=get_abs_img_root(get_spec_image($v['icon'],300,181,0));
		}
		
		$page_total = ceil($count/$page_size);	
               
		
		
		$root = array();
		$root['bigcate_list']=$bigcate_list;
		$root['return'] = 1;	
		$root['email']=$email;
		$root['f_link_data']=get_link_list();	
		$root['item'] = $list;
		$root['page'] = array("page"=>$page,"page_total"=>ceil($res['count']/$page_size),"page_size"=>$page_size);
	
		$root['page_title']="小组";

		
		output($root);
	}
}
?>

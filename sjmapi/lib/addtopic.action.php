<?php
class addtopic{
	public function index(){
		$root = array();   
		$root['return'] = 1;  
		$group_id = intval($_REQUEST['id']);
		$page = intval($GLOBALS['request']['page']); //分页
		$page=$page==0?1:$page;
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);

		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		
		if($user_id==0)
		{
			$root['return']=0;
			$root['info']="请先登陆";
		}
		$group_id = intval($_REQUEST['id']);
		$group_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_group where is_effect = 1 and id = ".$group_id." limit ".$limit);
		if(!$group_item){
			$root['return']=0;
			$root['info']="不存在该主题";
		}

		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_group where is_effect = 1 and id = ".$group_id);		
			
		$root['id']=$group_id;
		$root['page'] = array("page"=>$page,"page_total"=>ceil($count/PAGE_SIZE),"page_size"=>PAGE_SIZE);
		$root['page_title']="发表主题";

		
		output($root);
	}
}
?>

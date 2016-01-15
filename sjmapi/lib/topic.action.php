<?php
// fwb add 2014-08-27
class topic{
	public function index(){
		$root = array();   
		$root['return'] = 1;  
		$group_id = intval($_REQUEST['id']);
		
		$email = addslashes($GLOBALS['request']['email']);//用户名或邮箱
		
		$pwd = addslashes($GLOBALS['request']['pwd']);//密码

		$condition = " and id = ".$group_id;

		$sql = "select * from ".DB_PREFIX."topic use index($sortkey) where is_effect = 1 and is_delete = 0  $condition";
	
		$list= $GLOBALS['db']->getAll($sql);
		foreach($list as $k=>$v){
			$list[$k]['group_title']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."topic_group where id=".$v['group_id']);
			$list[$k]['content']=str_replace("./public/","../public/",$v['content']);
			$id=$v['id'];
		}
		$root['item']=$list[0];	
		$root['email']=$email;
		
		$root['f_link_data']=get_link_list();	
		$root['id']=$group_id;
		$page = intval($GLOBALS['request']['page']); //分页
		$page=$page==0?1:$page;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		$reply=array();
		$reply = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."topic_reply where topic_id=".$id." and is_effect = 1 and is_delete = 0 order by create_time desc limit ".$limit);		
		$count= $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_reply where topic_id=".$id." and is_effect = 1 and is_delete = 0 ");
		$root["reply_list"]=$reply;
		
  
		$root['page_title']="小组主题";
		$root['page'] = array("page"=>$page,"page_total"=>ceil($count/$page_size),"page_size"=>$page_size);
		
		output($root);
	}
}
?>

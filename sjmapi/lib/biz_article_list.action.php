<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class biz_article_list
{
	public function index(){
		
		//分页
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
			$page = 1;
		
		$root = array();
		//$root['biz_article_cate_id'] = $GLOBALS['m_config']['biz_article_cate_id'];//我的消息，（文章分类ID）
		
		$cate_id = intval($GLOBALS['request']['cate_id']);
		if ($cate_id == 0){
			$cate_id =intval($GLOBALS['m_config']['biz_article_cate_id']);
		}
		
		$cate_id = 19;
		//分页
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$sql = "select id,title from ".DB_PREFIX."article where is_effect = 1 and cate_id = ".$cate_id." order by sort";
		$sql.=" limit ".$limit;
		
		$sql_count = "select count(*) from ".DB_PREFIX."article where is_effect = 1 and cate_id = ".$cate_id;
		

		$count = $GLOBALS['db']->getOne($sql_count);
		$list = $GLOBALS['db']->getAll($sql);
	
	
						
		$root['page'] = array("page"=>$page,"page_total"=>ceil($count/PAGE_SIZE),"page_size"=>PAGE_SIZE);	
		//$root['sql'] = $sql;
		$root['return'] = 1;
		$root['list'] = $list;
		
		output($root);		
	}
}
?>

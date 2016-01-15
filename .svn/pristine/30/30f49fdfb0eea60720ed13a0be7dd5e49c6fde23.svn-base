<?php
class myyouhuilist
{
	public function index()
	{

		$root = array();
		$root['return'] = 1;

		
		$page = intval($_FANWE['requestData']['page']); //分页
		$page=($page>0)?$page:1;
		$page=$page==0?1:$page;
		
		//检查用户,用户密码
		$user_return = $GLOBALS['user_info'];
                //var_dump($user_return);exit;
		$user = $user_return;
		$user_id  = intval($user['id']);
		//var_dump($user_id);exit;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		if(strim($GLOBALS['request']['from']=='wap')){
			$sql_count = "select count(*) from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as yh on yh.id = yl.youhui_id ";
			$sql ="select yh.id, yh.supplier_id as merchant_id,yh.end_time,yh.name as title,yh.brief as content,yh.icon as merchant_logo,yh.create_time,yh.xpoint,yh.ypoint,yh.icon as image_1,yl.youhui_sn as yl_sn,yl.create_time as yl_create_time,yl.confirm_time as yl_confirm_time,yh.begin_time as begin_time,yh.end_time as end_time,yl.confirm_time as confirm_time from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as yh on yh.id = yl.youhui_id  ";
		}else{
			$sql_count = "select count(*) from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as yh on yh.id = yl.youhui_id ";
			$sql ="select yh.id, yh.supplier_id as merchant_id,yh.name as title,yh.brief as content,yh.icon as merchant_logo,yh.create_time,yh.xpoint,yh.ypoint,yh.icon as image_1,yl.youhui_sn as yl_sn,yl.create_time as yl_create_time,yl.confirm_time as yl_confirm_time,yh.begin_time as begin_time,yh.end_time as end_time,yl.confirm_time as confirm_time from ".DB_PREFIX."youhui_log as yl left join ".DB_PREFIX."youhui as yh on yh.id = yl.youhui_id  ";
		}// fwb update 2014-08-27
		
		$where = " yl.user_id=$user_id";
		
		$sql_count.=" where ".$where;
                
		$sql.=" where ".$where;
		$sql.=" order by yl.create_time desc limit ".$limit;
			
		$total = $GLOBALS['db']->getOne($sql_count);
                
		$page_total = ceil($total/$page_size);
		//echo $sql;
		
		$list = $GLOBALS['db']->getAll($sql);
                //var_dump($list);exit;
		$youhui_list = array();
		
		foreach($list as $item){
			
		$youhui_list[] = m_youhuiLogItem($item);//			
			
		}

		$root['item'] = $youhui_list;
		if($GLOBALS['request']['from']=="wap"){
			$root['email']=$email;
			$root['f_link_data']=get_link_list();
		}// fwb add 2014-08-27
		$root['count'] = $total;
                //var_dump($root['item']);exit;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		$root['now'] = $now;
		
		output($root);
	}
}
?>
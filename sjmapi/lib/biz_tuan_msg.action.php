<?php
class biz_tuan_msg
{
	public function index()
	{

		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
		$type = strim($GLOBALS['request']['type']);//0:全部评价;1:差评;2:未读
		$deal_id = strim($GLOBALS['request']['deal_id']);//团购商品id
		
		if($supplier_id > 0)
		{	 		 	
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
					
			$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;
			
			$sql = "select m.id,m.content,m.create_time,m.update_time, m.point,m.admin_reply,m.admin_id,u.user_name from ".DB_PREFIX."message m left join fanwe_user u on u.id = m.user_id where m.rel_id = ".$deal_id." and m.rel_table = 'deal' and m.pid = 0 and m.is_buy = 1";
			$count_sql = "select count(*) from ".DB_PREFIX."message m left join fanwe_user u on u.id = m.user_id where m.rel_id = ".$deal_id." and m.rel_table = 'deal' and m.pid = 0 and m.is_buy = 1";
				
			
			//0:全部评价;1:差评;2:未读
			if ($type == 1){
				$sql .= " and m.point <= 2 ";
				$count_sql .= " and m.point <= 2 ";
			}else if ($type == 2){
				$sql .= " and m.is_read = 0 ";
				$count_sql .= " and m.is_read = 0 ";
			}
			
			$count = $GLOBALS['db']->getOne($count_sql);
				
			$sql .= " order by m.create_time desc limit ".$limit;
			$deal_list = $GLOBALS['db']->getAll($sql);
			
			//$root['sql'] = $sql;
			//echo $sql; exit;
			
			foreach($deal_list as $k=>$v)
			{				
				$deal_list[$k]['create_time_format'] = to_date($v['create_time'], 'Y-m-d H:i');	
				$deal_list[$k]['update_time_format'] = to_date($v['update_time'], 'Y-m-d H:i');
			}
			
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/PAGE_SIZE),"page_size"=>PAGE_SIZE);
			if ($deal_list == false || $deal_list == null){
				$deal_list = array();
			}
			//$root['count'] = count($deal_list);
			//$root['22'] = print_r($deal_list,1);
			$root['item'] = $deal_list;
			$root['return'] = 1;
		}else{			
			$root['return'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商户不存在或密码错误";
		}
		output($root);
	}
}
?>
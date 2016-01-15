<?php
class biz_coupon
{
	public function index()
	{

		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
		$deal_id = intval($GLOBALS['request']['deal_id']);//团购商品id
		
		if($supplier_id > 0)
		{	 		 	
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
					
			$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;
			
			$list = $GLOBALS['db']->getAll("select d.sn,d.password,d.confirm_time from ".DB_PREFIX."deal_coupon as d where d.confirm_account > 0 and d.is_valid = 1 and d.is_delete = 0 and d.deal_id = ".$deal_id." and d.supplier_id = ".$supplier_id." order by d.confirm_time desc limit ".$limit);
			
			foreach($list as $k=>$v)
			{				
				$list[$k]['confirm_time_format'] = to_date($v['confirm_time'], 'Y-m-d H:i');			
			}
			
			$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon as d where d.confirm_account > 0 and d.is_valid = 1 and d.is_delete = 0 and d.deal_id = ".$deal_id." and  d.supplier_id = ".$supplier_id);
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/PAGE_SIZE),"page_size"=>PAGE_SIZE);
		
			$root['item'] = $list;
			$root['return'] = 1;
		}
		output($root);
	}
}
?>
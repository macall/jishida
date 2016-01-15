<?php
class biz_rebates
{
	public function index()
	{

		require_once APP_ROOT_PATH."system/libs/user.php";
		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		$user_qrcode = strim($GLOBALS['request']['user_qrcode']);//会员二维码
		$page = intval($GLOBALS['request']['page']);//分页
		if($page==0)
			$page = 1;		
		$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;
						
		$keyword = strim($GLOBALS['request']['keyword']);//查询关键字
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
		
		if($supplier_id > 0)
		{	 		 	
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆

			$time = get_gmtime();
			
			$sql = "select id, user_name, user_qrcode, qrcode_end from ".DB_PREFIX."user where user_qrcode = '".$user_qrcode."' limit 1";
			$user =	$GLOBALS['db']->getRow($sql);
			
			if (!$user){
				$root['return'] = 0;
				$root['info'] = "会员卡不存在";
				output($root);
				
			}else{

				if ($user['qrcode_end'] < $time){
					$root['return'] = 0;
					$root['info'] = "会员卡已过期";
					output($root);
				}				
				
			}			
			
			$root['user_id'] = $user['id'];
			$root['user_qrcode'] = $user['user_qrcode'];
			$root['user_name'] = $user['user_name'];
				
			$sql = "select d.id,d.name,d.sub_name,d.icon, d.current_price,d.return_qrcode_money from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where ";

			$count_sql = "select count(d.id) from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where ";
			
			
			
			$condition = " d.is_shop in (0,1) and l.location_id in (".implode(",",$biz_user['location_ids']).") and d.is_delete = 0 and d.supplier_id = ".$supplier_id." and d.buy_type = 0 and d.publish_wait = 0  ";
		
			$condition .= " and ((".$time.">= d.begin_time or d.begin_time = 0) and (".$time."< d.end_time or d.end_time = 0) and d.buy_status <> 2) ";
						
			//$condition .= " and d.return_qrcode_money > 0";
			
			if($keyword)
			{
				$kws_div = div_str($keyword);
				foreach($kws_div as $k=>$item)
				{
					$kws[$k] = str_to_unicode_string($item);
				}
				$ukeyword = implode(" ",$kws);
				$condition .=" and (match(d.tag_match,d.name_match,d.locate_match,d.deal_cate_match) against('".$ukeyword."' IN BOOLEAN MODE) or d.name like '%".$keyword."%') ";
			}
			
			$sql .= $condition." order by d.id desc limit ".$limit;
			
			//$root['sql'] = $sql;
			
			$count_sql .= $condition;
			//print_r($biz_user);
			//echo $sql; exit;
			$deal_list = $GLOBALS['db']->getAll($sql);
			foreach($deal_list as $k=>$v)
			{
				$deal_list[$k]['icon']=get_abs_img_root(get_spec_image($v['icon'],360,288,0));			
				$deal_list[$k]['current_price_format'] = format_price($v['current_price']);
				$deal_list[$k]['return_qrcode_money_format'] = format_price($v['return_qrcode_money']);
				
				$deal_list[$k]['allow_edit_total_money'] = 1; //允许下单时，直接编辑商品总价
			}
			
			$deal_count = $GLOBALS['db']->getOne($count_sql);
					
	
			$root['page'] = array("page"=>$page,"page_total"=>ceil($deal_count/PAGE_SIZE),"page_size"=>PAGE_SIZE);
			if ($deal_list == false || $deal_list == null){
				$deal_list = array();
			}
			$root['item'] = $deal_list;
			$root['return'] = 1;
			$root['info'] = "商品列表";
		}else{			
			$root['return'] = 0;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['info'] = "商户不存在或密码错误";
		}
		output($root);
	}
}
?>
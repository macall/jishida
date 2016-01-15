<?php
class biz_tuan
{
	public function index()
	{

		require_once APP_ROOT_PATH."system/model/user.php";
		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
	
		$type = strim($GLOBALS['request']['type']);//0:消费者评价;1:消费统计
		
		if($supplier_id > 0)
		{	 		 	
			$root['user_login_status'] = 1;//用户登陆状态：1:成功登陆;0：未成功登陆
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
			$page = 1;
					
			$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;
			
			if ($type == 1)
				$sql = "select DISTINCT d.id,d.begin_time,d.current_price,d.sub_name,d.name,d.img from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where d.is_shop in (0,2) and l.location_id in (".implode(",",$biz_user['location_ids']).") and d.is_delete = 0 and d.supplier_id = ".$supplier_id." order by d.id desc limit ".$limit;
			else
				$sql = "select DISTINCT d.id,d.begin_time,d.current_price,d.sub_name,d.name,d.img from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where d.is_shop = 0 and l.location_id in (".implode(",",$biz_user['location_ids']).") and d.is_delete = 0 and d.supplier_id = ".$supplier_id." order by d.id desc limit ".$limit;
				
			//print_r($biz_user);
			//echo $sql; exit;
			$deal_list = $GLOBALS['db']->getAll($sql);
			
			foreach($deal_list as $k=>$v)
			{
				
				$deal_list[$k]['begin_time_format'] = to_date($v['begin_time'], 'Y-m-d');
				$deal_list[$k]['current_price_format'] = format_price($v['current_price']);
				//$deal_list[$k]['begin_time_format'] = to_date($v['begin_time'], 'Y-m-d');
				
				if ($type == 1){
					//已售团购券数量
					$deal_list[$k]['coupon_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon as dc where dc.deal_id = ".$v['id']." and dc.is_valid = 1 and dc.is_delete = 0 "));
					//已消费团购券数量
					$deal_list[$k]['confirm_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon as dc where dc.deal_id = ".$v['id']." and dc.is_valid = 1 and dc.is_delete = 0 and dc.confirm_account <> 0"));
				
					$deal_list[$k]['img'] = get_abs_img_root(get_spec_image($v['img'],160,160,0));		
					//$deal_list[$k]['img2'] = get_domain().APP_ROOT;
				}else{
					//购买点评数量
					$deal_list[$k]['buy_dp_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_id = ".$v['id']." and rel_table = 'deal' and pid = 0 and is_buy = 1"));
					
					//购买点评未读数
					$deal_list[$k]['buy_dp_no_read_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_id = ".$v['id']." and rel_table = 'deal' and pid = 0 and is_buy = 1 and is_read = 0"));

					//购买差评数
					$deal_list[$k]['buy_dp_low_point_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_id = ".$v['id']." and rel_table = 'deal' and pid = 0 and is_buy = 1 and point <= 2"));
						
					
					//星级点评数
					$deal_list[$k]['star_1'] = 0;
					$deal_list[$k]['star_2'] = 0;
					$deal_list[$k]['star_3'] = 0;
					$deal_list[$k]['star_4'] = 0;
					$deal_list[$k]['star_5'] = 0;

					$buy_dp_sum = 0.0;
					$buy_dp_group = $GLOBALS['db']->getAll("select point,count(*) as num from ".DB_PREFIX."message where rel_id = ".$v['id']." and rel_table = 'deal' and pid = 0 and is_buy = 1 group by point");
					foreach($buy_dp_group as $dp_k=>$dp_v)
					{
						$star = intval($dp_v['point']);
						if ($star >= 1 && $star <= 5){
							$deal_list[$k]['star_'.$star] = $dp_v['num'];
							
							$buy_dp_sum += $star * $dp_v['num'];
						}
					}
										
					//点评平均分
					$deal_list[$k]['buy_dp_avg'] = round($buy_dp_sum / $deal_list[$k]['buy_dp_count'],1);														
				}				
			}			
			
			$deal_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_location_link as l on l.deal_id = d.id where l.location_id in (".implode(",",$biz_user['location_ids']).") and d.is_delete = 0 and d.supplier_id = ".$supplier_id);
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($deal_count/PAGE_SIZE),"page_size"=>PAGE_SIZE);
			if ($deal_list == false || $deal_list == null){
				$deal_list = array();
			}
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
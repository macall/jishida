<?php
class supplier_message_list{
	public function index()
	{
		$merchant_id = intval($GLOBALS['request']['merchant_id']);/*商家ID*/
		$page = intval($GLOBALS['request']['page']);/*分页*/
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		
		$root = array();
		$root['return'] = 1;
		
		$page=$page==0?1:$page;
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		$supplier_locationinfo = $GLOBALS['db']->getRow("select name,id,new_dp_count_time from ".DB_PREFIX."supplier_location where id = ".$merchant_id);
			
		syn_supplier_locationcount($supplier_locationinfo);
		
		$condition = " dp.status = 1 and dp.supplier_location_id = ".$merchant_id." ";
		$sql_count = "select count(*) from ".DB_PREFIX."supplier_location_dp dp where ".$condition;
		$total = $GLOBALS['db']->getOne($sql_count);
	
		$page_total = ceil($total/$page_size);
		
		
		
		
		//$root['sql_count'] = $sql_count;
		
		$sql= "select dp.*,u.user_name from ".DB_PREFIX."supplier_location_dp as dp left outer join ".DB_PREFIX."user as u on u.id = dp.user_id  where ".$condition." order by dp.is_top desc, dp.create_time desc limit ".$limit;				
		$root['sql'] = $sql;
		$list = $GLOBALS['db']->getAll($sql);
		foreach($list as $k=>$v){
			$list[$k]['merchant_id']=$v['supplier_location_id'];
			$list[$k]['create_time_format']=getBeforeTimelag($v['create_time']);
			$list[$k]['width']=($v['point'] / 5) * 100;
		}
		
		//星级点评数
		$root['star_1'] = 0;
		$root['star_2'] = 0;
		$root['star_3'] = 0;
		$root['star_4'] = 0;
		$root['star_5'] = 0;
		$root['star_dp_width_1'] = 0;
		$root['star_dp_width_2'] = 0;
		$root['star_dp_width_3'] = 0;
		$root['star_dp_width_4'] = 0;
		$root['star_dp_width_5'] = 0;
		
		$buy_dp_sum = 0.0;
		
		$buy_dp_group = $GLOBALS['db']->getAll("select point,count(*) as num from ".DB_PREFIX."supplier_location_dp where supplier_location_id = ".$merchant_id." group by point");
		foreach($buy_dp_group as $dp_k=>$dp_v)
		{
			$star = intval($dp_v['point']);
			if ($star >= 1 && $star <= 5){
				$root['star_'.$star] = $dp_v['num'];				
				$buy_dp_sum += $star * $dp_v['num'];
				$root['star_dp_width_'.$star] = (round($dp_v['num']/ $total,1)) * 100;
			}
		}
		
		//点评平均分
		$root['buy_dp_sum']=$buy_dp_sum;
		$root['buy_dp_avg'] = round($buy_dp_sum / $total,1);
		$root['buy_dp_width'] = (round($buy_dp_sum / $total,1) / 5) * 100;		
		
	
		
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
	
	
		
		
		$root['message_list']=$list;
		$root['merchant_id']=$merchant_id;
		$root['page_title']="点评列表";
		
		output($root);
	}
}
?>
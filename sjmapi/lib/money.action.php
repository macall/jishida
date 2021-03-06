<?php
class voucher{
	public function index()
	{
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
			
		$root = array();
		$root['return'] = 1;		
		if($user_id>0)
		{
			$root['user_login_status'] = 1;		
			$status = intval($GLOBALS['request']['tag']);
	
			$page = intval($GLOBALS['request']['page']); //分页
			$page=$page==0?1:$page;
							
			$page_size = PAGE_SIZE;
			$limit = (($page-1)*$page_size).",".$page_size;

			$ext_condition = '';
			$now = get_gmtime();

			$sql = "select * from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id = et.id where e.user_id = ".$user_id." order by e.id desc limit ".$limit;
			$sql_count = "select count(*) from ".DB_PREFIX."ecv where user_id = ".$user_id;
			
			$list = $GLOBALS['db']->getAll($sql);
			//echo "select * from ".DB_PREFIX."deal_coupon where user_id = ".$user_id." and is_delete = 0 and is_valid = 1 ".$ext_condition." order by order_id desc limit ".$limit; exit;
			$count = $GLOBALS['db']->getOne($sql_count);
			
			$page_total = ceil($count/$page_size);
			
			$root['status']=$status;
			
			//$root = array();
			//$root['return'] = 1;
			
			//补充字段
			foreach($list as $k=>$v)
			{
				$list[$k]['createTime'] = "";
				if($v['end_time']>0)
				$list[$k]['endTime'] = to_date($list[$k]['end_time'],"Y-m-d");
				else 
				$list[$k]['endTime'] = "无限时";
				if($list[$k]['confirm_time']>0)
				$list[$k]['useTime'] = to_date($list[$k]['confirm_time'],"Y-m-d");
				else
				$list[$k]['useTime'] = "";
				$list[$k]['beginTime'] = "";
				//$list[$k]['dealIcon'] = get_abs_img_root(make_img($GLOBALS['db']->getOne("select img from ".DB_PREFIX."deal where id = ".$v['deal_id']),0));
				$list[$k]['dealIcon'] = get_abs_img_root(get_spec_image($GLOBALS['db']->getOne("select img from ".DB_PREFIX."deal where id = ".$v['deal_id']),160,160,1));
				if($v['end_time']>0)
				$list[$k]['lessTime'] = $v['end_time'] - get_gmtime();
				else
				$list[$k]['lessTime'] = "永久";
				
				$supplier_id = intval($GLOBALS['db']->getOne("select supplier_id from ".DB_PREFIX."deal where id = ".$v['deal_id']));
				$supplier_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".$supplier_id." and is_main = 1");
				
				$list[$k]['spName'] = $supplier_info['name']?$supplier_info['name']:"";
				$list[$k]['spTel'] = $supplier_info['tel']?$supplier_info['tel']:"";
				$list[$k]['spAddress'] = $supplier_info['address']?$supplier_info['address']:"";
		
				$list[$k]['couponSn'] = $v['sn'];
				$list[$k]['couponPw'] = $v['password'];				
				$list[$k]['qrcode'] = str_replace('sjmapi', '', get_domain().gen_qrcode($v['password']));
				
				//$list[$k]['qrcode'] = get_domain().gen_qrcode($v['password']);
				
				$list[$k]['dealName'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_order_item where id = ".$v['order_deal_id']);
				
				if(($v['end_time']==0||$v['end_time']>0&&$v['end_time']>time())&&($v['use_count']<$v['use_limit']||$v['use_limit']==0)){
					$list[$k]['is_use']=1;
				}
			}
			
			$root['item'] = $list;
			$root['count'] = $count;
			$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		
				
		}
		else
		{
			$root['user_login_status'] = 0;		
		}		
		$root['city_name']=$city_name;
		$root['page_title']='我的代金券';
		output($root);
	}
}
?>
<?php
class my_account
{
	public function index()
	{
		// 检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id = intval ( $user ['id'] );
		
		$root = array ();
		$root ['return'] = 1;
		if ($user_id > 0)
		{
			$root ['user_login_status'] = 1;
			$root ['user_name'] = $user ['user_name'];
			$root ['user_money'] = $user ['money'];
			$root ['user_money_format'] = format_price ( $user ['money'] ); // 用户金额
			$root ['user_score'] = $user ['score']; // 用户积分
			
			$coupon_count = $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "deal_coupon where user_id = " . $user_id . " and is_delete = 0 and is_valid = 1 " );
			$root ['coupon_count'] = $coupon_count;
			
			$youhui_count = $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "youhui_log as yl left join " . DB_PREFIX . "youhui as yh on yh.id = yl.youhui_id where yl.user_id=$user_id " );
			$root ['youhui_count'] = $youhui_count;
			
			$not_pay_order_count = $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "deal_order where user_id = " . $user_id . " and type = 0 and is_delete = 0 and pay_status <> 2" );
			$root ['not_pay_order_count'] = $not_pay_order_count;
			
			output ( $root );
		} else
		{
			$root ['user_login_status'] = 0;
		}
		
		output ( $root );
	}
}
?>
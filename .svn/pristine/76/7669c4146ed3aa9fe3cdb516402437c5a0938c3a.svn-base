<?php
class biz_rebates_order{
	public function index()
	{//file_put_contents("../auto_order.txt",print_r($GLOBALS['request']));
		require_once APP_ROOT_PATH."system/libs/user.php";
		$root = array();		
		
		$email = strim($GLOBALS['request']['biz_email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['biz_pwd']);//密码
		
		$user_qrcode = strim($GLOBALS['request']['user_qrcode']);//会员二维码
				
		$num = intval($GLOBALS['request']['num']);//购买数量
		if ($num == 0) $num = 1;
		
		$deal_id = intval($GLOBALS['request']['deal_id']);//购买的商品id
		
		$money = floatval($GLOBALS['request']['money']);//消费总金额;
		
		//检查用户,用户密码
		$biz_user = biz_check($email,$pwd);
		$supplier_id  = intval($biz_user['supplier_id']);
		
		if($supplier_id > 0)
		{
			
			
			$root['user_login_status'] = 1;
			
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
			
			$sql = "select d.id,d.name,d.sub_name,d.icon, d.buy_type, d.current_price, d.return_score, d.return_qrcode_money from ".DB_PREFIX."deal as d where d.supplier_id =".$supplier_id." and d.id = ". $deal_id;
			$deal = $GLOBALS['db']->getRow($sql);
			if (!$deal){
				$root['return'] = 0;
				$root['info'] = "商品不存在:".$deal_id;
				output($root);
			}
			
			$user_id = $user['id'];
			
			if ($money == 0){
				$money = $deal['current_price'] * $num;
			}
			
				$root['status'] = 1;
				
				//验证成功
				//开始生成订单
				$now = get_gmtime();
				$order['type'] = 0; //普通订单
				$order['user_id'] = $user_id;
				$order['user_name'] = $user['user_name'];
				$order['create_time'] = $now;
				$order['total_price'] = $money;  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费
				$order['pay_amount'] = $money;
				$order['pay_status'] = 2;  //线下支付，直接支付成功
				$order['delivery_status'] = 5;
				$order['order_status'] = 0;  //新单都为零， 等下面的流程同步订单状态
				$order['return_total_score'] = 0;  //结单后送的积分
				$order['return_total_money'] = $deal['return_qrcode_money'] * $num;  //结单后送的现金
				$order['memo'] = '会员卡扫描下单,金额为:'.format_price($money);

				$order['deal_total_price'] = $money;   //团购商品总价
				$order['discount_price'] = 0;
				$order['delivery_fee'] = 0;
				$order['ecv_money'] = 0;
				$order['account_money'] = 0;
				$order['ecv_sn'] = '';
				$order['delivery_id'] = 0;
				
				//线下支付
				$order['payment_id'] = 0;
				$order['payment_fee'] = 0;				
				$order['bank_id'] = 0;
				$order['is_auto_order'] = 1;//门店自主下单标识
				
				do
				{
					$order['order_sn'] = to_date(get_gmtime(),"Ymdhis").rand(10,99);									
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'INSERT','','SILENT');
					$order_id = intval($GLOBALS['db']->insert_id());
				}while($order_id==0);
				
				if ($order_id > 0){
										
					$deal_id = intval($deal['id']);
																		
					$goods_item = array();
					$goods_item['deal_id'] = $deal_id;
					$goods_item['number'] = $num;
					$goods_item['unit_price'] = $money;
					$goods_item['total_price'] = $money;
					$goods_item['name'] = $deal['name'];
					$goods_item['sub_name'] = $deal['sub_name'];
					$goods_item['attr'] = '0';
					$goods_item['verify_code'] = md5($deal_id."_0");;
					$goods_item['order_id'] = $order_id;
					$goods_item['return_score'] = $deal['return_score'];
					$goods_item['return_total_score'] = $order['return_total_score'];
					$goods_item['return_money'] = $deal['return_qrcode_money'];
					$goods_item['return_total_money'] = $order['return_total_money'];
					$goods_item['buy_type']	=	$deal['buy_type'];
					$goods_item['attr_str']	=	'';//$deal['attr_str'];					
					$goods_item['balance_unit_price'] = 0;
					$goods_item['balance_total_price'] = 0;
					
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_item",$goods_item,'INSERT','','SILENT');
					
					if($order['return_total_money']!=0)
					{
						$msg = sprintf($GLOBALS['lang']['ORDER_RETURN_MONEY'],$order['order_sn']);
						modify_account(array('money'=>$order['return_total_money'],'score'=>0),$order['user_id'],$msg);
					}
					
					if($order['return_total_score']!=0)
					{
						$msg = sprintf($GLOBALS['lang']['ORDER_RETURN_SCORE'],$order['order_sn']);
						modify_account(array('money'=>0,'score'=>$order['return_total_score']),$order['user_id'],$msg);
					}
					
					
					$sql = "update ".DB_PREFIX."deal set buy_count = buy_count + ".$num.",user_count = user_count + 1 where id=".$deal_id;					
					$GLOBALS['db']->query($sql); //增加商品的发货量
				}
				
				$root['order_id'] = $order_id;
				
				$root['info'] = '交易完成,共获得:'.format_price($order['return_total_money'])." 返利";
			
		}else{
			$root['user_login_status'] = 0;
			$root['info'] = '请先登陆';
		}
		
		output($root);
	}
}
?>
<?php
class pay_order{
	public function index(){
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);		
		
			
		$root = array();
		$root['return'] = 1;
		$root['page_title'] ='订单支付';
		$root['city_name']=$city_name;
		if($user_id>0)
		{
			$order_id = intval($GLOBALS['request']['order_id']);
			if ($order_id == 0){
				$payment_notice_sn = $GLOBALS['request']['out_trade_no'];			
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");				
				$order_id = intval($payment_notice['order_id']);
			}
			
			$root['user_login_status'] = 1;
			
			$root['pay_status'] = 0;//0:订单未收款(全额);1:订单已经收款(全额)
			$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where user_id = {$user_id} and id = ".$order_id);
			if (empty($order)){
				$root['pay_status'] = 1;
				$root['pay_info'] = '订单不存在.';
				$root['show_pay_btn'] = 0;
				output($root);		
			}
			
			if ($order['pay_status'] == 2){
				$root['pay_status'] = 1;
				$root['pay_code'] = '';//$pay['pay_code'];
				$root['order_id'] = $order_id;
				$root['order_sn'] = $order['order_sn'];				
				$root['pay_info'] = '订单已经收款.';
				$root['show_pay_btn'] = 0;
				
				//有团购券,再显示团购券列表
				$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where user_id = {$user_id} and order_id = ".$order_id);				
				
				$couponlist = array();
				foreach($list as $k=>$v)
				{						
					$couponlist[$k]['couponSn'] = $v['sn'];
					$couponlist[$k]['couponPw'] = $v['password'];				
					$couponlist[$k]['qrcode'] = str_replace('sjmapi', '', get_domain().gen_qrcode($v['password']));
				}
				$root['couponlist'] = $couponlist;
				
				output($root);
			}
			
			
			$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id=".intval($order['payment_id']));
			$pay_code = strtolower($payment_info['class_name']);
			/*online_pay 支付方式：1：在线支付；0：线下支付;2:手机wap;3:手机sdk */
			$online_pay = intval($payment_info['online_pay']);
			if($GLOBALS['request']['from']=="wap"){
				
				if (!($pay_code == 'wtenpay' || $pay_code == 'walipay' || $pay_code == 'mcod' || $pay_code == 'account' || $online_pay == 2 )){
					
					$root['return'] = 0;
					$root['pay_info'] = '手机版本不支付,无法在手机上支付.'.$pay_code;
					$root['show_pay_btn'] = 0;	
					output($root);
				}	
			}else{
				if (!($pay_code == 'malipay' || $pay_code == 'mtenpay' || $pay_code == 'mcod' || $pay_code == 'walipay' || $pay_code == 'wtenpay' || $online_pay == 2 || $online_pay == 3)){
				$root['return'] = 0;
				$root['pay_info'] = '手机版本不支付,无法在手机上支付.'.$pay_code;
				$root['show_pay_btn'] = 0;	
				output($root);
				}
			}// fwb update 2014-08-27
			
			//3. 相应的支付接口
			$pay_price = $order['total_price'] - $order['pay_amount'];
			if($payment_info&&$pay_price>0)
			{
				require_once APP_ROOT_PATH."system/model/cart.php";
				$payment_notice_id = make_payment_notice($pay_price,$order_id,$payment_info['id']);
				//创建支付接口的付款单
			}
			
			//创建了支付单号，通过支付接口创建支付数据
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			$pay = $payment_object->get_payment_code($payment_notice_id);
			
			$root['return'] = 1;
			$root['pay_code'] = $pay['pay_code'];
			$root['order_id'] = $order_id;
			$root['order_sn'] = $order['order_sn'];
			$root['show_pay_btn'] = 0;//0:不显示，支付按钮; 1:显示支付按钮
			$root['is_wap_url'] = $pay['is_wap_url'];//0:不显示，支付按钮; 1:显示支付按钮
			if($pay['wap_notify_url'])
				$root['wap_notify_url'] = $pay['wap_notify_url'];
			
			$root['is_wap'] = intval($pay['is_wap']);
			
			//支付接口支付 malipay,支付宝;mtenpay,财付通;mcod,货到付款/现金支付
			if($GLOBALS['request']['from']=="wap"){
				
				if ($root['is_wap'] == 1){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['pay_wap'] = $pay['notify_url'];
						
					if ($root['pay_money'] > 0){
						$root['show_pay_btn'] = 1;
					}				
				}else if ($pay['pay_code'] == 'mcod'){
					
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['mcod'] = $pay;
					
					$root['show_pay_btn'] = 0;
				}else{
					$root['return'] = 0;
					$root['pay_info'] = '手机版本不支付,无法在手机上支付.';
					$root['show_pay_btn'] = 0;
				}
				$root['payment_name']=strtolower($payment_info['name']);
				output($root);
			}else{
				if ($root['is_wap'] == 1){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['pay_wap'] = $pay['notify_url'];
						
					if ($root['pay_money'] > 0){
						$root['show_pay_btn'] = 1;
					}
				}else if ($pay['pay_code'] == 'malipay'){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['malipay'] = $pay;
					
					if ($root['pay_money'] > 0){
						$root['show_pay_btn'] = 1;
					}
				}else if ($pay['pay_code'] == 'mtenpay'){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['mtenpay'] = $pay;
					if ($root['pay_money'] > 0){
						$root['show_pay_btn'] = 1;
					}		
				}else if ($pay['pay_code'] == 'mcod'){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['mcod'] = $pay;
					
					$root['show_pay_btn'] = 0;
				}else if ($pay['pay_code'] == 'walipay'){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['walipay'] = $pay;
					
					if ($root['pay_money'] > 0){
						$root['show_pay_btn'] = 1;
					}
				}else if ($pay['pay_code'] == 'wtenpay'){
					$root['pay_money_format'] = $pay['total_fee_format'];
					$root['pay_money'] = $pay['total_fee'];
					$root['pay_info'] = $pay['body'];
					$root['wtenpay'] = $pay;
					if ($root['pay_money'] > 0){
						$root['show_pay_btn'] = 1;
					}
				}else{
					$root['return'] = 0;
					$root['pay_info'] = '手机版本不支付,无法在手机上支付.';
					$root['show_pay_btn'] = 0;
				}
				
				output($root);
			}// fwb update 2014-08-27
			
		}
		else {
			$root['user_login_status'] = 0;
			output($root);
		}		
	}
}
?>
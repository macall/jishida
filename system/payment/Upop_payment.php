<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'银联支付',
	'upop_merAbbr'	=>	'商户名称',
	'upop_account'	=>	'帐号',
	'upop_security_key'		=>	'秘钥',
	'GO_TO_PAY'	=>	'前往银联在线支付',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
);
$config = array(
	'upop_merAbbr'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //合作者身份ID
	'upop_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //帐号: 
	'upop_security_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //校验码
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Upop';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://cn.unionpay.com/';
    return $module;
}

// 支付宝支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
require APP_ROOT_PATH.'system/payment/Upop/quickpay_service.php';
class Upop_payment implements payment {
	public  $upop_evn = 2;
	public static $api_url = array(
        0  => array(
            'front_pay_url' => 'http://58.246.226.99/UpopWeb/api/Pay.action',
            'back_pay_url'  => 'http://58.246.226.99/UpopWeb/api/BSPay.action',
            'query_url'     => 'http://58.246.226.99/UpopWeb/api/Query.action',
        ),
        1  => array(
            'front_pay_url' => 'http://www.epay.lxdns.com/UpopWeb/api/Pay.action',
            'back_pay_url'  => 'http://www.epay.lxdns.com/UpopWeb/api/BSPay.action',
            'query_url'     => 'http://www.epay.lxdns.com/UpopWeb/api/Query.action',
        ),
        2  => array(
            'front_pay_url' => 'https://unionpaysecure.com/api/Pay.action',
            'back_pay_url'  => 'https://besvr.unionpaysecure.com/api/BSPay.action',
            'query_url'     => 'https://query.unionpaysecure.com/api/Query.action',
        ),
    );

	public function get_payment_code($payment_notice_id)
	{
		$upop_evn = $this->upop_evn;
		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		
		// 商户名称
		quickpay_conf::$pay_params['merAbbr']		= $payment_info['config']['upop_merAbbr'];

        foreach (Upop_payment::$api_url[$upop_evn] as $key => $value)
        {
            quickpay_conf::$$key = $value;
        }

		if ($upop_evn == '2') // 生产环境
		{
			quickpay_conf::$security_key			= $payment_info['config']['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment_info['config']['upop_account'];
		}
		else if ($upop_evn == '1') // PM环境
		{
			quickpay_conf::$security_key			= $payment_info['config']['upop_security_key_pm'];
			quickpay_conf::$pay_params['merId']		= $payment_info['config']['upop_account_pm'];
		}
		else if ($upop_evn == '0') // 开发联调环境
		{
			quickpay_conf::$security_key			= $payment_info['config']['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment_info['config']['upop_account'];
		}
		
		$frontEndUrl = SITE_DOMAIN.APP_ROOT.'/callback/payment/upop_response.php';
		$backEndUrl = SITE_DOMAIN.APP_ROOT.'/callback/payment/upop_notify.php';
		

		mt_srand(quickpay_service::make_seed());

		$param = array();

		$param['transType']             = quickpay_conf::CONSUME;  // 交易类型，CONSUME or PRE_AUTH
		$param['orderAmount']           = $money * 100;  // 交易金额 转化为分
		$param['orderNumber']           = $payment_notice['notice_sn'];		   // 订单号，必须唯一
		$param['orderTime']             = to_date(NOW_TIME,'YmdHis');		   // 交易时间, YYYYmmhhddHHMMSS
		$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，CURRENCY_CNY=>人民币

		$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  // 用户IP
		$param['frontEndUrl']           = $frontEndUrl;   // 前台回调URL
		$param['backEndUrl']            = $frontEndUrl;    // 后台回调URL

		/* 可填空字段
		   $param['commodityUrl']          = "http://www.example.com/product?name=商品";  //商品URL
		   $param['commodityName']         = '商品名称';   //商品名称
		   $param['commodityUnitPrice']    = 11000;        //商品单价
		   $param['commodityQuantity']     = 1;            //商品数量
		*/
		

		$button = "<button class='ui-button paybutton' rel='blue' type='submit'>去网银在线支付</button>";
		$pay_service = new quickpay_service($param, quickpay_conf::FRONT_PAY);
		$html = $pay_service->create_html($button);

        return $html;
	}
	
	public function response($request)
	{
		unset($request['city']);
        unset($request['ctl']);
		unset($request['act']);
		unset($request['class_name']);
		unset($_POST['city']);
		unset($_POST['ctl']);
		unset($_POST['act']);
		unset($_POST['class_name']);
        $upop_evn = $this->upop_evn;
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Upop'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	// 商户名称
		quickpay_conf::$pay_params['merAbbr']		= $payment['config']['upop_merAbbr'];

        foreach (Upop_payment::$api_url[$upop_evn] as $key => $value)
        {
            quickpay_conf::$$key = $value;
        }

		if ($upop_evn == '2') // 生产环境
		{
			quickpay_conf::$security_key			= $payment['config']['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment['config']['upop_account'];
		}
		else if ($upop_evn == '1') // PM环境
		{
			quickpay_conf::$security_key			= $payment['config']['upop_security_key_pm'];
			quickpay_conf::$pay_params['merId']		= $payment['config']['upop_account_pm'];
		}
		else if ($upop_evn == '0') // 开发联调环境
		{
			quickpay_conf::$security_key			= $payment['config']['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment['config']['upop_account'];
		}
    	
        try {
		    $response = new quickpay_service($request, quickpay_conf::RESPONSE);
		    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
		        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
		        showErr($err);
		    }
		    $arr_ret = $response->get_args(); 
		    
		    if (quickpay_conf::$pay_params['merId'] != $arr_ret['merId'])
			{
				echo "fail";
				die();
			}
			// 如果未支付成功。
			if ($arr_ret['respCode'] != '00')
			{
				echo "fail";
				die();
			}
		    
		    //告诉用户交易完成
		    $payment_notice_sn = $arr_ret['orderNumber'];
		    $outer_notice_sn = $arr_ret['orderNumber'];
		    
		    $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/model/cart.php";
			$rs = payment_paid($payment_notice['id']);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$outer_notice_sn."' where id = ".$payment_notice['id']);
					if($order_info['type']==0)
					app_redirect(url("index","payment#done",array("id"=>$payment_notice['order_id']))); //支付成功
					else
					app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['order_id']))); //支付成功
				}
				else 
				{
					if($order_info['pay_status'] == 2)
					{					
						if($order_info['type']==0)
						app_redirect(url("index","payment#done",array("id"=>$payment_notice['order_id']))); //支付成功
						else
						app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['order_id']))); //支付成功
					}
					else
					app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id']))); 
				}
			}
			else
			{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id']))); 
			}
		
		}
		catch(Exception $exp) {
		    $str .= var_export($exp, true);
		    showErr("error happend: " . $str);
		}
		
		  
	}
	
	public function notify($request)
	{
		unset($request['city']);
        unset($request['ctl']);
		unset($request['act']);
		unset($request['class_name']);
		unset($_POST['city']);
		unset($_POST['ctl']);
		unset($_POST['act']);
		unset($_POST['class_name']);
		$upop_evn = $this->upop_evn;
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Upop'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	// 商户名称
		quickpay_conf::$pay_params['merAbbr']		= $payment['config']['upop_merAbbr'];

        foreach (Upop_payment::$api_url[$upop_evn] as $key => $value)
        {
            quickpay_conf::$$key = $value;
        }

		if ($upop_evn == '2') // 生产环境
		{
			quickpay_conf::$security_key			= $payment['config']['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment['config']['upop_account'];
		}
		else if ($upop_evn == '1') // PM环境
		{
			quickpay_conf::$security_key			= $payment['config']['upop_security_key_pm'];
			quickpay_conf::$pay_params['merId']		= $payment['config']['upop_account_pm'];
		}
		else if ($upop_evn == '0') // 开发联调环境
		{
			quickpay_conf::$security_key			= $payment['upop_security_key'];
			quickpay_conf::$pay_params['merId']		= $payment['upop_account'];
		}
    	
    	
        try {
		    $response = new quickpay_service($request, quickpay_conf::RESPONSE);
		    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
		        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
		        showErr($err);
		    }
		    $arr_ret = $response->get_args(); 
		    
		    if (quickpay_conf::$pay_params['merId'] != $arr_ret['merId'])
			{
				echo "fail";
				die();
			}
			// 如果未支付成功。
			if ($arr_ret['respCode'] != '00')
			{
				echo "fail";
				die();
			}
		    //告诉用户交易完成
		    $payment_notice_sn = $arr_ret['orderNumber'];
		    $outer_notice_sn = $arr_ret['orderNumber'];
		    
		    $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/model/cart.php";
			$rs = payment_paid($payment_notice['id']);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$outer_notice_sn."' where id = ".$payment_notice['id']);
					 echo "Success";
				}
				else 
				{
					 echo "Success";
				}
			}
			else
			{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id']))); 
			}
		
		}
		catch(Exception $exp) {
		    $str .= var_export($exp, true);
		    echo "fail";
		}
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Upop'");
		if($payment_item)
		{
			$html = "<label class='ui-radiobox' rel='payment_rdo'><input type='radio' name='payment' value='".$payment_item['id']."' />";

			if($payment_item['logo']!='')
			{
				$html .= "<img src='".APP_ROOT.$payment_item['logo']."' />";
			}
			else
			{
				$html .= $payment_item['name'];
			}
			$html.="</label>";
			return $html;
		}
		else
		{
			return '';
		}
	}
	
	
}
?>
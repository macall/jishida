<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'财付通支付',
	'tencentpay_id'	=>	'商户ID',
	'tencentpay_key'	=>	'商户密钥',
	'tencentpay_sign'	=>	'自定义签名',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'GO_TO_PAY'	=>	'前往财付通支付',
);
$config = array(
	'tencentpay_id'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户ID
	'tencentpay_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户密钥
	'tencentpay_sign'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //自定义签名
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Tenpay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
     $module['reg_url'] = 'http://union.tenpay.com/mch/mch_register.shtml?sp_suggestuser=222359';
    return $module;
}

// 余额支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Tenpay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		require APP_ROOT_PATH."system/payment/Tenpay/classes/RequestHandler.class.php";
		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		$data_return_url = SITE_DOMAIN.APP_ROOT.'/callback/payment/tenpay_response.php';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/callback/payment/tenpay_notify.php';

        $cmd_no = '1';

        /* 获得订单的流水号，补零到10位 */
        $sp_billno = $payment_notice_id;

        $spbill_create_ip =  $_SERVER['REMOTE_ADDR'];
        
        /* 交易日期 */
        $today = to_date($payment_notice['create_time'],'YmdHis');


        /* 将商户号+年月日+流水号 */
        $out_trade_no = $payment_notice['notice_sn'];

        /* 银行类型:支持纯网关和财付通 */
        $bank_type = "DEFAULT";


        $desc = $out_trade_no;
        $attach = $payment_info['config']['tencentpay_sign'];

		
        /* 返回的路径 */
        $return_url = $data_return_url;

        /* 总金额 */
        $total_fee = $money*100;

        /* 货币类型 */
        $fee_type = '1';

        /* 重写自定义签名 */
        //$payment['magic_string'] = abs(crc32($payment['magic_string']));

        /* 数字签名 */
       /* $sign_text = "cmdno=" . $cmd_no . "&date=" . $today . "&bargainor_id=" . $payment_info['config']['tencentpay_id'] .
          "&transaction_id=" . $transaction_id . "&sp_billno=" . $sp_billno .
          "&total_fee=" . $total_fee . "&fee_type=" . $fee_type . "&return_url=" . $return_url .
          "&attach=" . $attach . "&spbill_create_ip=" . $spbill_create_ip ."&key=" . $payment_info['config']['tencentpay_key'];
        $sign = strtoupper(md5($sign_text));

         交易参数 
        $parameter = array(
            'cmdno'             => $cmd_no,                     // 业务代码, 财付通支付支付接口填  1
            'date'              => $today,                      // 商户日期：如20051212
            'bank_type'         => $bank_type,                  // 银行类型:支持纯网关和财付通
            'desc'              => $desc,                       // 交易的商品名称
            'purchaser_id'      => '',                          // 用户(买方)的财付通帐户,可以为空
            'bargainor_id'      => $payment_info['config']['tencentpay_id'],  // 商家的财付通商户号
            'transaction_id'    => $transaction_id,             // 交易号(订单号)，由商户网站产生(建议顺序累加)
            'sp_billno'         => $sp_billno,                  // 商户系统内部的定单号,最多10位
            'total_fee'         => $total_fee,                  // 订单金额
            'fee_type'          => $fee_type,                   // 现金支付币种
            'return_url'        => $return_url,                 // 接收财付通返回结果的URL
            'attach'            => $attach,                     // 用户自定义签名
        	'spbill_create_ip'  => $spbill_create_ip,           // 安全防范参数
            'sign'              => $sign,                       // MD5签名
            //'sys_id'            => '542554970',                 
            //'sp_suggestuser'    => '1202822001'                 //财付通分配的商户号
        );
		//
		

		
		$payLinks = '<form style="text-align:center;" action="https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi" target="_blank" style="margin:0px;padding:0px" >';

	 	foreach ($parameter AS $key=>$val)
        {
            $payLinks  .= "<input type='hidden' name='$key' value='$val' />";
        }
        
    	if(!empty($payment_info['logo']))
		{
			$payLinks .= "<input type='image' src='".APP_ROOT.$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
		}
		$payLinks .= "<input type='submit' class='paybutton' value='".sprintf($this->payment_lang['GO_TO_PAY'],$this->get_name($bank_type))."'></form>";
        $code = '<div style="text-align:center">'.$payLinks.'</div>';
		$code.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div>";
        return $code;
        */
        
        $reqHandler = new RequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($payment_info['config']['tencentpay_key']);
		$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
		
		//----------------------------------------
		//设置支付参数 
		//----------------------------------------
		$reqHandler->setParameter("partner", $payment_info['config']['tencentpay_id']);
		$reqHandler->setParameter("out_trade_no", $out_trade_no);
		$reqHandler->setParameter("total_fee", $total_fee);  //总金额
		$reqHandler->setParameter("return_url", $return_url);
		$reqHandler->setParameter("notify_url", $data_notify_url);
		$reqHandler->setParameter("body", $desc);
		$reqHandler->setParameter("bank_type", $bank_type);  	  //银行类型，默认为财付通
		//用户ip
		$reqHandler->setParameter("spbill_create_ip", CLIENT_IP);//客户端IP
		$reqHandler->setParameter("fee_type", $fee_type);               //币种
		$reqHandler->setParameter("subject",$desc);          //商品名称，（中介交易时必填）
		
		//系统可选参数
		$reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
		$reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
		$reqHandler->setParameter("input_charset", "utf-8");   	  //字符集
		$reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号
		
		//业务可选参数
		$reqHandler->setParameter("attach", $attach);             	  //附件数据，原样返回就可以了
		$reqHandler->setParameter("product_fee", "");        	  //商品费用
		$reqHandler->setParameter("transport_fee", "0");      	  //物流费用
		$reqHandler->setParameter("time_start", $today);  //订单生成时间
		$reqHandler->setParameter("time_expire", "");             //订单失效时间
		$reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
		$reqHandler->setParameter("goods_tag", "");               //商品标记
		$reqHandler->setParameter("trade_mode",$cmd_no);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
		$reqHandler->setParameter("transport_desc","");              //物流说明
		$reqHandler->setParameter("trans_type","1");              //交易类型
		$reqHandler->setParameter("agentid","");                  //平台ID
		$reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
		$reqHandler->setParameter("seller_id","");                //卖家的商户号
		
		
		
		//请求的URL
		$reqUrl = $reqHandler->getRequestURL();
		if($_REQUEST['v']==1){
			$debugInfo = $reqHandler->getDebugInfo();
			echo "<br/>" . $reqUrl . "<br/>";
			echo "<br/>" . $debugInfo . "<br/>";
		}
		$payLinks = '<form style="text-align:center;" action="'.$reqHandler->getGateUrl().'" target="_blank" style="margin:0px;padding:0px" method="post" >';
		$params = $reqHandler->getAllParameters();
		foreach($params as $k => $v) {
			$payLinks.="<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\" />\n";
		}

		$payLinks .= "<button class='ui-button paybutton' rel='blue' type='submit'>前往财付通支付</button></form>";
        $code = '<div style="text-align:center">'.$payLinks.'</div>';
		$code.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div>";
        return $code;
	}
	
	public function response($request)
	{
		unset($_POST['city']);
		unset($_GET['city']);
		require (APP_ROOT_PATH."system/payment/Tenpay/classes/ResponseHandler.class.php");
		require (APP_ROOT_PATH."system/payment/Tenpay/classes/function.php");
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Tenpay'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
        $resHandler = new ResponseHandler();
		$resHandler->setKey($payment['config']['tencentpay_key']);
		
		//判断签名
		if($resHandler->isTenpaySign())
		{
			//通知id
			$notify_id = $resHandler->getParameter("notify_id");
			//商户订单号
			$out_trade_no = $resHandler->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $resHandler->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $resHandler->getParameter("discount");
			//支付结果
			$trade_state = $resHandler->getParameter("trade_state");
			//交易模式,1即时到账
			$trade_mode = $resHandler->getParameter("trade_mode");
			
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$out_trade_no."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/model/cart.php";
			$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$out_trade_no."' where id = ".$payment_notice['id']);
			$rs = payment_paid($payment_notice['id']);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);
				if($rs)
				{
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
		}else{
		    showErr($GLOBALS['payment_lang']["PAY_FAILED"]);
		    //showErr($resHandler->getDebugInfo() );
		}   
	}
	
	public function notify($request)
	{
		unset($_POST['city']);
		unset($_GET['city']);
		require (APP_ROOT_PATH."system/payment/TenpayBank/classes/ResponseHandler.class.php");
		require (APP_ROOT_PATH."system/payment/TenpayBank/classes/function.php");
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='TenpayBank'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
        $resHandler = new ResponseHandler();
		$resHandler->setKey($payment['config']['tencentpay_key']);
		
		//判断签名
		if($resHandler->isTenpaySign())
		{
			//通知id
			$notify_id = $resHandler->getParameter("notify_id");
			//商户订单号
			$out_trade_no = $resHandler->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $resHandler->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $resHandler->getParameter("discount");
			//支付结果
			$trade_state = $resHandler->getParameter("trade_state");
			//交易模式,1即时到账
			$trade_mode = $resHandler->getParameter("trade_mode");
			
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$out_trade_no."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/model/cart.php";
			$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$out_trade_no."' where id = ".$payment_notice['id']);
			$rs = payment_paid($payment_notice['id']);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);
				if($rs)
				{
					echo "success";
				}
				else 
				{
					echo "success";
				}
			}
			else
			{
				 echo "fail";
			}
		}else{
		    echo "fail";
		} 
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Tenpay'");
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
<?php
// +----------------------------------------------------------------------
// | EaseTHINK 易想团购系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.easethink.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'微信支付(WAP版本)',
	'appid'	=>	'微信公众号ID',
	'appsecret'=>'微信公众号SECRT',
	'mchid'	=>	'微信支付MCHID',
  	'partnerid'	=>	'商户ID',
	'partnerkey'	=>	'商户key',
	'key'	=>	'商户支付密钥Key',
	'sslcert'=>'apiclient_cert证书路径',
	'sslkey'=>'apiclient_key证书路径',
	'type'=>'类型(V2或V3)',
);
$config = array(
	'appid'=>array(
		'INPUT_TYPE'=>'0',
	),//微信公众号ID
	'appsecret'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //微信公众号SECRT
	'mchid'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //微信支付MCHID
 	'partnerid'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户ID
	'partnerkey'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户key
	'key'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户支付密钥Key
	'sslcert'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //apiclient_cert证书路径
	'sslkey'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //apiclient_key证书路径
	'type'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //类型
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Wwxjspay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付;2:手机wap;3:手机sdk */
    $module['online_pay'] = '2';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}

// 支付宝手机支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Wwxjspay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		
 		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_info = $GLOBALS['db']->getRow("select deal_ids from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$order_sn = $payment_notice['notice_sn'];
 		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		 
		$wx_config=$payment_info['config'];
		 
 		$sql = "select name from ".DB_PREFIX."deal where id =". intval($order_info['deal_ids']);
		
 		$title_name =$GLOBALS['db']->getOne($sql);
 		$subject = $order_sn;
 		include(APP_ROOT_PATH."system/payment/Wxjspay/WxPayPubHelper.php");
  	
		$notify_url =get_domain().APP_ROOT."/wxpay_web/notify_url.php?order_id=".intval($payment_notice['order_id'])."&out_trade_no=".$order_sn;//."&out_trade_no={$data.walipay.out_trade_no}";
		$notify_url = str_replace("/sjmapi", "", $notify_url);
		$notify_url = str_replace("/mapi", "", $notify_url);
  		$order_id = $order_sn;//网页支付的订单在订单有效期内可以进行多次支付请求，但是需要注意的是每次请求的业务参数都要一致，交易时间也要保持一致。否则会报错“订单与已存在的订单信息不符”
		
		$data_return_url = get_domain().APP_ROOT."/wap/index.php?ctl=pay_order&order_id=".intval($payment_notice['order_id']);
		//$return['notify_url']=$notify_url;
		$data_return_url = str_replace("/sjmapi", "", $data_return_url);
		$data_return_url = str_replace("/mapi", "", $data_return_url);
		$return['notify_url']=$data_return_url;
		$return['is_wap']=1;
		$return['is_wap_url']=0;
		$return['show_pay_wxbtn']=1;
		$return['pay_code'] = 'Wwxjspay';
		$return['body'] = $title_name;
		$return['total_fee'] = $money;
		$return['total_fee_format'] = format_price($money);
		$return['out_trade_no'] = $payment_notice['notice_sn'];
		$money_fen=intval($money*100);
  		if($wx_config['type']=='V2'){
			require_once APP_ROOT_PATH.'system/extend/ip.php';
			$iplocation = new iplocate();
	 		$user_ip = $iplocation->getIP(); 
			$unifiedOrder = new UnifiedOrder_pub();
			$unifiedOrder->update_config($wx_config['appid'],$wx_config['appsecret'],$wx_config['mchid'],$wx_config['partnerid'],$wx_config['partnerkey'],$wx_config['key'],$wx_config['sslcert'],$wx_config['sslkey']);
			$unifiedOrder->setParameter("input_charset", "GBK");
			$unifiedOrder->setParameter("bank_type", "WX");
			$unifiedOrder->setParameter("body", $title_name);
			$unifiedOrder->setParameter("partner",$wx_config['partnerid']);
	 		//$unifiedOrder->setParameter("out_trade_no", $unifiedOrder->create_noncestr());
	 		$unifiedOrder->setParameter("out_trade_no",$order_sn);
	 		$unifiedOrder->setParameter("total_fee", "$money_fen");
			$unifiedOrder->setParameter("fee_type", "0");
			$unifiedOrder->setParameter("notify_url", $notify_url);
			$unifiedOrder->setParameter("spbill_create_ip",$user_ip);
			$unifiedOrder->setParameter("input_charset", "GBK");
			$jsApiParameters = $unifiedOrder->create_biz_package();
  		}elseif($wx_config['type']=='V3'){
  		
  			$jsApi = new JsApi_pub();
  			$jsApi->update_config($wx_config['appid'],$wx_config['appsecret'],$wx_config['mchid'],$wx_config['partnerid'],$wx_config['partnerkey'],$wx_config['key'],$wx_config['sslcert'],$wx_config['sslkey']);

  			if (!isset($_REQUEST['code']))
  			{
  				//触发微信返回code码
  				$url = $jsApi->createOauthUrlForCode(urlencode($return['notify_url']));
  				//header("Location:".$url);
  				$return['is_wap_url']=1;
  				$return['wap_notify_url']=$url;
  				return $return;
  			}else
  			{
  				//获取code码，以获取openid
  				$code = $_REQUEST['code'];
  				$jsApi->setCode($code);
  				$openid = $jsApi->getOpenId();
  			}
  		 
  			$unifiedOrder = new UnifiedOrder_pub();
  			$unifiedOrder->update_config($wx_config['appid'],$wx_config['appsecret'],$wx_config['mchid'],$wx_config['partnerid'],$wx_config['partnerkey'],$wx_config['key'],$wx_config['sslcert'],$wx_config['sslkey']);
  			$unifiedOrder->setParameter("openid","$openid");//商品描述
  			$unifiedOrder->setParameter("body",substr($title_name,0,50));//商品描述
  			$timeStamp =NOW_TIME;
  				
  			$unifiedOrder->setParameter("out_trade_no","$order_sn");//商户订单号
  			$unifiedOrder->setParameter("total_fee",$money_fen);//总金额
  			$unifiedOrder->setParameter("notify_url",$notify_url);//通知地址
  			$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
  			
  			$prepay_id = $unifiedOrder->getPrepayId();
  				
  			//=========步骤3：使用jsapi调起支付============
  			$jsApi->setPrepayId($prepay_id);
  			//file_put_contents(APP_ROOT_PATH."/sjmapi/log/log_xml_1.txt",print_r($prepay_id,1));
  			$jsApiParameters = $jsApi->getParameters();
  			
  			//$deal_url=get_domain().APP_ROOT."/wap/index.php";
  			//$deal_url = str_replace("/sjmapi", "", $deal_url);
  			//$deal_url = str_replace("/mapi", "", $deal_url);
  			
  			//$jsApiParameters=str_replace('deal_url',$deal_url,$jsApiParameters);
  			}
  			$return['parameters']=$jsApiParameters;
  		
  			//echo $jsApiParameters;
  			return $return;
			
			
	}
	
	public function response($request)
	{	
							
	}
	
	public function notify($request){
		
	}
	
	public function get_display_code(){
		return "";
	}
}
?>
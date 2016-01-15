<?php

$payment_lang = array(
	'name'	=>	'财付通手机WAP支付',
	'tenpay_partner'	=>	'合作者身份ID',
	'tenpay_key'	=>	'财付通公钥',
);
$config = array(
	'tenpay_partner'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //合作者身份ID
	//支付宝公钥
	'tenpay_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	)
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Wtenpay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}

// 支付宝手机支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Wtenpay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
			
		
		$sql = "select name ".
						  "from ".DB_PREFIX."deal_order_item ".					
						  "where order_id =". intval($payment_notice['order_id']);
		$title_name = $GLOBALS['db']->getOne($sql);

		
		$subject = msubstr($title_name,0,40);
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php.php?ctl=payment&act=response&class_name=Wtenpay';
		$notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Wtenpay';
		
		$pay = array();
		$pay['subject'] = $subject;
		$pay['body'] = $title_name;
		$pay['total_fee'] = $money;
		$pay['total_fee_format'] = format_price($money);
		$pay['out_trade_no'] = $payment_notice['notice_sn'];
		$pay['notify_url'] = $notify_url;
		
		$pay['partner'] = $payment_info['config']['tenpay_partner'];//合作商户ID
				
		$pay['key'] = $payment_info['config']['tenpay_key'];//支付宝(RSA)公钥
		

		$pay['pay_code'] = 'wtenpay';//,支付宝;mtenpay,财付通;mcod,货到付款
				
		return $pay;
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
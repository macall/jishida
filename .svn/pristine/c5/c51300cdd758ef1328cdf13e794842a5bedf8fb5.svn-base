<?php
class wap_qrcode{
	public function index()
	{
		$wap_url = strim($GLOBALS['request']['wap_url']);
		preg_match("/([&|-|\/]id[=|-](\d+))/i", $wap_url,$match);
		
		//print_r($GLOBALS['request']);;exit;
		
		$root = array();
		$root['return'] = 1;
		$root['status'] = 0;//1:解释成功;0:解释失败
		$id = intval($match[2]);
		if ($id > 0){
			$sql = "select * from  ".DB_PREFIX."supplier_location where id = ".$id;
			$supplier = $GLOBALS['db']->getRow($sql);
			if ($supplier){
				$root['status'] = 1;
				$root['is_auto_order'] = $supplier['is_auto_order'];//0：支持自主下单;1:不支持
				$root['id'] = $supplier['id'];//门店id
				$root['name'] = $supplier['name'];//门店名称
				$root['mobile_brief'] = $supplier['mobile_brief'];//手机端列表简介	
				$root['address'] = $supplier['address'];//地址
				$root['op_type'] = 1;//操作类型; 1:打开手机端的门店详细页; 0:打开下单界面
				$root['info'] = '';
			}else{				
				$root['info'] = '该门店不存在';
			}						
		}else{			
			$root['info'] = '无效的商家地址';
		}
		

		
		output($root);
	}
}
?>
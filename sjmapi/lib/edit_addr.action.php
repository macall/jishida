<?php
class edit_addr{
	public function index()
	{
		$url = strim($GLOBALS['request']['aurl']);
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
		
		$root = array();
		$root['return'] = 1;	
		if($user_id>0)
		{
			
			$root['user_login_status'] = 1;		

			$id = intval($GLOBALS['request']['id']);//id,有ID值则更新，无ID值，则插入
			$root['consignee']=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where user_id = {$user_id} and id = {$id}" );
			$root['id'] = $id;
			$consignee_data=array();
			$consignee_id=$id;
				$consignee_info =  $consignee_data['consignee_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where id = ".$consignee_id);
				$region_lv1 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = 0");  //一级地址
				foreach($region_lv1 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv1'])
					{
						$region_lv1[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv1'] = $region_lv1;
				
				$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$consignee_info['region_lv1']);  //二级地址
				foreach($region_lv2 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv2'])
					{
						$region_lv2[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv2'] = $region_lv2;
				
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$consignee_info['region_lv2']);  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv3'])
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv3'] = $region_lv3;
				
				$region_lv4 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where pid = ".$consignee_info['region_lv3']);  //四级地址
				foreach($region_lv4 as $k=>$v)
				{
					if($v['id'] == $consignee_info['region_lv4'])
					{
						$region_lv4[$k]['selected'] = 1;
						break;
					}
				}
				$consignee_data['region_lv4'] = $region_lv4;	
				$root['consignee_data']=$consignee_data;
				
				//$url = base64_decode($url);
				if($url)
				{
					$root['aurl'] = $url;
				}
		}		
		else
		{
			$root['user_login_status'] = 0;		
		}	
		$root['city_name']=$city_name;
		$root['page_title'] = '编辑收货地址';	
		output($root);
	}
}
?>
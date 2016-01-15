<?php
class get_curcitybyip
{
	public function index()
	{
		
		$root = array();
		$client_ip=strim($GLOBALS['request']['client_ip']);
		if (empty($client_ip))
			$client_ip = get_client_ip();
		
		$root['ip'] = $client_ip;
		$root['ip_city_name'] = '';
		$root['ip_city_id'] = 0;
		
		//设置如存在的IP订位
		if(file_exists(APP_ROOT_PATH."system/extend/ip.php"))
		{			
			require_once APP_ROOT_PATH."system/extend/ip.php";
			
			$iplocation = new iplocate();
			$address=$iplocation->getaddress($client_ip);
			$root['address'] = $address;
			//$root['ip_city_name'] = str_replace('市', '', $address['area1']);
			$root['ip_city_id'] = 0;
			if (mb_strpos($address['area1'],'省',0,'utf-8') === false){
				$root['ip_city_name'] = $address['area1'];
			}else{
				$ipos = intval(mb_strpos($address['area1'],'省',0,'utf-8'));
				$root['ip_city_name'] = mb_substr($address['area1'],$ipos + 1,10,'utf-8');
			}			

			$root['ip_city_name'] = str_replace('市', '', $root['ip_city_name']);

			$sql = "select * from ".DB_PREFIX."deal_city where is_delete = 0 and is_effect = 1 ";
			$city_list = $GLOBALS['db']->getAll($sql);//不知谁把$city_list 查询去掉了; 去掉后就不能通过ip定位了; chenfq 现在又添加上去了 2014-09-18
			
			foreach ($city_list as $city)
			{
				if(strpos($address['area1'],$city['name']))
				{
					$deal_city = $city;
					$root['ip_city_id'] = $city['id'];
					break;
				}
			}
		}
		if(!$deal_city)
			$deal_city = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_city where is_default = 1 and is_effect = 1 and is_delete = 0");
		
		$root['city_name'] = $deal_city['name'];
		$root['city_id'] = $deal_city['id'];
		
		output($root);
	}
}
?>
<?php
class changecity{
	public function index()
	{
		$m_latitude = doubleval($GLOBALS['request']['latitude']);  //ypoint
		$m_longitude = doubleval($GLOBALS['request']['longitude']);  //xpoint
		
		$city_name = strim($GLOBALS['request']['location_city_name']); //定位城市名
		
		$sql = "select id, name from ".DB_PREFIX."deal_city where is_delete = 0 and is_effect = 1";
		$city_list = $GLOBALS['db']->getAll($sql);		
		foreach ($city_list as $city)
		{
			if(strpos($city_name,$city['name']) || $city_name == $city['name'])
			{
				$deal_city = $city;
				break;
			}
		}
		
		$root = array();		
		if(!$deal_city){
			$root['status'] = 0;
			$root['info'] = $city_name.' 城市还未开通,请选择其它城市';
		}else{
			$root['status'] = 1;
			$root['city_id'] = $deal_city['id'];
			$root['city_name'] = $deal_city['name'];
			$root['info'] = $city_name.'城市,定位成功';
		}
		
		$root['deal_city'] = $deal_city;
		$root['city_list'] = $city_list;
		$root['return'] = 1;
		
		$root['m_latitude'] = $m_latitude;
		$root['m_longitude'] = $m_longitude;
		
		output($root);
	}
}

?>
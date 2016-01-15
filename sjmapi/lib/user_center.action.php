<?php
class user_center
{
	public function index()
	{
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		
		$root = array();	
		$user_data = $GLOBALS['user_info'];

		//logUtils::log_obj($user_data);
		
		$user_id = intval($user_data['id']);

		if($user_id == 0){
			$root['user_login_status'] = 0;
			$root['info'] = "请先登陆";	
			$root['page_title'] = "登陆";			
		}else{
			$root['user_login_status'] = 1;
			$root['info'] = "用户中心";
			$root['page_title'] = "用户中心";
			$root['uid'] = $user_data['id'];
			$root['user_name'] = $user_data['user_name'];
			$root['user_email'] = $user_data['email'];
			$root['user_money'] = $user_data['money'];
			$root['user_money_format'] = format_price($user_data['money']);//用户金额
			$root['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"));
		}
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
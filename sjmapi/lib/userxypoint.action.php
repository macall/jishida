<?php
class userxypoint
{
	public function index()
	{

		$root = array();
		$root['return'] = 0;

		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);

		$latitude = floatval($GLOBALS['request']['latitude']);//ypoint
		$longitude = floatval($GLOBALS['request']['longitude']);//xpoint


		$root['m_latitude'] = $latitude;
		$root['m_longitude'] = $longitude;
		$root['status'] = 1;
		
		if ($user_id > 0 && $latitude > 0 && $longitude > 0){
			$user_x_y_point = array(
								'uid' => $user_id,
								'xpoint' => $longitude,
								'ypoint' => $latitude,
								'locate_time' => get_gmtime(),
			);
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_x_y_point", $user_x_y_point, 'INSERT');
			$sql = "update ".DB_PREFIX."user set xpoint = $longitude, ypoint = $latitude, locate_time = ".get_gmtime()." where id = $user_id";
			$GLOBALS['db']->query($sql);

			$root['return'] = 1;
		}

		output($root);
	}
}
?>
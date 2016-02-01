<?php
class tech_dp_reply{
	public function index()
	{
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$id  = intval($GLOBALS['request']['id']);
		$sql = "select dp.*,from_unixtime(dp.create_time,'%Y-%m-%d %h:%i:%s') as create_time_format,u.user_name from ".DB_PREFIX."supplier_location_dp dp left join ".DB_PREFIX."user u on dp.user_id=u.id where dp.id=$id";
		$root = $GLOBALS['db']->getRow($sql);
		if($root['reply_time']>0){
			$root['reply_time_format']=date("Y-m-d H:i:s",$root['reply_time']);
		}
		$content = strim($GLOBALS['request']['content']);//点评内容
		if($content!=''){
			$result=$GLOBALS['db']->query("update ".DB_PREFIX."supplier_location_dp set reply_content = '".$content."',reply_time=".time()." where id = '".$id."'");
			$root['sql']="update ".DB_PREFIX."supplier_location_dp set reply_content = ".$content." where id = '".$id."'";
			$root['status'] = 1;
			$root['info'] = "回复成功";		
		}
		output($root);
	}
}
?>
<?php
class commentlist
{
	public function index()
	{
		require_once APP_ROOT_PATH."system/model/user.php";
		
		$id = intval($GLOBALS['request']['share_id']);
		$page = intval($GLOBALS['request']['page']);
		
		$user_data = $GLOBALS['user_info'];
		$user_data['id'] = intval($user_data['id']);

		$root = array();
		$root['id']=$id;
		$root['return'] = 1;
		$res = m_get_topic_reply($id,$page);
		$root['item'] = $res['list'];
		$root['page'] = $res['page'];
		
		
		output($root);
	}
}
?>
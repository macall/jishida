<?php
class user_do_complain_post{
	public function index()
	{
		$content = strim($GLOBALS['request']['content']);//邮箱
		$user_id = strim($GLOBALS['request']['user_id']);//密码
		$tech_id = strim($GLOBALS['request']['tech_id']);//用户名
		$order_id = intval($GLOBALS['request']['order_id']);
                
                //检查用户,用户密码
		$user = $GLOBALS['user_info'];
                if($user_id != $user['id']){
                    $root['status'] = 0;
                    $root['info'] = "非法用户，禁止访问";
                    output($root);
                }
                
                if(!content){
                    $root['status'] = 0;
                    $root['info'] = "请填写投诉内容";
                    output($root);
                }
                if(!user_id){
                    $root['status'] = 0;
                    $root['info'] = "投诉出错,当前无用户";
                    output($root);
                }
                if(!tech_id){
                    $root['status'] = 0;
                    $root['info'] = "投诉出错,当前无被投诉人";
                    output($root);
                }
                if(!order_id){
                    $root['status'] = 0;
                    $root['info'] = "投诉订单出错";
                    output($root);
                }
                
                $data = array(
                    'user_id' => $user_id,
                    'content' => $content,
                    'tech_id' => $tech_id,
                    'order_id' => $order_id,
                    'create_time' => time()
                );
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_order_complain", $data, 'INSERT');
                $complain_id = $GLOBALS['db']->insert_id();
                if(!empty($complain_id)){
                    $root['status'] = 1;
                    $root['complain_id'] = $complain_id;
                    $root['info'] = "投诉成功";
                }
                
		output($root);
	}
}
?>
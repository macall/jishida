<?php
class tuan_comment_list
{
	public function index()
	{
		$root = array();
		$root['return'] = 1;

		$tuan_id = intval($GLOBALS['request']['tuan_id']);
		//添加点评数据
		$content = addslashes(trim($GLOBALS['request']['content']));
	
		if ($content != null && $content != ""){
			
			//检查用户,用户密码
			$user = $GLOBALS['user_info'];
			$user_id  = intval($user['id']);
		
				$rel_table = 'youhui';
				$rel_id = $tuan_id;
		
				$merchant_youhui_comment = array(
							'user_id' => $user_id,
							'rel_id' => $rel_id,
							'rel_table' => $rel_table,
							//'supplier_location_id' => $supplier_location_id,
							'title' => $content,
							'content' => $content,
							'is_effect' => 1,
							'create_time' => get_gmtime(),
				);

				$GLOBALS['db']->autoExecute(DB_PREFIX."message", $merchant_youhui_comment, 'INSERT');
			
		}else{
			$root['status'] = 0;
			$root['info'] = "请输入您的评论";
		}
	
		$root['page_title']="我要评论";
		output($root);
	}
}
?>
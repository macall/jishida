<?php
// fwb add 2014-08-27
class topic_reply{
	public function index(){
		$root = array();   
		$root['return'] = 1;  
		$group_id = intval($_REQUEST['id']);
		
		
		$content = addslashes(trim($GLOBALS['request']['content']));
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$user_name=$GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id=".$user_id);
		if($group_id>0)
		{
			if($user_id>0){
				$addtopic = array(
						'user_id' => $user_id,
						'topic_id'=> $group_id,
						'content' => $content,
						'user_name'=> $user_name,
						'create_time' => get_gmtime(),
						'is_effect' => 1,
						'is_delete' => 0,
				);
	
				$GLOBALS['db']->autoExecute(DB_PREFIX."topic_reply", $addtopic, 'INSERT');
			
	      
				$id = $GLOBALS['db']->insert_id();
				$root['id'] = $id;
				if($id > 0)
				{
					$root['status'] = 1;
					$GLOBALS['db']->query("update ".DB_PREFIX."topic set reply_count = reply_count + 1,last_time = ".get_gmtime().",last_user_id = ".intval($user_id)." where id = ".$group_id);
					$root['info'] = "添加成功";
					
				}else{
					$root['status'] = 0;
					$root['info'] = "添加失败";
				}	
			}else{
				$root['status'] = 2;
				$root['info'] = "请先登陆";
			}
			
		}else{
			$root['status']=0;
			$root['info']="请输入要评论的主题";
		}
		

		$root['email']=$email;
	
		$root['f_link_data']=get_link_list();	
		$root['id']=$group_id;
		
		$root['page_title']="发表主题";

		
		output($root);
	}
}
?>

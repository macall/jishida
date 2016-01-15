<?php
// fwb add 2014-08-27
class uc_addtopic{
	public function index(){
		$root = array();   
		$root['return'] = 1;  
		$group_id = intval($_REQUEST['id']);
		
		$forum_title=htmlspecialchars(addslashes(trim($_REQUEST['forum_title'])));
		$content = addslashes(trim($GLOBALS['request']['content']));
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$user_name=$GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id=".$user_id);
		if($group_id>0)
		{
			if($forum_title==''){
				$root['status']=0;
				$root['info']="请输入要发表的主题";
			}else{
				$group_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_group where id = ".$group_id);
				if($group_info['user_id']!=$user_id) //不是组长进行验证
				{
					if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_topic_group where group_id=".$group_id." and user_id = ".$user_id)==0)
					{
						$root['status']=0;
						$root['info']="不是本组会员, 不能发表主题";
					}
				}else{
					$addtopic = array(
								'user_id' => $user_id,
								'group_id'=> $group_id,
								'forum_title' => $forum_title,
								'content' => $content,
								'user_name'=> $user_name,
								'create_time' => get_gmtime(),
								'is_effect' => 1,
								'is_delete' => 0,
					);
	
					$GLOBALS['db']->autoExecute(DB_PREFIX."topic", $addtopic, 'INSERT');
				
	          
					$id = $GLOBALS['db']->insert_id();
					$root['id'] = $id;
					if($id > 0)
					{
						$root['status'] = 1;
						$root['info'] = "添加成功";
					}else{
						$root['status'] = 0;
						$root['info'] = "添加失败";
					}
					
				}
					
			}
			
			
		}
		

		$root['email']=$email;
	
		$root['f_link_data']=get_link_list();	
		$root['id']=$group_id;
		
		$root['page_title']="发表主题";

		
		output($root);
	}
}
?>

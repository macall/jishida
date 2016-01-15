<?php
class receive_ecv
{
	public function index()
	{
		require_once APP_ROOT_PATH."system/libs/user.php";
		//print_r($email);echo"<br />";print_r($pwd);exit;
		
		//检查用户,用户密码
		$user_return = $GLOBALS['user_info'];
		$user = $user_return;
		$user_id  = intval($user['id']);
		//print_r($user_id);exit;
		$uname=strim($GLOBALS['request']['uname']);
		
		
		if($user_id==0)
		{
			$root['status']=0;
			$root['info']="请先登录";
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			output($root);	
		}
		else
		{
			
			$ecvtype_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where uname= '".$uname."'");
			
			if(!$ecvtype_info)
			{
				$root['status'] = 0;
				$root['info'] ="代金券不存在";
				output($root);
			}
			
			
			$id=intval($ecvtype_info['id']);
			
			$is_use=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."ecv where ecv_type_id=".$id." and user_id=".$user_id);
			if($is_use >0 ){
				$root['status'] = 0;
				$root['info'] ="您已经领取过了";
				output($root);
			}
			
			if($ecvtype_info['end_time']>0&&$ecvtype_info['end_time']<get_gmtime())
			{
				$root['status'] = 0;
				$root['info'] ="代金券已过期";
				output($root);
			}
			
			$GLOBALS['db']->query("update ".DB_PREFIX."ecv set user_id = ".$user_id." where user_id = 0 and ecv_type_id = ".$id." limit 1");
			
			if($GLOBALS['db']->affected_rows() > 0){
				$root['status'] = 1;
				$root['info']="您已成功领取红包".round($ecvtype_info['money'],2)."元";
				$root['id']=$id;
				modify_account(array('money'=>round($ecvtype_info['money'],2)),$user_id,"成功领取红包".round($ecvtype_info['money'],2)."元");
			}else{
				$root['status'] = 0;
				$root['info'] ="剩余数量不足";
				output($root);
			}
		}
		output($root);
	}
}
?>
<?php
class ecvtype
{
	public function index()
	{

		//$email = addslashes($GLOBALS['request']['email']);//用户名或邮箱
	//	$pwd = addslashes($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
	//	$user = user_check($email,$pwd);
		//print_r($user);exit;		
		//$user_id  = intval($user['id']);
		
		//if ($user_id == 0){

		//	$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
	//		output($root);
	//	}	
		

		$uname = strim($GLOBALS['request']['uname']);

		$sql = "select * from ".DB_PREFIX."ecv_type  where uname = '".$uname."'";


		//echo $sql; exit;
		$ecvtype = $GLOBALS['db']->getRow($sql);
		if($ecvtype){
			$ecvtype['desc']=str_replace("./public/","../public/",$ecvtype['desc']);
		}
		$root =$ecvtype;
		$root['page_title']=$ecvtype['name'];
		output($root);
	}
}
?>
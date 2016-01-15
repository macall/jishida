<?php
class verify_phone{
	public function index()
	{
		$mobile_phone=trim($GLOBALS['request']['mobile']);
		//print_r($GLOBALS['request']);
		$root = array();
		$root['return'] = 1;

		if(app_conf("SMS_ON")==0)
		{
			$root['status'] = 0;
			$root['info'] = '短信功能关闭';//$GLOBALS['lang']['SMS_OFF'];
			output($root);
		}
		
		if($mobile_phone == '')
		{
			$root['status'] = 0;
			$root['info'] = '手机号码不能为空';
			output($root);
		}
		
		if(!check_mobile($mobile_phone))
		{
			$root['status'] = 0;
			$root['info'] = "请输入正确的手机号码";
			output($root);
		}
		
		if(!check_ipop_limit(CLIENT_IP,"mobile_verify",60,0))
		{
			$root['status'] = 0;
			$root['info'] = '发送太快了';
			output($root);
		}
		
		$have_user_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where mobile = '$mobile_phone'");
		
		if($have_user_id){
			//已经验证的
			$root['info'] = '该手机号码已经注册过!';
			$root['status']=0;
			output($root);
		}

		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);			
		if($user_id>0)
		{
			$root['user_login_status'] = 1;		

			
		//删除超过5分钟的验证码
			$sql = "DELETE FROM ".DB_PREFIX."sms_mobile_verify WHERE mobile_phone = '$mobile_phone' and add_time <=".(get_gmtime()-300);
			//$root['sql']=$sql;
			$GLOBALS['db']->query($sql);
							
			
			$smsSubscribe = $GLOBALS['db']->getRow("select `id`,`mobile_phone`,`code`,`send_count`,`add_time` from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '$mobile_phone' and type=0 order by id desc");
			
			$new_time=get_gmtime();
			$difftime=$new_time-$smsSubscribe['add_time'];
			if($smsSubscribe && intval($smsSubscribe['send_count']) <= 1 && $difftime <61)
			{
				$root['info']="验证码已发出,请注意查收";
				$root['status']=1;
				output($root);
			}
			else
			{							
				if (empty($smsSubscribe) || empty($smsSubscribe['code'])){
					//$tempcode = unpack('H4',str_shuffle(md5(uniqid())));
					$code = rand(1111,9999);//$tempcode[1];
				}else{
					//发送一样的，验证码;
					$code = $smsSubscribe['code'];
				}
				
				$message=$code."（".app_conf("SHOP_TITLE")."手机绑定验证码,请完成验证）,如非本人操作,请勿略本短信";
				require_once APP_ROOT_PATH."system/utils/es_sms.php";
				$sms = new sms_sender();
				$send=$sms->sendSms($mobile_phone,$message);
				//$send['status']=1;
				if($send['status'])
				{		
					$add_time = get_gmtime();
					$re=$GLOBALS['db']->query("insert into ".DB_PREFIX."sms_mobile_verify(mobile_phone,code,add_time,send_count,ip) values('$mobile_phone','$code','$add_time',1,"."'".CLIENT_IP."')");
					
					/*插入一条发送成功记录到队列表中*/
					$msg_data['dest'] = $mobile_phone;
					$msg_data['send_type'] = 0;
					$msg_data['content'] = addslashes($message);;
					$msg_data['send_time'] = $add_time;
					$msg_data['is_send'] = 1;
					$msg_data['is_success'] = 1;
					$msg_data['create_time'] = $add_time;
					$msg_data['user_id'] = intval($have_user_id);
					$msg_data['title'] = "手机号绑定验证";
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); 
		
					$root['info']="验证码发出,请注意查收";
					$root['status']=1;
				}
				else
				{				
					$root['info']="发送失败";
					$root['status']=0;
				}
			}
		}
		else
		{
			$root['user_login_status'] = 0;		
		}		
		output($root);
	}
}
?>
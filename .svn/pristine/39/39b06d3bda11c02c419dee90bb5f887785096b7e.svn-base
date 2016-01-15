<?php
class register_verify_phone{
	public function index()
	{
		
		/*
		//创建验证码表  ,如果表存在则不创建
		$table=$GLOBALS['db_config']['DB_PREFIX']."sms_mobile_verify";
		$create_table="CREATE TABLE IF NOT EXISTS `".$table."` (
					  `id` int(11) NOT NULL auto_increment,
					  `mobile_phone` varchar(50) NOT NULL default '',
					  `code` varchar(20) NOT NULL default '',
					  `status` tinyint(1) NOT NULL default '0',
					  `add_time` int(10) default NULL,
					  `send_count` int(11) NOT NULL default '0',
					  `type` tinyint(1) NOT NULL default '0',
					  PRIMARY KEY  (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	   $GLOBALS['db']->query($create_table,'SILENT');
		//end
		 */
		$mobile_phone=trim($GLOBALS['request']['mobile']);
		
		$is_login=intval($GLOBALS['request']['is_login']);//is_login 0:仅注册,会判断手机号码,是否存在; 1:可登陆,可注册 不判断手机号码是否存在;
		

		$root = array();
		//$root['return'] = 1;
		/*
		$isMobile = preg_match("/^(13\d{9}|14\d{9}|18\d{9}|15\d{9})|(0\d{9}|9\d{8})$/",$mobile_phone);
		if(!$isMobile)
		{
			$root['info']="请输入正确的手机号码";
			$root['status']=0;
			output($root);
		}	
		*/
		
		if(app_conf("SMS_ON")==0)
		{
			$root['status'] = 0;
			$root['info'] = '短信功能关闭';//$GLOBALS['lang']['SMS_OFF'];
			output($root);
		}
				
		if($mobile_phone =='')
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
				
		if(!check_ipop_limit(CLIENT_IP,"register_verify_phone",60,0))
		{
			$root['status'] = 0;
			$root['info'] = '发送太快了';
			output($root);
		}
				
		$have_user_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where mobile = '$mobile_phone'");
		
		//is_login 0:仅注册,会判断手机号码,是否存在; 1:可登陆,可注册 不判断手机号码是否存在;
		if($is_login == 0 && $have_user_id){
			//已经验证的
			$root['info'] = '该手机号码已经注册过!';
			$root['status']=0;
			output($root);
		}
		
		
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
					$root['info']="发送失败".$send['msg'];
					$root['status']=0;
				}
			
		}
		output($root);
	}
}
?>
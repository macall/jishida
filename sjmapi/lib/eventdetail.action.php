<?php
//参数:event_id
class eventdetail
{
	public function index()
	{	
		
	
		require_once APP_ROOT_PATH."system/model/user.php";

		if(strim($GLOBALS['request']['act_2'])=='bm')
		{
			$root['status'] = 1;
			$city_name =strim($GLOBALS['request']['city_name']);//城市名称
			//检查用户,用户密码
			$user_data = $GLOBALS['user_info'];
			
			//报名
			if($user_data)
			{
				$root['user_login_status'] = 1;
				
				$event_id = intval($GLOBALS['request']['event_id']);
				$user_id = intval($GLOBALS['user_info']['id']);
				require_once APP_ROOT_PATH."system/model/event.php";
// 				$event = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event where id = ".$event_id." and is_effect = 1");
				$event = get_event($event_id);
				
				if($event)
				{
					if($event['xpoint']=='')
					{
						$event['xpoint']=0;
					}
					if($event['ypoint']=='')
					{
						$event['ypoint']=0;
					}
								

					if($event['submit_begin_time']>NOW_TIME)
					{
						$root['return'] = 0;
						$root['info'] = "活动未开始";
					}
					elseif($event['submit_end_time']>0&&$event['submit_end_time']<NOW_TIME)
					{
						$root['return'] = 0;
						$root['info'] = "活动报名已结束";
					}
					elseif($event['submit_count']>=$event['total_count']&&$event['total_count']>0)
					{
						$root['return'] = 0;
						$root['info'] = "活动名额已满";
					}					
					else
					{
						//开始提交报名
						$user_id = intval($GLOBALS['user_info']['id']);						
						$user_submit = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where user_id = ".$user_id." and event_id = ".$event_id);
						if($user_submit)
						{
							
							if($user_submit['is_verify']==1)
							{
								$root['return'] = 0;
							$root['info'] = "您已经报过名了";
							}
							elseif($user_submit['is_verify']==2)
							{
								$root['return'] = 0;
							$root['info'] = "您的报名审核不通过";
							}
							else
							{
								//已经报名，仅作修改
								$bm = $GLOBALS['request']['bm'];
								
								$GLOBALS['db']->query("delete from ".DB_PREFIX."event_submit_field where submit_id = ".$user_submit['id']);
								foreach($bm as $field_id=>$bm_result)
								{
									$field_data = array();
									$field_data['submit_id'] = $user_submit['id'];
									$field_data['field_id'] = $field_id;
									$field_data['event_id'] = $event_id;
									$field_data['result'] = strim($bm_result);
									$GLOBALS['db']->autoExecute(DB_PREFIX."event_submit_field",$field_data,"INSERT");
								}
								$root['return'] = 1;
								$root['info'] = "报名修改成功";
							}
							
						}
						else
						{
							
							$submit_data = array();
							$submit_data['user_id'] = $user_id;
							$submit_data['event_id'] = $event_id;
							$submit_data['create_time'] = get_gmtime();
							$GLOBALS['db']->autoExecute(DB_PREFIX."event_submit",$submit_data,"INSERT");
							$submit_id = $GLOBALS['db']->insert_id();
							if($submit_id)
							{
								
								$bm = $GLOBALS['request']['bm'];
								//file_put_contents(APP_ROOT_PATH. "sjmapi/log/bm_".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($GLOBALS['request'],true));								
								//$bm = (unserialize($GLOBALS['request']['bm']));								
								foreach($bm as $field_id=>$bm_result)
								{	
													
									$field_data = array();
									$field_data['submit_id'] = $submit_id;
									$field_data['field_id'] = $field_id;
									$field_data['event_id'] = $event_id;
									$field_data['result'] = strim($bm_result);
									
									
									$GLOBALS['db']->autoExecute(DB_PREFIX."event_submit_field",$field_data,"INSERT");
								}
								
								$GLOBALS['db']->query("update ".DB_PREFIX."event set submit_count = submit_count+1 where id=".$event_id);
								
								
								if($event['is_auto_verify']==1)
								{
									//自动审核，发券
									$sn = verify_event_submit($submit_id);
								}
								
								//同步分享
// 								$title = "报名参加了".$event['name'];
// 								$content = "报名参加了".$event['name']." - ".$event['brief'];
// 								$url_route = array(
// 										'rel_app_index'	=>	'youhui',
// 										'rel_route'	=>	'edetail',
// 										'rel_param' => 'id='.$event['id']
// 								);							
								
// 								$tid = insert_topic($content,$title,$type="eventsubmit",$group="", $relay_id = 0, $fav_id = 0,$group_data ="",$attach_list=array(),$url_route);
// 								if($tid)
// 								{
// 									$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '".$GLOBALS['request']['source']."' where id = ".intval($tid));
// 								}

								//同步分享
								$title = "报名参加了".$event['name'];
								$content = "报名参加了".$event['name']." - ".$event['brief'];
								$url_route = array(
										'rel_app_index'	=>	'index',
										'rel_route'	=>	'event#'.$event['id'],
										'rel_param' => ''
								);
									
								require_once APP_ROOT_PATH."system/model/topic.php";
								$tid = insert_topic($content,$title,$type="eventsubmit",$group="", $relay_id = 0, $fav_id = 0,$group_data ="",$attach_list=array(),$url_route);
								if($tid)
								{
									$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '".$GLOBALS['request']['source']."' where id = ".intval($tid));
								}
									
								require_once APP_ROOT_PATH."system/model/user.php";
								modify_account(array("score"=>"-".$event['score_limit']), $user_id,"活动报名：".$event['name']);
								
								$root['return'] = 1;
								$root['info'] = "报名成功";
								if($sn)
									$root['info'] .= " 验证码：".$sn;
								else
									$root['info'] .= " 请等待审核";
								
								rm_auto_cache("event",array("id"=>$event['id']));
							}
							else
							{
								$root['return'] = 0;
								$root['info'] = "报名失败";
							}
							
						}
					}
				}
				else
				{
					$root['return'] = 0;
					$root['info'] = "没有该活动数据";
				}
			}
			else
			{
				$root['return'] = 0;
				$root['user_login_status'] = 0;
				$root['info'] = "请先登录";
			}
			output($root);
			//报名
			

		}
		
		//报名结束
		
		$page = intval($GLOBALS['request']['page']); //分页,无用
		if($page==0)
			$page = 1;
		$event_id = intval($GLOBALS['request']['event_id']);
		if($event_id)
		{			
	
			$user_data = $GLOBALS['user_info'];
			
			require_once APP_ROOT_PATH."system/model/event.php";
			$event = get_event($event_id);
			
			if($event['xpoint']=='')
			{
				$event['xpoint']=0;
			}
			if($event['ypoint']=='')
			{
				$event['ypoint']=0;
			}
			
			
			
			$pattern = "/<img([^>]*)\/>/i";
			$replacement = "<img width=300 $1 />";

			$event['icon']=get_abs_img_root($event['icon']);

			$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/i";
			//$replacement = "<img width=300 $1 />";
			$replacement = "<img src='$1' width='278' />";
			$event['content'] = get_abs_img_root(preg_replace($pattern, $replacement, $event['content']));
			
			//$event['content'] = get_abs_img_root(get_spec_image($event['content'], 278,168,1));
			$event['content'] = preg_replace($pattern, $replacement, $event['content']);
			$event['event_begin_time'] = to_date($event['event_begin_time'],'Y-m-d');
			$event['event_end_time'] = to_date($event['event_end_time'],'Y-m-d');	
			
			//验证是否报名
			//$is_submit = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_submit where user_id = ".intval($GLOBALS['user_info']['id'])." and event_id = ".$event['id']);
					
// 			$event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$event_id." order by sort asc");
// 			foreach($event_fields as $k=>$v)
// 			{
// 				$event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
// 			}
// 			$event['field_list'] = $event_fields;
// 			$event['is_submit'] = $is_submit;
// 			$event['field_list_json']=json_encode($event_fields);

			
			$user_submit = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where user_id = ".$user_data['id']." and event_id = ".$event_id);
			if($user_submit)
			{
				if($user_submit['is_verify']==1)
				{
					$event['is_submit'] = 1; //已报名
					$event['is_verify'] = 1; //已审核
				}
				elseif($user_submit['is_verify']==2)
				{
					$event['is_submit'] = 1; //已报名
					$event['is_verify'] = 2; //审核失败
				}
				else
				{
					//未审核
					$event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$event_id." order by sort asc");
					foreach($event_fields as $k=>$v)
					{
						$event_fields[$k]['result'] = $GLOBALS['db']->getOne("select result from ".DB_PREFIX."event_submit_field where submit_id = ".$user_submit['id']." and field_id = ".$v['id']." and event_id = ".$event_id);
						$event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
					}
					
					$event['event_fields'] = $event_fields;
					
					$event['is_submit'] = 1; //已报名
					$event['is_verify'] = 0; //未审核
					
// 					$GLOBALS['tmpl']->assign("event_fields",$event_fields);
// 					$GLOBALS['tmpl']->assign("user_submit",$user_submit);  //表示修改已报名记录
// 					$GLOBALS['tmpl']->assign("btn_name","修改报名");
				}
			}
			else
			{
				$event_fields = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_field where event_id = ".$event_id." order by sort asc");
				foreach($event_fields as $k=>$v)
				{
					$event_fields[$k]['value_scope'] = explode(" ",$v['value_scope']);
				}
				$event['event_fields'] = $event_fields;					
				$event['is_submit'] = 0; //已报名
			}
			
		}
		
		//$res = m_get_event_reply($event_id,$page);
		
		//$event['comments'] =  $res['list'];
		//$root['page'] = $res['page'];
		
		require_once APP_ROOT_PATH."system/model/review.php";
		require_once APP_ROOT_PATH."system/model/user.php";
		$message_re = get_dp_list(3,$param=array("deal_id"=>0,"youhui_id"=>0,"event_id"=>$event_id,"location_id"=>0,"tag"=>""),"","");
		
		foreach($message_re['list'] as $k=>$v)
		{
			$message_re['list'][$k]['width'] = ($v['point'] / 5) * 100;
			$uinfo = load_user($v['user_id']);
			$message_re['list'][$k]['user_name'] = $uinfo['user_name'];
			foreach($message_re['list'][$k]['images'] as $kk=>$vv)
			{
				$message_re['list'][$k]['images'][$kk] = get_abs_img_root(get_spec_image($vv,60,60,1));
				$message_re['list'][$k]['oimages'][$kk] = get_abs_img_root($vv);
			}
		}
		
		$root['message_list']=$message_re['list'];
		
		if(count($message_re['list'])>0)
		{
			$sql = "select count(*) from ".DB_PREFIX."supplier_location_dp where  ".$message_re['condition'];
			$message_re['count'] = $GLOBALS['db']->getOne($sql);
		}
		
		$root['message_count']=$message_re['count'];
		
		
		
		$root['return'] = 1;
		$root['item'] = $event;	
		$root['page_title']="活动详情";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
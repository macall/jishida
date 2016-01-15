<?php
class youhuiitem
{
	public function index()
	{

		$act_2 = $GLOBALS['request']['act_2'];//子操作 空:没子操作; dz:设置打折提醒;sc:(取消)收藏

		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//print_r($email);
		//print_r($pwd);
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		//print_r($user);exit;		
		$user_id  = intval($user['id']);
		
		if ($act_2 != '' && $user_id == 0){
			$root['act_2'] = $act_2;
			$root['user_login_status'] = 0;//用户登陆状态：1:成功登陆;0：未成功登陆
			output($root);
		}	
		

		$id = intval($GLOBALS['request']['id']);

		$sql = "update ".DB_PREFIX."youhui set view_count = view_count + 1 where id = ".$id;
		$GLOBALS['db']->query($sql);

		//sc:(取消)收藏
		if ($act_2 =="sc"){
			$sql = "select uid from  ".DB_PREFIX."youhui_sc where uid = $user_id and youhui_id = $id";
			if (intval($GLOBALS['db']->getOne($sql) > 0)) {
				//已经设置打折提醒，则取消
				$sql = "delete from ".DB_PREFIX."youhui_sc where uid = $user_id and youhui_id = $id";
				$GLOBALS['db']->query($sql);
			}else{
				//没设置，则设置
				$merchant_youhui_sc = array(
					'uid' => $user_id,
					'youhui_id' => $id
				);
				$GLOBALS['db']->autoExecute(DB_PREFIX."youhui_sc", $merchant_youhui_sc, 'INSERT');
			}
		}





		$sql = "select a.id, a.supplier_id as merchant_id,a.is_sms, a.name as title, a.icon as merchant_logo,a.create_time,a.xpoint,a.ypoint,a.address as api_address,a.icon as image_1, a.image_3,a.image_3_w,a.image_3_h, a.begin_time,a.end_time, a.description as content,a.use_notice,a.view_count,a.print_count,a.sms_count,".
			   "(select count(*) from ".DB_PREFIX."youhui_sc as b where b.uid = $user_id and b.youhui_id = a.id) as is_sc, ".
				"(select count(*) from ".DB_PREFIX."message as c where c.rel_table = 'youhui' and c.rel_id = a.id) as comment_count, ".
				"(select name from ".DB_PREFIX."deal_city as d where d.id = a.city_id) as city_name ".
				" from ".DB_PREFIX."youhui as a where a.id = $id ";

		//file_put_contents(APP_ROOT_PATH. "sjmapi/log/sql_".strftime("%Y%m%d%H%M%S",time()).".txt",$sql);
		//echo $sql; exit;
		$youhui = $GLOBALS['db']->getRow($sql);

		$root = m_youhuiItem($youhui);

		$root['logo']=get_abs_img_root(get_spec_image($root['logo'],320,194,0));;
	
		$root['down_count'] = $root['sms_count'] + $root['print_count'];
		//$root['merchant_id'] = 0;
		$root['act_2'] = $act_2;
		$root['user_login_status'] = 1;
        //print_r($root);exit;
		//分享信息
		//$site_url = ';网址:'.str_replace($_FANWE['site_root'],'',$_FANWE['site_url']).FU('yh/detail',array('id'=>$youhui['id']));
		//$site_url = get_domain().url("youhui","ydetail",array("id"=>$deal['id']));
		$site_url = get_domain().url("youhui","fdetail",array("id"=>$youhui['id']));
		$site_url = str_replace('/sjmapi/','/',$site_url);
		$root['share_content'] = msubstr($youhui['title'],0,140 - strlen($site_url) - 3).$site_url;

		$root['return'] = 1;

		$merchant_id = intval($youhui[merchant_id]);

		if ($merchant_id > 0){

			if ($act_2 == "dz"){
				$sql = "select uid from  ".DB_PREFIX."supplier_dy where uid = $user_id and supplier_id = $merchant_id";
				if (intval($GLOBALS['db']->getOne($sql) > 0)) {
					//已经设置打折提醒，则取消
					$sql = "delete from ".DB_PREFIX."supplier_dy where uid = $user_id and supplier_id = $merchant_id";
					$GLOBALS['db']->query($sql);
				}else{
					//没设置，则设置
					$merchant_dy = array(
						 						'uid' => $user_id,
						 						'supplier_id' => $merchant_id
					);
					$GLOBALS['db']->autoExecute(DB_PREFIX."supplier_dy", $merchant_dy, 'INSERT');
				}
			}


		
			
			$sql = "select a.id,a.name,a.content as brief,a.preview as logo, b.uid as is_dy from ".DB_PREFIX."supplier as a ".
			 			   " left outer join ".DB_PREFIX."supplier_dy as b on b.uid = $user_id and b.supplier_id = a.id ".						   
							"where a.id = $merchant_id ";
			
			//echo $sql; exit;
			$merchant = $GLOBALS['db']->getRow($sql);
			$merchant = m_merchantItem($merchant);

			$root['merchant'] = $merchant;
			
			
			
			$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
			$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
			$pi = 3.14159265;  //圆周率
			$r = 6378137;  //地球平均半径(米)
			/*
			if($GLOBALS['request']['from']=="wap"){
					//购买评论			
				$message_re=m_get_message_list(3," m.rel_table = 'deal' and m.rel_id=".$id." and m.is_buy = 1");//购买评论
				$root['message_list']=$message_re['list']; 
				$root['message_count']=$message_re['count'];
				$supplier_location_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."supplier_location where supplier_id=".$merchant_id);
	
				//门店评论
				$comment_list=$GLOBALS['db']->getAll("select a.id,a.content,a.point,a.avg_price,a.create_time,b.id as user_id,b.user_name from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$supplier_location_id." and a.status = 1");
				$youhui_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_table = 'youhui' and rel_id=".$id." and user_id=".$user_id);
				$comment_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location_dp as a left join ".DB_PREFIX."user as b on b.id=a.user_id where a.supplier_location_id = ".$supplier_location_id." and a.status = 1");
				$count_point=0;
				foreach($comment_list as $k=>$v)
				{
					$comment_list[$k]['avg_price']=round($v['avg_price'],2);
					$comment_list[$k]['time']=pass_date($v['create_time']);
					$count_point+=$v['point'];
				}
				$root['comment_list']=$comment_list;
				$root['youhui_count']=$youhui_count;
				$score=round($count_point/$comment_count,2);
			
				$width = $score > 0 ? ($score / 5) * 100 : 0;
				$root['point']=$score;
				$root['width']=$width;
				$root['comment_count']=$comment_count;
					//fwb 2014-08-27
			}//fwb add 2014-08-27
			*/
			
			$sql = "select a.id,a.name,a.address,a.api_address, a.tel,a.supplier_id as brand_id,a.brief,a.preview as logo,a.xpoint,a.ypoint,a.route, (select count(*) from ".DB_PREFIX."message as m where m.rel_table = 'supplier_location' and m.rel_id = a.id) as comment_count,c.name as city_name,
				(ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((a.ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((a.ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (a.xpoint * $pi) / 180 ) ) * $r) as distance 
				 from ".DB_PREFIX."supplier_location as a ".			 			   
						   " left outer join ".DB_PREFIX."deal_city as c on c.id = a.city_id ".
							"where a.supplier_id = $merchant_id ";			
			$list = $GLOBALS['db']->getAll($sql);
			$list_merchant = array();
			foreach($list as $item){			
				$list_merchant[] = m_merchantItem($item);
			}
			$root['list_merchant'] = $list_merchant;			
		}
		
		
		require_once APP_ROOT_PATH."system/model/review.php";
		require_once APP_ROOT_PATH."system/model/user.php";
		
		$message_re = get_dp_list(3,$param=array("deal_id"=>0,"youhui_id"=>$youhui['id'],"event_id"=>0,"location_id"=>0,"tag"=>""),"","");
		
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
		
		$root['city_name']=$city_name;
		
		$root['page_title']="优惠券详情";
		
		output($root);
	}
}
?>
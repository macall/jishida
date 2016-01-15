<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

define("YOUHUI_OUT_OF_STOCK",4); //库存不足
define("YOUHUI_USER_OUT_OF_STOCK",5); //每日库存不足
define("YOUHUI_LIMIT",7); //条件不足以兑换（积分不足，经验不够）

define("YOUHUI_NOTICE",0); //未上线
define("YOUHUI_ONLINE",1); //进行中
define("YOUHUI_HISTORY",2); //过期

define("YOUHUI_DOWNLOAD_SUCCESS",6); //优惠券领取成功




//获取优惠券详情
function get_youhui($id,$preview=false)
{
	static $youhuis;
	$youhui = $youhuis[$id];
	if($youhui)return $youhui;
	
	$youhui = load_auto_cache("youhui",array("id"=>$id));	
	
	if($youhui)
	{
		if(!$preview&&$youhui['is_effect']==0)
			return false;
		//商户信息
		if($youhui['supplier_id']>0)
		{
			$youhui['supplier_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".intval($youhui['supplier_id']));
			$youhui['supplier_location_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_location_link where youhui_id = ".$youhui['id']);
			//$deal['supplier_address_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".intval($deal['supplier_id'])." and is_main = 1");
		}
		
		if($youhui['youhui_type']==1)
		{
			//减免:代金券
			$youhui['tags'][] = 4;
		}
		else
		{
			$youhui['tags'][] = 9;
		}
		if($youhui['total_num']>0)
			$youhui['less'] = $youhui['total_num'] - $youhui['user_count'];
		else
			$youhui['less'] = -1;
		
		$durl = $youhui['url'];
			
		$youhui['share_url'] = SITE_DOMAIN.$durl;
		if($GLOBALS['user_info'])
		{
			if(app_conf("URL_MODEL")==0)
			{
				$youhui['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
			}
			else
			{
				$youhui['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
			}
		}
		
		$youhuis[$id] = $youhui;	
	}
	return $youhui;
}


function get_youhui_count($type=array(YOUHUI_NOTICE,YOUHUI_ONLINE,YOUHUI_HISTORY),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0), $join='', $where='')
{
	if(empty($param))
		$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0);

	$tname = "y";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and  ( 1<>1 ';
	if(in_array(YOUHUI_ONLINE,$type))
	{
		//进行中的
		$condition .= " or ((".$time.">= ".$tname.".begin_time or ".$tname.".begin_time = 0) and (".$time."< ".$tname.".end_time or ".$tname.".end_time = 0) ) ";
	}

	if(in_array(YOUHUI_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".end_time and ".$tname.".end_time <> 0)) ";
	}
	if(in_array(YOUHUI_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".begin_time and ".$tname.".begin_time <> 0 )) ";
	}

	$condition .= ')';


	$param_condition = build_youhui_filter_condition($param,$tname);
	$condition.=" ".$param_condition;

	if($where != '')
	{
		$condition.=" and ".$where;
	}

	if($join)
		$sql = "select count(*) from ".DB_PREFIX."youhui as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select count(*) from ".DB_PREFIX."youhui as ".$tname." where  ".$condition;



	$count = $GLOBALS['db']->getOne($sql,false);
	
	return $count;
	
}
/**
 * 获取优惠券列表
 */
function get_youhui_list($limit,$type=array(YOUHUI_NOTICE,YOUHUI_ONLINE,YOUHUI_HISTORY),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0), $join='', $where='',$orderby = '',$append_field="")
{
	if(empty($param))
		$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0);

	$tname = "y";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and  ( 1<>1 ';
	if(in_array(YOUHUI_ONLINE,$type))
	{
		//进行中的
		$condition .= " or ((".$time.">= ".$tname.".begin_time or ".$tname.".begin_time = 0) and (".$time."< ".$tname.".end_time or ".$tname.".end_time = 0) ) ";
	}

	if(in_array(YOUHUI_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".end_time and ".$tname.".end_time <> 0)) ";
	}
	if(in_array(YOUHUI_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".begin_time and ".$tname.".begin_time <> 0 )) ";
	}

	$condition .= ')';


	$param_condition = build_youhui_filter_condition($param,$tname);
	$condition.=" ".$param_condition;

	if($where != '')
	{
		$condition.=" and ".$where;
	}

	if($join)
		$sql = "select ".$tname.".*".$append_field." from ".DB_PREFIX."youhui as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select ".$tname.".*".$append_field." from ".DB_PREFIX."youhui as ".$tname." where  ".$condition;

	if($orderby=='')
		$sql.=" order by ".$tname.".sort desc limit ".$limit;
	else
		$sql.=" order by ".$orderby." limit ".$limit;

	$youhuis = $GLOBALS['db']->getAll($sql,false);
// 			echo $sql;exit;
	if($youhuis)
	{
		foreach($youhuis as $k=>$youhui)
		{
			//格式化数据
			$youhui['begin_time_format'] = to_date($youhui['begin_time']);
			$youhui['end_time_format'] = to_date($youhui['end_time']);

			$durl = url("index","youhui#".$youhui['id']);
			$youhui['share_url'] = SITE_DOMAIN.$durl;
			$youhui['url'] = $durl;


			if($GLOBALS['user_info'])
			{
				if(app_conf("URL_MODEL")==0)
				{
					$youhui['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
				}
				else
				{
					$youhui['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
				}
			}
				

			if($youhui['youhui_type']==1)
			{
				//减免:代金券
				$youhui['tags'][] = 4;
			}
			else
			{
				$youhui['tags'][] = 9;
			}

			$youhui['percent'] = $youhui['avg_point']/5.0*100.0;
			
			if($youhui['total_num']>0)
				$youhui['less'] = $youhui['total_num'] - $youhui['user_count'];
			else
				$youhui['less'] = -1;
			
			$youhuis[$k] = $youhui;
		}
		}
		return array('list'=>$youhuis,'condition'=>$condition);
}


/**
 * 构建优惠查询条件
 * @param unknown_type $param
 * @return string
 */
function build_youhui_filter_condition($param,$tname="")
{
	$area_id = intval($param['aid']);
	$quan_id = intval($param['qid']);
	$cate_id = intval($param['cid']);
	$deal_type_id = intval($param['tid']);
	$city_id = intval($param['city_id']);
	$condition = "";
	if($city_id>0)
	{
		$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
		if($ids)
		{
			if($tname)
				$condition .= " and ".$tname.".city_id in (".implode(",",$ids).")";
			else
				$condition .= " and city_id in (".implode(",",$ids).")";
		}
	}
	if($area_id>0)
	{
		if($quan_id>0)
		{

			$area_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."area where id = ".$quan_id);
			$kw_unicodes[] = str_to_unicode_string($area_name);
				
			$kw_unicode = implode(" ",$kw_unicodes);
			//有筛选
			if($tname)
				$condition .=" and (match(".$tname.".locate_match) against('".$kw_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .=" and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE)) ";
		}
		else
		{
			$ids = load_auto_cache("deal_quan_ids",array("quan_id"=>$area_id));
			$quan_list = $GLOBALS['db']->getAll("select `name` from ".DB_PREFIX."area where id in (".implode(",",$ids).")");
			$unicode_quans = array();
			foreach($quan_list as $k=>$v){
				$unicode_quans[] = str_to_unicode_string($v['name']);
			}
			$kw_unicode = implode(" ", $unicode_quans);
			if($tname)
				$condition .= " and (match(".$tname.".locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
			else
				$condition .= " and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
		}
	}

	if($cate_id>0)
	{
		$cate_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$cate_id);
		$cate_name_unicode = str_to_unicode_string($cate_name);
			
		if($deal_type_id>0)
		{
			$deal_type_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate_type where id = ".$deal_type_id);
			$deal_type_name_unicode = str_to_unicode_string($deal_type_name);
			if($tname)
				$condition .= " and (match(".$tname.".deal_cate_match) against('+".$cate_name_unicode." +".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .= " and (match(deal_cate_match) against('+".$cate_name_unicode." +".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
		}
		else
		{
			if($tname)
				$condition .= " and (match(".$tname.".deal_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .= " and (match(deal_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
		}
	}
	return $condition;
}

/**
 * 下载优惠券
 * @param unknown_type $id
 * @param unknown_type $user_id
 * 
 * 返回
 * array("status"=>"结果状态","info"=>"消息","log"=>"领取的优惠券记录");
 * status:1领取成功 0.领取失败 2.库存已满 3.时间超期
 */
function download_youhui($id,$user_id)
{
	$youhui_info  = get_youhui($id);
	if($youhui_info)
	{
		//判断时间，库存与每日限量
		if($youhui_info['begin_time']!=0&&$youhui_info['begin_time']>NOW_TIME)
		{
			$data['status']= YOUHUI_NOTICE; //未上线
			$data['info'] = "活动未开始,优惠券不能领取";
			return $data;
		}
		elseif($youhui_info['end_time']!=0&&$youhui_info['end_time']<=NOW_TIME)
		{
			$data['status']= YOUHUI_HISTORY; //过期
			$data['info'] = "活动已过期,优惠券不能领取";
			return $data;
		}
		else
		{
			if($youhui_info['user_limit']>0)
			{
				$date_begin = to_timespan(to_date(NOW_TIME,"Y-m-d"),"Y-m-d");
				$date_end = $date_begin+24*3600;
				//验证每日限量
				$user_day_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."youhui_log where user_id = ".$user_id." and youhui_id = ".$youhui_info['id']." and create_time > ".$date_begin." and create_time < ".$date_end);
				if($user_day_count>=$youhui_info['user_limit'])
				{
					$log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui_log where user_id = ".$user_id." and youhui_id = ".$youhui_info['id']." and create_time > ".$date_begin." and create_time < ".$date_end." and confirm_time = 0 order by create_time desc");					
					$data['status']= YOUHUI_USER_OUT_OF_STOCK; //会员每日限量已满
					if($log)
					{
						$data['info'] = "您今日已经领取了".$user_day_count."张优惠券，请去会员中心查看";
						$data['log'] = $log;  //有log需跳转
					}
					else
					{
						$data['info'] = "您今日已经领取了".$user_day_count."张优惠券";
					}
					return $data;
				}
			}
			
			if($youhui_info['score_limit']>0||$youhui_info['point_limit']>0)
			{
				if($GLOBALS['user_info']['id']!=$user_id)
					$c_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
				else
					$c_user_info = $GLOBALS['user_info'];
				
				if($c_user_info['score']<$youhui_info['score_limit'])
				{
					$data['status'] = YOUHUI_LIMIT;
					$data['info'] = "积分不足，不能下载";
					return $data;
				}
				
				if($c_user_info['point']<$youhui_info['point_limit'])
				{
					$data['status'] = YOUHUI_LIMIT;
					$data['info'] = "经验不足，不能下载";
					return $data;
				}
			}
			
			//领取库存验证
			$sql  = "update ".DB_PREFIX."youhui set user_count = user_count + 1 where id = ".$youhui_info['id']." and user_count + 1 <= total_num";			
			$GLOBALS['db']->query($sql);
			if($GLOBALS['db']->affected_rows()>0)
			{
				//执行领取
				$log = array();
				$log['youhui_id'] = $youhui_info['id'];
				$log['user_id'] = $user_id;
				$log['mobile'] = $GLOBALS['db']->getOne("select mobile from ".DB_PREFIX."user where id = ".$user_id);
				$log['create_time'] = NOW_TIME;
				$log['return_money'] = $youhui_info['return_money'];
				$log['return_score'] = $youhui_info['return_score'];
				$log['return_point'] = $youhui_info['return_point'];
				if($youhui_info['expire_day']>0)	$log['expire_time'] = NOW_TIME + $youhui_info['expire_day']*3600*24;
				while(intval($log['id'])==0)
				{
					$log['youhui_sn'] = rand(100,999).$youhui_info['id'].rand(100,999);
					$GLOBALS['db']->autoExecute(DB_PREFIX."youhui_log",$log,'INSERT','','SILENT');
					$log['id'] = $GLOBALS['db']->insert_id();
				}
				
				require_once APP_ROOT_PATH."system/model/user.php";
				modify_account(array("score"=>"-".$youhui_info['score_limit']), $user_id,"下载优惠券".$youhui_info['name']);
				
				$data['status'] = YOUHUI_DOWNLOAD_SUCCESS;
				$data['info'] = "领取成功";
				$data['log'] = $log;
				return $data;
			}
			else
			{
				$log = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui_log where user_id = ".$user_id." and youhui_id = ".$youhui_info['id']." and confirm_time = 0 order by create_time desc"); 				
				$data['status']= YOUHUI_OUT_OF_STOCK; //限量已满
				if($log)
				{
					$data['info'] = "您来晚了，优惠券已领光，请去会员中心查看你已领到的优惠券";
					$data['log'] = $log;
				}
				else
				{
					$data['info'] = "您来晚了，优惠券已领光";
				}				
				return $data;
			}
		}
	}
	else
	{
		$data['status']= -1;
		$data['info'] = "优惠券不存在";
		return $data;
	}	
	
}

?>
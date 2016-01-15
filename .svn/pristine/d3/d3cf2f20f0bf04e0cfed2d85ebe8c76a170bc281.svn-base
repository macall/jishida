<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

define("EVENT_OUT_OF_STOCK",4); //库存不足

define("EVENT_NOTICE",0); //未上线
define("EVENT_ONLINE",1); //进行中
define("EVENT_HISTORY",2); //过期




//获取活动详情
function get_event($id,$preview=false)
{
	static $events;
	$event = $events[$id];
	if($event)return $event;
	
	$event = load_auto_cache("event",array("id"=>$id));	
	
	if($event)
	{
		if(!$preview&&$event['is_effect']==0) 
			return false;
		
		//商户信息
		if($event['supplier_id']>0)
		{
			$event['supplier_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".intval($event['supplier_id']));
			$event['supplier_location_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_location_link where event_id = ".$event['id']);
			//$deal['supplier_address_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".intval($deal['supplier_id'])." and is_main = 1");
		}
		$event['submit_begin_time_format'] = to_date($event['submit_begin_time'],"Y-m-d");
		$event['submit_end_time_format'] = to_date($event['submit_end_time'],"Y-m-d");
		$event['event_begin_time_format'] = to_date($event['event_begin_time'],"Y-m-d");
		$event['event_end_time_format'] = to_date($event['event_end_time'],"Y-m-d");
		
		
		$durl = $event['url'];
			
		$event['share_url'] = SITE_DOMAIN.$durl;
		if($GLOBALS['user_info'])
		{
			if(app_conf("URL_MODEL")==0)
			{
				$event['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
			}
			else
			{
				$event['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
			}
		}
		
		$events[$id] = $event;	
	}
	return $event;
}


function get_event_count($type=array(EVENT_NOTICE,EVENT_ONLINE,EVENT_HISTORY),$param=array("cid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0), $join='', $where='')
{
	if(empty($param))
		$param=array("cid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0);

	$tname = "e";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and  ( 1<>1 ';
	if(in_array(EVENT_ONLINE,$type))
	{
		//进行中的
		$condition .= " or ((".$time.">= ".$tname.".submit_begin_time or ".$tname.".submit_begin_time = 0) and (".$time."< ".$tname.".submit_end_time or ".$tname.".submit_end_time = 0) ) ";
	}

	if(in_array(EVENT_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".submit_end_time and ".$tname.".submit_end_time <> 0)) ";
	}
	if(in_array(EVENT_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".submit_begin_time and ".$tname.".submit_begin_time <> 0 )) ";
	}

	$condition .= ')';


	$param_condition = build_event_filter_condition($param,$tname);
	$condition.=" ".$param_condition;

	if($where != '')
	{
		$condition.=" and ".$where;
	}

	if($join)
		$sql = "select count(*) from ".DB_PREFIX."event as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select count(*) from ".DB_PREFIX."event as ".$tname." where  ".$condition;



	$count = $GLOBALS['db']->getOne($sql,false);
	return $count;
}
/**
 * 获取活动列表
 */
function get_event_list($limit,$type=array(EVENT_NOTICE,EVENT_ONLINE,EVENT_HISTORY),$param=array("cid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0), $join='', $where='',$orderby = '',$field_append="")
{
	if(empty($param))
		$param=array("cid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0);

	$tname = "e";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and  ( 1<>1 ';
	if(in_array(EVENT_ONLINE,$type))
	{
		//进行中的
		$condition .= " or ((".$time.">= ".$tname.".submit_begin_time or ".$tname.".submit_begin_time = 0) and (".$time."< ".$tname.".submit_end_time or ".$tname.".submit_end_time = 0) ) ";
	}

	if(in_array(EVENT_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".event_end_time and ".$tname.".event_end_time <> 0)) ";
	}
	if(in_array(EVENT_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".submit_begin_time and ".$tname.".submit_begin_time <> 0 )) ";
	}

	$condition .= ')';


	$param_condition = build_event_filter_condition($param,$tname);
	$condition.=" ".$param_condition;

	if($where != '')
	{
		$condition.=" and ".$where;
	}

	if($join)
		$sql = "select ".$tname.".*".$field_append." from ".DB_PREFIX."event as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select ".$tname.".*".$field_append." from ".DB_PREFIX."event as ".$tname." where  ".$condition;

	if($orderby=='')
		$sql.=" order by ".$tname.".sort desc limit ".$limit;
	else
		$sql.=" order by ".$orderby." limit ".$limit;


	$events = $GLOBALS['db']->getAll($sql,false);
	//		echo $count_sql;
	if($events)
	{
		foreach($events as $k=>$event)
		{
			//格式化数据
			$event['submit_begin_time_format'] = to_date($event['submit_begin_time'],"Y-m-d");
			$event['submit_end_time_format'] = to_date($event['submit_end_time'],"Y-m-d");
			$event['event_begin_time_format'] = to_date($event['event_begin_time'],"Y-m-d");
			$event['event_end_time_format'] = to_date($event['event_end_time'],"Y-m-d");

			$durl = url("index","event#".$event['id']);
			$event['share_url'] = SITE_DOMAIN.$durl;
			$event['url'] = $durl;


			if($GLOBALS['user_info'])
			{
				if(app_conf("URL_MODEL")==0)
				{
					$event['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
				}
				else
				{
					$event['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
				}
			}				


			$events[$k] = $event;
		}
		}
		return array('list'=>$events,'condition'=>$condition);
}


/**
 * 构建活动查询条件
 * @param unknown_type $param
 * @return string
 */
function build_event_filter_condition($param,$tname="")
{
	$area_id = intval($param['aid']);
	$quan_id = intval($param['qid']);
	$cate_id = intval($param['cid']);
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
		$cate_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."event_cate where id = ".$cate_id);
		$cate_name_unicode = str_to_unicode_string($cate_name);
		if($tname)
				$condition .= " and (match(".$tname.".cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .= " and (match(cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
	}
	return $condition;
}

/**
 * 审核活动报名：发序列号，以及相关短信邮件
 * @param unknown_type $submit_id
 */
function verify_event_submit($submit_id)
{
	$submit_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where id = ".$submit_id);
	if($submit_data['is_verify']==0)
	{
		do{
			$sn = rand(100,999).$submit_data['event_id'].rand(10,99);
			$GLOBALS['db']->query("update ".DB_PREFIX."event_submit set sn = '".$sn."',is_verify = 1 where id = ".$submit_id);
		}while($GLOBALS['db']->affected_rows()==0);	
		
		send_event_sn_mail($submit_id);
		send_event_sn_sms($submit_id);
	}
	else
	{
		$sn = $submit_data['sn'];
	}
	return $sn;
}
/**
 * 拒绝审核
 * @param unknown_type $submit_id
 */
function refuse_event_submit($submit_id)
{
	$submit_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event_submit where id = ".$submit_id);
	if($submit_data)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."event_submit set sn = '',is_verify = 2 where id = ".$submit_id);
		if($GLOBALS['db']->affected_rows())
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."event set submit_count = submit_count-1 where id=".$submit_data['event_id']);		
			rm_auto_cache("event",array("id"=>$submit_data['event_id']));
			return true;
		}		
	}
	return false;
}

?>
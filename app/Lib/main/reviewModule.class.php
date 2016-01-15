<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class reviewModule extends MainBaseModule
{
	public function index()
	{		
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		if(empty($GLOBALS['user_info']))
		{
			app_redirect(url("index","user#login"));
		}

		require_once APP_ROOT_PATH."system/model/review.php";
		
		$order_item_id = intval($_REQUEST['order_item_id']);  //订单商品ID
		$youhui_log_id = intval($_REQUEST['youhui_log_id']);  //优惠券领取日志ID
		$event_submit_id = intval($_REQUEST['event_submit_id']); //活动报名日志ID
		
		if($order_item_id>0)
		{
			$deal_id = intval($GLOBALS['db']->getOne("select deal_id from ".DB_PREFIX."deal_order_item where id = ".$order_item_id));
		}
		else
		{
			$deal_id = intval($_REQUEST['deal_id']);
		}
		
		if($youhui_log_id>0)
		{
			$youhui_id = intval($GLOBALS['db']->getOne("select youhui_id from ".DB_PREFIX."youhui_log where id = ".$youhui_log_id));
		}
		else
		{
			$youhui_id = intval($_REQUEST['youhui_id']);
		}
		
		if($event_submit_id>0)
		{
			$event_id = intval($GLOBALS['db']->getOne("select event_id from ".DB_PREFIX."event_submit where id = ".$event_submit_id));
		}
		else
		{
			$event_id = intval($_REQUEST['event_id']);
		}		
		
		$location_id = intval($_REQUEST['location_id']);
		
		if($deal_id>0)
		{
			require_once APP_ROOT_PATH."system/model/deal.php";
			$deal_info = get_deal($deal_id);
			if($deal_info)
			{
				//验证是否可以点评
				$checker = check_dp_status($GLOBALS['user_info']['id'],array("deal_id"=>$deal_id,"order_item_id"=>$order_item_id));
				if(!$checker['status'])
				{
					showErr($checker['info'],0,$deal_info['url']);
				}
							
				
				$dp_data = load_dp_info(array("deal_id"=>$deal_id));
				if($deal_info['is_shop']==1)
					$dp_cfg = load_dp_cfg(array("scate_id"=>$deal_info['shop_cate_id']));
				else
					$dp_cfg = load_dp_cfg(array("cate_id"=>$deal_info['cate_id']));
				
				$item_info['id'] = $deal_info['id'];
				$item_info['key'] = 'deal_id';
				$item_info['ex_key'] = 'order_item_id';
				$item_info['ex_id'] = $order_item_id;
				$item_info['name'] = $deal_info['sub_name'];
				$item_info['detail'] = $deal_info['name'];
				$item_info['url'] = $deal_info['url'];
				$item_info['image'] = $deal_info['icon'];
				
				$GLOBALS['tmpl']->assign("dp_data",$dp_data);
				$GLOBALS['tmpl']->assign("dp_cfg",$dp_cfg);
				$GLOBALS['tmpl']->assign("item_info",$item_info);
				//print_r($dp_cfg);
				
				//输出导航
				$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>url("index"));		
				$site_nav[] = array('name'=>$deal_info['sub_name'],'url'=>url("index","review",array("deal_id"=>$deal_info['id'])));
				$GLOBALS['tmpl']->assign("site_nav",$site_nav);
				
				//输出seo
				$page_title = "";
				$page_keyword = "";
				$page_description = "";
				if($deal_info['supplier_info']['name'])
				{
					$page_title.="[".$deal_info['supplier_info']['name']."]";
					$page_keyword.=$deal_info['supplier_info']['name'].",";
					$page_description.=$deal_info['supplier_info']['name'].",";
				}
				$page_title.= $deal_info['sub_name'];
				$page_keyword.=$deal_info['sub_name'];
				$page_description.=$deal_info['sub_name'];
				$GLOBALS['tmpl']->assign("page_title",$page_title);
				$GLOBALS['tmpl']->assign("page_keyword",$page_keyword);
				$GLOBALS['tmpl']->assign("page_description",$page_description);
				
				//输出右侧的其他团购
				if($deal_info['is_shop']==0)
					$side_deal_list = get_deal_list(5,array(DEAL_ONLINE,DEAL_NOTICE),array("cid"=>$deal_info['cate_id'],"city_id"=>$GLOBALS['city']['id']),"","  d.buy_type <> 1 and d.is_shop = 0 and d.id<>".$deal_info['id']);
				elseif($deal_info['is_shop']==1)
				{
					if($deal_info['buy_type']==1)
						$side_deal_list = get_goods_list(5,array(DEAL_ONLINE,DEAL_NOTICE),array("cid"=>$deal_info['shop_cate_id'],"city_id"=>$GLOBALS['city']['id']),"","  d.buy_type = 1 and d.is_shop = 1 and d.id<>".$deal_info['id']);
					else
						$side_deal_list = get_goods_list(5,array(DEAL_ONLINE,DEAL_NOTICE),array("cid"=>$deal_info['shop_cate_id'],"city_id"=>$GLOBALS['city']['id']),"","  d.buy_type <> 1 and d.is_shop = 1 and d.id<>".$deal_info['id']);
				}
				
				
				//$side_deal_list = get_deal_list(4,array(DEAL_ONLINE));
				$GLOBALS['tmpl']->assign("side_deal_list",$side_deal_list['list']);
			}
			else
			{
				showErr("你要点评的商品不存在");
			}			
		}
		elseif($youhui_id>0)
		{
			require_once APP_ROOT_PATH."system/model/youhui.php";
			$youhui_info = get_youhui($youhui_id);
			if($youhui_info)
			{
				//验证是否可以点评
				$checker = check_dp_status($GLOBALS['user_info']['id'],array("youhui_id"=>$youhui_id,"youhui_log_id"=>$youhui_log_id));
				if(!$checker['status'])
				{
					showErr($checker['info'],0,$youhui_info['url']);
				}
				
				$dp_data = load_dp_info(array("youhui_id"=>$youhui_id));
				$dp_cfg = load_dp_cfg(array("cate_id"=>$youhui_info['deal_cate_id']));
				
				$item_info['id'] = $youhui_info['id'];
				$item_info['key'] = 'youhui_id';
				$item_info['ex_key'] = 'youhui_log_id';
				$item_info['ex_id'] = $youhui_log_id;
				$item_info['name'] = $youhui_info['name'];
				$item_info['url'] = $youhui_info['url'];
				$item_info['image'] = $youhui_info['icon'];
				
				$GLOBALS['tmpl']->assign("dp_data",$dp_data);
				$GLOBALS['tmpl']->assign("dp_cfg",$dp_cfg);
				$GLOBALS['tmpl']->assign("item_info",$item_info);
				
				
				//输出导航
				$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>url("index"));		
				$site_nav[] = array('name'=>$youhui_info['name'],'url'=>url("index","review",array("youhui_id"=>$youhui_info['id'])));
				$GLOBALS['tmpl']->assign("site_nav",$site_nav);
				
				//输出seo
				$page_title = "";
				$page_keyword = "";
				$page_description = "";
				if($youhui_info['supplier_info']['name'])
				{
					$page_title.="[".$youhui_info['supplier_info']['name']."]";
					$page_keyword.=$youhui_info['supplier_info']['name'].",";
					$page_description.=$youhui_info['supplier_info']['name'].",";
				}
				$page_title.= $youhui_info['name'];
				$page_keyword.=$youhui_info['name'];
				$page_description.=$youhui_info['name'];
				$GLOBALS['tmpl']->assign("page_title",$page_title);
				$GLOBALS['tmpl']->assign("page_keyword",$page_keyword);
				$GLOBALS['tmpl']->assign("page_description",$page_description);
			}
			else
			{
				showErr("你要点评的优惠券不存在");
			}			
		}
		elseif($location_id>0)
		{
			require_once APP_ROOT_PATH."system/model/supplier.php";
			$location_info = get_location($location_id);
			if($location_info)
			{
				//验证是否可以点评
				$checker = check_dp_status($GLOBALS['user_info']['id'],array("location_id"=>$location_id));
				if(!$checker['status'])
				{
					showErr($checker['info'],0,$location_info['url']);
				}
		
				$dp_data = load_dp_info(array("location_id"=>$location_id));
				$dp_cfg = load_dp_cfg(array("cate_id"=>$location_info['deal_cate_id']));
		
				$item_info['id'] = $location_info['id'];
				$item_info['key'] = 'location_id';
				$item_info['name'] = $location_info['name'];
				$item_info['url'] = $location_info['url'];
				$item_info['image'] = $location_info['preview'];
		
				$GLOBALS['tmpl']->assign("dp_data",$dp_data);
				$GLOBALS['tmpl']->assign("dp_cfg",$dp_cfg);
				$GLOBALS['tmpl']->assign("item_info",$item_info);
		
		
				//输出导航
				$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>url("index"));
				$site_nav[] = array('name'=>$location_info['name'],'url'=>url("index","review",array("location_id"=>$location_info['id'])));
				$GLOBALS['tmpl']->assign("site_nav",$site_nav);
		
				//输出seo
				$page_title = "";
				$page_keyword = "";
				$page_description = "";
				if($location_info['supplier_info']['name'])
				{
					$page_title.="[".$location_info['supplier_info']['name']."]";
					$page_keyword.=$location_info['supplier_info']['name'].",";
					$page_description.=$location_info['supplier_info']['name'].",";
				}
				$page_title.= $location_info['name'];
				$page_keyword.=$location_info['name'];
				$page_description.=$location_info['name'];
				$GLOBALS['tmpl']->assign("page_title",$page_title);
				$GLOBALS['tmpl']->assign("page_keyword",$page_keyword);
				$GLOBALS['tmpl']->assign("page_description",$page_description);
			}
			else
			{
				showErr("你要点评的商家不存在");
			}
		}
		elseif($event_id>0)
		{
			require_once APP_ROOT_PATH."system/model/event.php";
			$event_info = get_event($event_id);
			if($event_info)
			{
				//验证是否可以点评
				$checker = check_dp_status($GLOBALS['user_info']['id'],array("event_id"=>$event_id,"event_submit_id"=>$event_submit_id));
				if(!$checker['status'])
				{
					showErr($checker['info'],0,$event_info['url']);
				}
		
				$dp_data = load_dp_info(array("event_id"=>$event_id));
				$dp_cfg = load_dp_cfg(array("ecate_id"=>$event_info['cate_id']));
		
				$item_info['id'] = $event_info['id'];
				$item_info['key'] = 'event_id';
				$item_info['ex_key'] = 'event_submit_id';
				$item_info['ex_id'] = $event_submit_id;
				$item_info['name'] = $event_info['name'];
				$item_info['url'] = $event_info['url'];
				$item_info['image'] = $event_info['icon'];
		
				$GLOBALS['tmpl']->assign("dp_data",$dp_data);
				$GLOBALS['tmpl']->assign("dp_cfg",$dp_cfg);
				$GLOBALS['tmpl']->assign("item_info",$item_info);
		
		
				//输出导航
				$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>url("index"));
				$site_nav[] = array('name'=>$event_info['name'],'url'=>url("index","review",array("event_id"=>$event_info['id'])));
				$GLOBALS['tmpl']->assign("site_nav",$site_nav);
		
				//输出seo
				$page_title = "";
				$page_keyword = "";
				$page_description = "";
				if($event_info['supplier_info']['name'])
				{
					$page_title.="[".$event_info['supplier_info']['name']."]";
					$page_keyword.=$event_info['supplier_info']['name'].",";
					$page_description.=$event_info['supplier_info']['name'].",";
				}
				$page_title.= $event_info['name'];
				$page_keyword.=$event_info['name'];
				$page_description.=$event_info['name'];
				$GLOBALS['tmpl']->assign("page_title",$page_title);
				$GLOBALS['tmpl']->assign("page_keyword",$page_keyword);
				$GLOBALS['tmpl']->assign("page_description",$page_description);
			}
			else
			{
				showErr("你要点评的活动不存在");
			}
		}
		else
		{
			app_redirect(url("index"));
		}

		$GLOBALS['tmpl']->display("review.html");
	}
	
	
	
	public function save()
	{		
		global_run();
		if(empty($GLOBALS['user_info']))
		{
			$data['status']=-1;
			$data['info'] = "";
			ajax_return($data);
		}
		require_once APP_ROOT_PATH."system/model/review.php";
		
		$deal_id = intval($_REQUEST['deal_id']);
		$youhui_id = intval($_REQUEST['youhui_id']);
		$event_id = intval($_REQUEST['event_id']);
		$location_id = intval($_REQUEST['location_id']);
		$order_item_id = intval($_REQUEST['order_item_id']);
		$youhui_log_id = intval($_REQUEST['youhui_log_id']);
		$event_submit_id = intval($_REQUEST['event_submit_id']);
		$param = array(
			"deal_id"	=> $deal_id,
			"youhui_id"	=> $youhui_id,
			"event_id"	=>	$event_id,
			"location_id"	=> $location_id,
			"order_item_id"	=> $order_item_id,
			"youhui_log_id"	=>	$youhui_log_id,
			"event_submit_id"	=> $event_submit_id		
		);
		
		$checker = check_dp_status($GLOBALS['user_info']['id'],$param);
		if(!$checker['status'])
		{
			showErr($checker['info'],1);
		}
		
		$content = strim(valid_str($_REQUEST['content']));  //点评内容

		$dp_point = intval($_REQUEST['dp_point']); //总评分
		if($dp_point<=0)
		{
			$data['status']=0;
			$data['info'] = "请为总评打分";
			ajax_return($data);
		}
		
		$dp_image = array(); //点评图片
		foreach($_REQUEST['dp_image'] as $k=>$v)
		{
			if(strim($v)!="")
				$dp_image[] = strim($v);
		}
		
		$tag_group = array(); //标签分组
		foreach($_REQUEST['dp_tags'] as $k=>$tags_arr)
		{
			foreach($tags_arr as $v)
			{
				if(strim($v)!="")
				{
					$v_array = preg_split("/[ ,]/", $v);
					foreach($v_array as $kk=>$vv)
					{
						if(strim($vv)!="")
						$tag_group[$k][] = strim(valid_str($vv));
					}				
				}	
			}			
		}

		
		$point_group = array(); //评分分组
		foreach($_REQUEST['dp_point_group'] as $k=>$v)
		{
			if(intval($v)>0)
			{
				$point_group[$k] = intval($v);
 			}
 			else
 			{
 				$name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."point_group where id = ".intval($k));
 				$info = "请打分";
 				if($name)
 					$info = "请为".$name."打分";
 				$data['status']=0;
 				$data['info'] = $info;
 				ajax_return($data);
 			}
		}
		
		$result = save_review($GLOBALS['user_info']['id'], $param, $content, $dp_point, $dp_image, $tag_group,$point_group);
		if($result['status'])
		{
			//分享
			$attach_list = array();
			if($result['deal_id']>0)
			{
				require_once APP_ROOT_PATH."system/model/deal.php";
				$deal_info = get_deal($result['deal_id']);
				if($deal_info['uname']!="")
					$url_key = $deal_info['uname'];
				else
					$url_key = $deal_info['id'];
				$type="dealcomment";
				$url_route = array(
						'rel_app_index'	=>	'index',
						'rel_route'	=>	'deal#'.$url_key,
						'rel_param' =>''
				);
				
				//同步图片	
				if($deal_info['icon'])
				{
					require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
					$imagecls = new es_imagecls();
					$info = $imagecls->getImageInfo(APP_ROOT_PATH.$deal_info['icon']);
					
					$image_data['width'] = intval($info[0]);
					$image_data['height'] = intval($info[1]);
					$image_data['name'] = $deal_info['sub_name'];
					$image_data['filesize'] = filesize(APP_ROOT_PATH.$deal_info['icon']);
					$image_data['create_time'] = NOW_TIME;
					$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
					$image_data['user_name'] = strim($GLOBALS['user_info']['user_name']);
					$image_data['path'] = $deal_info['icon'];
					$image_data['o_path'] = $deal_info['icon'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);
					
					$img_id = intval($GLOBALS['db']->insert_id());
					$attach_list[] = array("type"=>"image","id"=>intval($img_id));
				}
				
				
				
			}
			elseif($result['youhui_id']>0)
			{
				require_once APP_ROOT_PATH."system/model/youhui.php";
				$youhui_info = get_youhui($result['youhui_id']);
				
				$type="youhuicomment";
				$url_route = array(
						'rel_app_index'	=>	'index',
						'rel_route'	=>	'youhui#'.$result['youhui_id'],
						'rel_param' => ''
				);
				
				//同步图片
				if($youhui_info['icon'])
				{
					require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
					$imagecls = new es_imagecls();
					$info = $imagecls->getImageInfo(APP_ROOT_PATH.$youhui_info['icon']);
					
					$image_data['width'] = intval($info[0]);
					$image_data['height'] = intval($info[1]);
					$image_data['name'] = $youhui_info['name'];
					$image_data['filesize'] = filesize(APP_ROOT_PATH.$youhui_info['icon']);
					$image_data['create_time'] = NOW_TIME;
					$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
					$image_data['user_name'] = strim($GLOBALS['user_info']['user_name']);
					$image_data['path'] = $youhui_info['icon'];
					$image_data['o_path'] = $youhui_info['icon'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);
					
					$img_id = intval($GLOBALS['db']->insert_id());
					$attach_list[] = array("type"=>"image","id"=>intval($img_id));
				}
				
			}
			elseif($result['event_id']>0)
			{
				require_once APP_ROOT_PATH."system/model/event.php";
				$event_info = get_event($result['youhui_id']);
				
				$type="eventcomment";
				$url_route = array(
						'rel_app_index'	=>	'index',
						'rel_route'	=>	'event#'.$result['event_id'],
						'rel_param' => ''
				);
				
				//同步图片
				if($event_info['icon'])
				{
					require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
					$imagecls = new es_imagecls();
					$info = $imagecls->getImageInfo(APP_ROOT_PATH.$event_info['icon']);
					
					$image_data['width'] = intval($info[0]);
					$image_data['height'] = intval($info[1]);
					$image_data['name'] = $event_info['name'];
					$image_data['filesize'] = filesize(APP_ROOT_PATH.$event_info['icon']);
					$image_data['create_time'] = NOW_TIME;
					$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
					$image_data['user_name'] = strim($GLOBALS['user_info']['user_name']);
					$image_data['path'] = $event_info['icon'];
					$image_data['o_path'] = $event_info['icon'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);
					
					$img_id = intval($GLOBALS['db']->insert_id());
					$attach_list[] = array("type"=>"image","id"=>intval($img_id));
				}
			}
			else
			{
				require_once APP_ROOT_PATH."system/model/supplier.php";
				$location_info = get_location($result['location_id']);
				
				$type = "slocationcomment";
				$url_route = array(
						'rel_app_index'	=>	'index',
						'rel_route'	=>	'store#'.$result['location_id'],
						'rel_param' => ''
				);
				
				//同步图片
				if($location_info['preview'])
				{
					require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
					$imagecls = new es_imagecls();
					$info = $imagecls->getImageInfo(APP_ROOT_PATH.$location_info['preview']);
						
					$image_data['width'] = intval($info[0]);
					$image_data['height'] = intval($info[1]);
					$image_data['name'] = $location_info['name'];
					$image_data['filesize'] = filesize(APP_ROOT_PATH.$location_info['preview']);
					$image_data['create_time'] = NOW_TIME;
					$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
					$image_data['user_name'] = strim($GLOBALS['user_info']['user_name']);
					$image_data['path'] = $location_info['preview'];
					$image_data['o_path'] = $location_info['preview'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);
						
					$img_id = intval($GLOBALS['db']->insert_id());
					$attach_list[] = array("type"=>"image","id"=>intval($img_id));
				}
			}
			
			foreach($_REQUEST['topic_image_id'] as $att_id)
			{
				if(intval($att_id)>0)
					$attach_list[] = array("type"=>"image","id"=>intval($att_id));
			}
				
			
			require_once APP_ROOT_PATH."system/model/topic.php";			
			
			$tid = insert_topic($content,"",$type,$group="", $relay_id = 0, $fav_id = 0,$group_data = "",$attach_list,$url_route);
			if($tid)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
			$result['jump'] = url($url_route['rel_app_index'],$url_route['rel_route'],$url_route['rel_param']);
			ajax_return($result);
		}
		else
		{
			ajax_return($result);
		}
		
	}
	
}
?>
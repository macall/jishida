<?php
class add_dp
{
	public function index()
	{
		
		$content = strim($GLOBALS['request']['content']);//点评内容
		$point = intval($GLOBALS['request']['point']);//点评分数
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$type = strim($GLOBALS['request']['type']);
		$id = intval($GLOBALS['request']['id']);
		$deal_id = 0;
		$youhui_id = 0;
		$location_id = 0;
		$event_id = 0;
		if($type=="deal")
		{
			$deal_id = $id;
			require_once APP_ROOT_PATH."system/model/deal.php";
			$relate_data = get_deal($deal_id);
		}
		elseif($type=="supplier")
		{
			$location_id = $id;
			require_once APP_ROOT_PATH."system/model/supplier.php";
			$relate_data = get_location($location_id);
		}
		elseif($type=="youhui")
		{
			$youhui_id = $id;
			require_once APP_ROOT_PATH."system/model/youhui.php";
			$relate_data = get_youhui($youhui_id);
		}
		elseif($type=="event")
		{
			$event_id = $id;
			require_once APP_ROOT_PATH."system/model/event.php";
			$relate_data = get_event($event_id);
		}
		
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);		
			
		$root = array();
		$root['return'] = 1;
		if($user_id>0)
		{
			$GLOBALS['user_info'] = $user;
			$root['user_login_status']	=	1;
			
			require_once APP_ROOT_PATH."system/model/review.php";
			require_once APP_ROOT_PATH."system/model/deal.php";
			
			if($type=="deal")
			{
				if($relate_data['is_shop']==1)
					$cfg = load_dp_cfg(array("scate_id"=>$relate_data['shop_cate_id']));
				else 
					$cfg = load_dp_cfg(array("cate_id"=>$relate_data['cate_id']));
			}
			elseif($type=="event")
			{
				$cfg = load_dp_cfg(array("ecate_id"=>$relate_data['cate_id']));
				
			}
			elseif($type=="supplier") 
			{
				$cfg = load_dp_cfg(array("cate_id"=>$relate_data['deal_cate_id']));
			}
			elseif($type=="youhui")
			{
				$cfg = load_dp_cfg(array("cate_id"=>$relate_data['deal_cate_id']));
			}
			
			$point_group = array();
			foreach($cfg['point_group'] as $row)
			{
				$point_group[$row['id']] = $point;
			}			
			
			$dp_img = array();
			if(count($_FILES['file']['name'])>9)
			{
				$root['status'] =0;
				$root['info'] = '上传图片不能超过9张';
			}
			else
			{
				//同步图片
				foreach($_FILES['file']['name'] as $k=>$v)
				{
					$_files['file']['name'] = $v;
					$_files['file']['type'] = $_FILES['file']['type'][$k];
					$_files['file']['tmp_name'] = $_FILES['file']['tmp_name'][$k];
					$_files['file']['error'] = $_FILES['file']['error'][$k];
					$_files['file']['size'] = $_FILES['file']['size'][$k];
					$res = upload_topic($_files);
				
					if($res['error']==0)
					{
						$dp_img[] = $res['url'];
					}
				}
					
	
				$result = save_review($user_id,array("deal_id"=>$deal_id,"youhui_id"=>$youhui_id,"event_id"=>$event_id,"location_id"=>$location_id), $content, $point, $dp_img,array(),$point_group);

				//$result = add_deal_dp($user_id, $content, $point, $deal_id);
				$root['status'] = $result['status'];
				$root['info'] = $result['info'];
			}
			
		}else
		{
			$root['user_login_status'] = 0;	
			$root['status'] =0;
			$root['info'] = '请先登录';
		}
		$root['page_title']="发表点评";
		$root['city_name']=$city_name;
		output($root);		
	}
}
?>
<?php
class add_deal_dp
{
	public function index()
	{
		
		$content = strim($GLOBALS['request']['content']);//点评内容
		$point = intval($GLOBALS['request']['point']);//点评分数
		$deal_id = intval($GLOBALS['request']['id']);//团购或商品id //只有购买后，才能点评
		
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);		
		$money = floatval($user['money']);
			
		$root = array();
		$root['return'] = 1;
		if($user_id>0)
		{
			$GLOBALS['user_info'] = $user;
			$root['user_login_status']	=	1;
			
			require_once APP_ROOT_PATH."system/model/review.php";
			require_once APP_ROOT_PATH."system/model/deal.php";
			$deal_info = get_deal($deal_id);
			if($deal_info['is_shop']==1)
				$cfg = load_dp_cfg(array("scate_id"=>$deal_info['shop_cate_id']));
			else 
				$cfg = load_dp_cfg(array("cate_id"=>$deal_info['cate_id']));
			
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
					
				$result = save_review($user_id,array("deal_id"=>$deal_id), $content, $point, $dp_img,array(),$point_group);
					
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
		output($root);		
	}
}
?>
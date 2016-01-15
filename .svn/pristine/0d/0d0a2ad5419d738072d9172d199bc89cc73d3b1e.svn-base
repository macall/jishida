<?php
class add_collect{
	public function index()
	{
		if($GLOBALS['request']['from']=="wap"){
			$goods_id=intval($GLOBALS['request']['id']);
		}else{
			$goods_id = intval($_REQUEST['id']);
		}
		$root['id']=$goods_id;
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);
		$root['return']=1;
		$root['status']=0;
		$sql="select * from ".DB_PREFIX."deal where id = ".$goods_id." and is_effect = 1 and is_delete = 0";
		$goods_info = $GLOBALS['db']->getRow($sql);
		if($user_id>0){
				if($goods_info){
					$sql = "select * from  ".DB_PREFIX."deal_collect where user_id = $user_id and deal_id = $goods_id";
					if (intval($GLOBALS['db']->getOne($sql) > 0)) {
						//已经设置打折提醒，则取消
						$sql = "delete from ".DB_PREFIX."deal_collect where user_id = $user_id and deal_id = $goods_id";
						$GLOBALS['db']->query($sql);
						$root['info'] ="取消收藏";
					}else{
						//没设置，则设置
						$deal_collect = array(
								'user_id' => $user_id,
								'deal_id' => $goods_id,
								'create_time' =>get_gmtime()
						);
						
						$GLOBALS['db']->autoExecute(DB_PREFIX."deal_collect", $deal_collect, 'INSERT');
						$root['info'] ="收藏成功";
						$root['status']=1;
						$root['return'] =1;
						
					}
				}else{
					$root['info'] ="没有该商品";
					$root['return'] =1	;
				}

		}
		else
		{
			$root['info'] ="你没有登录,请先登录";
			$root['return'] =0;
		}
		$root['city_name']=$city_name;
		output($root);	
	}
}
?>

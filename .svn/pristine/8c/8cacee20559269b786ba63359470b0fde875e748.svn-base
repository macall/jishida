<?php
class collect_deal
{
	public function index()
	{	
		$deal_id = intval($GLOBALS['request']['deal_id']);/*商品id*/
		$collect_status=intval($GLOBALS['request']['collect_status']);/*2:加载,1:增加收藏,0取消收藏;*/
	
		//检查用户,用户密码
		$user = $GLOBALS['user_info'];
		$user_id  = intval($user['id']);

		$root = array();
		$root['return'] = 1;
		if($user_id>0)
		{
			//用户登陆状态：1:成功登陆;0：未成功登陆
			$root['user_login_status']	=	1;
			$root['collect_status'] = $collect_status;
			if($collect_status==2)
			{
				$root['is_collect']=0;/*1:已收藏,0:未收藏*/
				$collect_deal_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_collect where deal_id = ".$deal_id." and user_id=".$user_id."");
				if(intval($collect_deal_id) >0)
					$root['is_collect']=1;
			}
			elseif($collect_status==1)
			{
				$goods_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id." and is_effect = 1 and is_delete = 0");
				$collect_deal_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_collect where deal_id = ".$deal_id." and user_id=".$user_id."");
				if($goods_info)
				{
					$sql = "INSERT INTO `".DB_PREFIX."deal_collect` (`id`,`deal_id`, `user_id`, `create_time`) select '0','".$goods_info['id']."','".$user_id."','".get_gmtime()."' from dual where not exists (select * from `".DB_PREFIX."deal_collect` where `deal_id`= '".$goods_info['id']."' and `user_id` = ".$user_id.")";
					$GLOBALS['db']->query($sql);
					
					if($GLOBALS['db']->affected_rows()>0)
					{
						$root['info'] = "收藏成功";
						$root['is_collect']=1;
					}
					else
					{
						$root['info'] = "您已经收藏过该商品了";
						$root['is_collect']=1;
					}
				}else{
					$root['info'] = "没有该商品";
					$root['is_collect']=0;
				}
			}
			else
			{
				$goods_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id." and is_effect = 1 and is_delete = 0");
				if($goods_info){
					$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_collect where deal_id = ".$goods_info['id']." and user_id=".$user_id."");
					$root['is_collect']=0;
					$root['info'] = "取消成功";
				}else{
					$root['info'] = "没有该商品";
					$root['is_collect']=1;
				}
			}
			
		}else{
			//未登录
			$root['user_login_status']	=	0;
		}
		output($root);
	}
}
?>
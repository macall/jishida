<?php
class indexs_more
{
	public function index()
	{
		
		
		$root = array();
		$root['return'] = 1;
		
	
		//首页菜单列表
		$indexs_list = $GLOBALS['cache']->get("WAP_INDEXS_MORE_".intval($GLOBALS['city_id']));
		if($indexs_list===false)
		{
			$indexs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_index where status = 1 and mobile_type = 1 and city_id in (0,".intval($GLOBALS['city_id']).") order by sort desc ");
			$indexs_list = array();
			foreach($indexs as $k=>$v)
			{
				$indexs_list[$k]['id'] = $v['id'];
				$indexs_list[$k]['name'] = $v['name'];
				$indexs_list[$k]['icon_name'] = $v['vice_name'];//图标名 http://fontawesome.io/icon/bars/
				$indexs_list[$k]['color'] = $v['desc'];//颜色
				$indexs_list[$k]['img'] = get_abs_img_root($v['img']);
				/*
				$indexs_list[$k]['is_hot'] = $v['is_hot'];
				$indexs_list[$k]['is_new'] = $v['is_new'];				
					
				$indexs_list[$k]['type'] = $v['type'];
				$indexs_list[$k]['data'] = $v['data'] = unserialize($v['data']);
				*/
				
				$indexs_list[$k]['url'] = getWebAdsUrl($v['type'],unserialize($v['data']));
			
			}
			$GLOBALS['cache']->set("WAP_INDEXS_MORE_".intval($GLOBALS['city_id']),$indexs_list,300);
		}
		
		$root['indexs'] = $indexs_list;		

		$root['page_title'] = "更多分类";
		
		output($root);
	}
}
?>
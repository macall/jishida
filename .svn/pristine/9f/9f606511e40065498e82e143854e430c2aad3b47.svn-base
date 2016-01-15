<?php
//参数:city_id, cate_id, page
class eventlist
{
	public function index()
	{		
		
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
		$page = 1;
	
		$cate_id = intval($GLOBALS['request']['cate_id']);
		$city_id = intval($GLOBALS['request']['city_id']);
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		
		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		
		if($GLOBALS['request']['from']=="wap"){
			/*输出分类*/
			$bigcate_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."event_cate where is_effect=1 order by sort");
			
			/*输出商圈*/
			$all_quan_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where city_id=".$city_id."");
			$quan_list=array();
			$quan_sub_list=array();
			
			$quan_list[0]['id']=0;
			$quan_list[0]['name']='全城';
			$quan_list[0]['quan_sub'][0]['id']=0;
			$quan_list[0]['quan_sub'][0]['pid']=0;
			$quan_list[0]['quan_sub'][0]['name']='全城';
			foreach($all_quan_list as $k=>$v)
			{
				if($v['pid']==0)
				{
					$quan_list[]=$v;
				}
				if($v['pid']>0)
					$quan_sub_list[$v['pid']][]=$v;
			}
			
			foreach ($quan_list as $k=>$v)
			{
				if($v['name'] !="全城")
				{
					if($quan_sub_list[$v['id']] ==null || $quan_sub_list[$v['id']] =='')
						$quan_list[$k]['quan_sub']=array();
					else
						$quan_list[$k]['quan_sub']=$quan_sub_list[$v['id']];
				}
			}
			$root[quan_list]=$quan_list;

		}
		
		$keyword = strim($GLOBALS['request']['keyword']);
		if($xpoint>0)
		{		
			$pi = 3.14159265;  //圆周率
			$r = 6378137;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (xpoint * $pi) / 180 ) ) * $r) as distance ";
			if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
			$where = " ypoint > $ybottom and ypoint < $ytop and xpoint > $xleft and xpoint < $xright ";
			$order = " distance asc,is_recommend desc,sort desc,id desc";
		}
		else
		{
			$field_append = $where = $order = "";
		}		
		
		$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;	
                if($keyword)
			{				
					$kws_div = div_str($keyword);
					foreach($kws_div as $k=>$item)
					{
						$kw[$k] = str_to_unicode_string($item);
					}
					$ukeyword = implode(" ",$kw);
					$where.=" (match(name_match) against('".$ukeyword."'  IN BOOLEAN MODE)  or name like '%".$keyword."%') ";
			}
			
		$res = m_search_event_list($limit,$cate_id,$city_id,$where,$order,$field_append);				
		$pattern = "/<img([^>]*)\/>/i";
		$replacement = "<img width=300 $1 />";
		foreach($res['list'] as $k=>$v)
		{
			if($v['ypoint']=='')
			{
				$res['list'][$k]['ypoint']=0;
			}
			if($v['xpoint']=='')
			{
				$res['list'][$k]['xpoint']=0;
			}
			
			$res['list'][$k]['icon'] = get_abs_img_root(get_spec_image($v['icon'],140,85,0));
			$res['list'][$k]['distance'] = round($v['distance']);
			$res['list'][$k]['date_time'] = pass_date($v['submit_begin_time']);
			$res['list'][$k]['event_begin_time'] = to_date($v['event_begin_time'],'Y-m-d');
			$res['list'][$k]['event_end_time'] = to_date($v['event_end_time'],'Y-m-d');

			$res['list'][$k]['submit_end_time'] = to_date($v['submit_end_time'],'Y-m-d');
			$res['list'][$k]['submit_begin_time'] = to_date($v['submit_begin_time'],'Y-m-d');
			$res['list'][$k]['content'] = preg_replace($pattern, $replacement, get_abs_img_root($v['content']));			
		}
		
		$root = array();
		$root['bigcate_list']=$bigcate_list;
		$root['return'] = 1;
				
		$root['item'] = $res['list'];
		$root['page'] = array("page"=>$page,"page_total"=>ceil($res['count']/PAGE_SIZE),"page_size"=>PAGE_SIZE);
		$root['page_title'] = "活动列表";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
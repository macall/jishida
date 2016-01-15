<?php
class nearbygoodses{
	public function index(){
		
		$catalog_id = intval($GLOBALS['request']['catalog_id']);//商品分类ID
		$city_id = intval($GLOBALS['request']['city_id']);//城市分类ID			
		$page = intval($GLOBALS['request']['page']); //分页
		$keyword = strim($GLOBALS['request']['keyword']);
		$page=$page==0?1:$page;
		$quan_id = intval($GLOBALS['request']['quan_id']); //商圈id		
		
		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		if($keyword)
		{
			$kws_div = div_str($keyword);
			foreach($kws_div as $k=>$item)
			{
				$kws[$k] = str_to_unicode_string($item);
			}
			$ukeyword = implode(" ",$kws);
			$condition ="  (match(tag_match,name_match,locate_match,deal_cate_match) against('".$ukeyword."' IN BOOLEAN MODE) or name like '%".$keyword."%') and ";
		}
		$condition.=" buy_type<>1 and is_lottery = 0 ";
		
		if($xpoint>0)
		{		
			$pi = 3.14159265;  //圆周率
			$r = 6378137;  //地球平均半径(米)
			$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (xpoint * $pi) / 180 ) ) * $r) as distance ";
			
			if($ybottom!=0&&$ytop!=0&&$xleft!=0&&$xright!=0)
			{
			if($condition!="")
			$condition.=" and ";
			$condition.= " ypoint > $ybottom and ypoint < $ytop and xpoint > $xleft and xpoint < $xright ";
			}
			$order = " distance asc,id desc ";
		}
		else
		{
			$field_append = "";
			$order = "sort desc,id desc ";
		}	
		
		//根据传入的商圈ID来搜索该商圈下的商品
		if ($quan_id > 0){
			$sql_q = "select name from ".DB_PREFIX."area where id = ".intval($quan_id);
			$q_name = $GLOBALS['db']->getOne($sql_q);
			$q_name_unicode = str_to_unicode_string($q_name);
			$condition .=" and (match(locate_match) against('".$q_name_unicode."' IN BOOLEAN MODE))";
		}
		
		$deals = m_get_deal_list($limit,$catalog_id,$city_id,array(DEAL_ONLINE),$condition,$order,0,$field_append);
		$list = $deals['list'];
		$count= $deals['count'];
		
		$page_total = ceil($count/$page_size);
		
		$root = array();
		$root['return'] = 1;

		
		$goodses = array();
		foreach($list as $item)
		{
			//$goods = array();
			$goods = getGoodsArray($item);
			$goods['distance'] = round($goods['distance']);
			$goodses[] = $goods;
		}
		$root['item'] = $goodses;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);

		
		output($root);
		
	}
}
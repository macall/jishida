<?php
class mapsearch_tuan{
	public function index(){
		$ytop = $latitude_top = floatval($GLOBALS['request']['latitude_top']);//最上边纬线值 ypoint
		$ybottom = $latitude_bottom = floatval($GLOBALS['request']['latitude_bottom']);//最下边纬线值 ypoint
		$xleft = $longitude_left = floatval($GLOBALS['request']['longitude_left']);//最左边经度值  xpoint
		$xright = $longitude_right = floatval($GLOBALS['request']['longitude_right']);//最右边经度值 xpoint
		$ypoint =  $m_latitude = doubleval($GLOBALS['request']['m_latitude']);  //ypoint 
		$xpoint = $m_longitude = doubleval($GLOBALS['request']['m_longitude']);  //xpoint
		$type = intval($GLOBALS['request']['type']); //-1:全部，0：优惠券；1：活动；2：团购；3：代金券；4：商家		
		$bcate_id=strim($GLOBALS['request']['bcate_id']);  /*大分类*/
		$cate_type_id=strim($GLOBALS['request']['cate_type_id']);  /*小分类*/
		//print_r($GLOBALS['request']);
			
		$pi = 3.14159265;  //圆周率
		$r = 6378137;  //地球平均半径(米)
		$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (xpoint * $pi) / 180 ) ) * $r) as distance ";
		$condition = "  ypoint > $ybottom and ypoint < $ytop and xpoint > $xleft and xpoint < $xright ";
		$limit = 10;
		
		/*查团购*/
		$res = m_get_deal_list($limit,$bcate_id,0,array(DEAL_ONLINE),$condition,"distance asc",0,$field_append,$cate_type_id);
		$tuan_list = array();
		foreach($res['list'] as $item){	
			$item['icon'] = get_abs_img_root($item['icon']);	
			$item['type'] = 2;		
			$item['distance'] = round($item['distance']);
			$tuan_list[] = $item;
		}
		
		if($tuan_list)
		$root['item'] = $tuan_list;
		else
		$root['item'] = array();

		
		output($root);
		
	}
}
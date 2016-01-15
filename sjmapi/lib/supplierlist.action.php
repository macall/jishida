<?php
class supplierlist{
	public function index(){
		require_once APP_ROOT_PATH.'app/Lib/supplier.php'; 
		
		$catalog_id = intval($GLOBALS['request']['catalog_id']);//商品分类ID
		$city_id = intval($GLOBALS['request']['city_id']);//城市分类ID	
		$quan_id = intval($GLOBALS['request']['quan_id']); //商圈id		
		$page = intval($GLOBALS['request']['page']); //分页
		$keyword = strim($GLOBALS['request']['keyword']);
		
		
		
		
		$page=$page==0?1:$page;
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
	
		$condition = " deal_type = 0 ";
	
		
		$deals = get_supplier_list($limit,$catalog_id,$city_id,$condition);
		$list = $deals['list'];
		//var_dump($list);die;
		$count= $deals['count'];
		
		$page_total = ceil($count/$page_size);
		
		$root = array();
		$root['return'] = 1;

		
		$goodses = array();
		foreach($list as $item)
		{
			//$goods = array();
			$goods = getGoodsArray($item);
			$goodses[] = $goods;
		}
		$root['item'] = $goodses;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);

		
		output($root);
		
	}
}
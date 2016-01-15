<?php
class sea_redcatetype
{
	public function index()
	{	
		$root = array();
		$root['return'] = 1;
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$cate_type_list=$GLOBALS['db']->getAll("select dct.id,dct.name,dctl.cate_id as pid from ".DB_PREFIX."deal_cate_type as dct left join ".DB_PREFIX."deal_cate_type_link as dctl on dctl.deal_cate_type_id=dct.id where dct.is_recommend=1 order by sort desc,id desc limit 9");

		$root['cate_type_list'] =$cate_type_list;
		$root['page_title'] ="搜索";// fwb add 2014-08-27
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
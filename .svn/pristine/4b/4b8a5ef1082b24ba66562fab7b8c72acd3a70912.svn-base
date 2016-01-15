<?php
// fwb add 2014-08-27
class tuaninfo
{
	public function index()
	{	
		$root = array();
		$root['return'] = 1;
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		$goods_id = intval($GLOBALS['request']['goods_id']);
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$tuan_info=$GLOBALS['db']->getOne("select description from ".DB_PREFIX."deal where id=".$goods_id."");

		//$tuan_info=str_replace("./public/","../public/",$tuan_info);
		$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/i";
		//$replacement = "<img width=300 $1 />";
		$replacement = "<img src='$1' width='256' />";
		$tuan_info = get_abs_img_root(preg_replace($pattern, $replacement, $tuan_info));
		$root['tuan_info']=$tuan_info;
		$root['email']=$email;
		$root['city_name']=$city_name;
		$root['page_title']="本单详情";
		output($root);
	}
}
?>
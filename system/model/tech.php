<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

define("DEAL_OUT_OF_STOCK",4); //库存不足
define("DEAL_ERROR_MIN_USER_BUY",5); //用户最小购买数不足
define("DEAL_ERROR_MAX_USER_BUY",6); //用户最大购买数超出
define("EXIST_DEAL_COUPON_SN",1);  //团购券序列号已存在

define("DEAL_NOTICE",0); //未上线
define("DEAL_ONLINE",1); //进行中
define("DEAL_HISTORY",2); //过期

define("DEAL_NOT_SUCCESS",0); //未成团
define("DEAL_SUCCESS",1); //成团
define("DEAL_NOT_STOCK",2); //卖光




/**
 * 获取指定的团购产品
 * @param unknown_type $key 商品的关键ID或uname
 * @param unknown_type $preview 是否为管理员预览
 * @return number
 */
function get_tech($key,$preview=false)
{
	$tech_info = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user where id=".intval($key)." and is_effect = 1 AND is_delete = 0 AND service_type_id = 2 ");
	return $tech_info;

}



?>
<?php
//过滤SQL注入
//function strim($string)
//{
//	return trim(addslashes($string));
//}

//解析URL标签
// $str = u:shop|acate#index|id=10&name=abc
function parse_url_tag($str)
{
	$key = md5("URL_TAG_".$str);
	if(isset($GLOBALS[$key]))
	{
		return $GLOBALS[$key];
	}

	$url = load_dynamic_cache($key);
	$url=false;
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	$str = substr($str,2);
	$str_array = explode("|",$str);
	$app_index = $str_array[0];
	$route = $str_array[1];
	$param_tmp = explode("&",$str_array[2]);
	$param = array();

	foreach($param_tmp as $item)
	{
		if($item!='')
			$item_arr = explode("=",$item);
		if($item_arr[0]&&$item_arr[1])
			$param[$item_arr[0]] = $item_arr[1];
	}
	$GLOBALS[$key]= url($app_index,$route,$param);
	set_dynamic_cache($key,$GLOBALS[$key]);
		
	return $GLOBALS[$key];
}

function get_muser_avatar($id,$type)
{
	$uid = sprintf("%09d", $id);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$path = $dir1.'/'.$dir2.'/'.$dir3;
				
	$id = str_pad($id, 2, "0", STR_PAD_LEFT); 
	$id = substr($id,-2);
	$avatar_file = "../public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";
	$avatar_check_file = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";

	if(file_exists($avatar_check_file))	
	return $avatar_file;
	else
//	return "../public/avatar/noavatar_".$type.".gif";
        return "/public/avatar/noavatar_".$type.".gif";
}
//显示错误
function showErr($msg,$ajax=0,$jump='',$stay=0)
{
	
	echo "<script>alert('".$msg."');location.href='".$jump."';</script>";exit;
	/*
	if($ajax==1)
	{
		$result['status'] = 0;
		$result['info'] = $msg;
		$result['jump'] = $jump;
		header("Content-Type:text/html; charset=utf-8");
		echo(json_encode($result));exit;
	}
	else
	{

		$GLOBALS['tmpl']->assign('page_title',$GLOBALS['lang']['ERROR_TITLE']." - ".$msg);
		$GLOBALS['tmpl']->assign('msg',$msg);
		if($jump=='')
		{
			$jump = $_SERVER['HTTP_REFERER'];
		}
		if(!$jump&&$jump=='')
			$jump = APP_ROOT."/";
		$GLOBALS['tmpl']->assign('jump',$jump);
		$GLOBALS['tmpl']->assign("stay",$stay);
		$GLOBALS['tmpl']->display("error.html");
		exit;
	}
	*/
}

//显示成功
function showSuccess($msg,$ajax=0,$jump='',$stay=0)
{
	echo "<script>alert('".$msg."');location.href='".$jump."';</script>";exit;
	/*
	if($ajax==1)
	{
		$result['status'] = 1;
		$result['info'] = $msg;
		$result['jump'] = $jump;
		header("Content-Type:text/html; charset=utf-8");
		echo(json_encode($result));exit;
	}
	else
	{
		$GLOBALS['tmpl']->assign('page_title',$GLOBALS['lang']['SUCCESS_TITLE']." - ".$msg);
		$GLOBALS['tmpl']->assign('msg',$msg);
		if($jump=='')
		{
			$jump = $_SERVER['HTTP_REFERER'];
		}
		if(!$jump&&$jump=='')
			$jump = APP_ROOT."/";
		$GLOBALS['tmpl']->assign('jump',$jump);
		$GLOBALS['tmpl']->assign("stay",$stay);
		$GLOBALS['tmpl']->display("success.html");
		exit;
	}
	*/
}

function do_postcart($request_param){
	
	if ($request_param['post_type'] ==''){
		//print_r($request_param);
		//exit;
		if($request_param['id']){
			/*
			 $cart['goods_id'] = 57;
			$cart['num'] = 1;
			$cart['attr_value_a'] = '白色';
			$cart['attr_value_b'] = '170';
			$cart['attr_id_a'] = 257;
			$cart['attr_id_b'] = 259;
			 
			*/
			$attr=array();
			$attr=$request_param['attr'];
			$attr_value=array();
			$attr_value=$request_param['attr_value'];
			$goods_id = $request_param['id'];
			$attr_id_a = $attr[0];
			$attr_value_a = trim($attr_value[0]);
			$attr_id_b = $attr[1];
			$attr_value_b = trim($attr_value[1]);
			$id = md5($goods_id."_".$attr_id_a."_".$attr_value_a."_".$attr_id_b."_".$attr_value_b);
			//$id = $goods_id;
			//echo $id;
			$session_cart_data=es_session::get("cart_data");
			if (empty($session_cart_data)){
				$session_cart_data = array();
			}
			if (isset($session_cart_data[$id])){
				$session_cart_data[$id]['num'] ++;
			}else{
				$tmp = array();
				$tmp["id"]  = $id;
				$tmp["goods_id"]  = $goods_id;
				$tmp["attr_id_a"] = $attr_id_a;
				$tmp['attr_id_b'] = $attr_id_b;
				$tmp["attr_value_a"] = $attr_value_a;
				$tmp['attr_value_b'] = $attr_value_b;
				$tmp['num'] = 1;
				$session_cart_data[$id] = $tmp;
			}
			es_session::set("cart_data",$session_cart_data);
		}
		//es_session::set("first_calc",1);
		
		$session_cart_data=es_session::get("cart_data");
		//print_r($session_cart_data);exit;
		
		if (count($session_cart_data) == 0){
			//购物车中,没有商品了，跳转到首页
			//app_redirect(url("index"));
			showErr('购物车未有商品',0,url("index"));
		}else{
			$request_param['cartdata']= str_replace("+","%2B",base64_encode(serialize(es_session::get("cart_data"))));
			return $request_param;
		}		
	}else if ($request_param['post_type'] =='del'){
		if($request_param['id']){
			$id=$request_param['id'];
			if(es_session::get("cart_data")){
				$session_cart_data=es_session::get("cart_data");
				unset($session_cart_data[$id]);
				es_session::set("cart_data",$session_cart_data);
				
				if (count($session_cart_data) == 0){
					//购物车中,没有商品了，跳转到首页
					app_redirect(url("index"));
				}else{
					//购物车中还有商品，刷新购物车界面
					app_redirect(url("index","postcart"));
				}				
				
				//echo "<script>alert('删除成功!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
			}
		}
	}
}

/**
 * 定位城市
 * @param int $deal_city_id 城市id
 * @return unknown
 */
function get_cur_deal_city($deal_city_id = 0)
{
	if($deal_city_id > 0)
	{		
		$deal_city = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_city where is_effect = 1 and is_delete = 0 and id = ".intval($deal_city_id));
	}

	if(!$deal_city)
	{
		//设置如存在的IP订位
		if(file_exists(APP_ROOT_PATH."system/extend/ip.php"))
		{
			require_once APP_ROOT_PATH."system/extend/ip.php";
			$ip =  get_client_ip();
			$iplocation = new iplocate();
			$address=$iplocation->getaddress($ip);
				
			$sql = "select * from ".DB_PREFIX."deal_city where is_delete = 0 and is_effect = 1 ";
			$city_list = $GLOBALS['db']->getAll($sql);//不知谁把$city_list 查询去掉了; 去掉后就不能通过ip定位了; chenfq 现在又添加上去了 2014-09-18
				
			foreach ($city_list as $city)
			{
				if(strpos($address['area1'],$city['name']))
				{
					$deal_city = $city;
					break;
				}
			}
		}
		if(!$deal_city)
			$deal_city = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_city where is_default = 1 and is_effect = 1 and is_delete = 0");
	}
	return $deal_city;
}

//编译生成css文件
function parse_css($urls)
{
	static $color_cfg;
	if(empty($color_cfg))
		$color_cfg = require_once APP_ROOT_PATH."wap/tpl/".TMPL_NAME."/color_cfg.php";
		
	$url = md5(implode(',',$urls));
	$css_url = 'public/runtime/wap/statics/'.$url.'.css';
	$url_path = APP_ROOT_PATH.$css_url;
	if(!file_exists($url_path)||IS_DEBUG||true)
	{
		$tmpl_path = $GLOBALS['tmpl']->_var['TMPL'];

		$css_content = '';
		foreach($urls as $url)
		{
			$css_content .= @file_get_contents($url);
		}
		$css_content = preg_replace("/[\r\n]/",'',$css_content);
		$css_content = str_replace("../images/",$tmpl_path."/images/",$css_content);
		if (is_array($color_cfg)){
			foreach($color_cfg as $k=>$v)
			{
				$css_content = str_replace($k,$v,$css_content);
			}
		}
		//		@file_put_contents($url_path, unicode_encode($css_content));
		@file_put_contents($url_path, $css_content);
	}
	return get_domain()."/".APP_ROOT.'/../'.$css_url;
}

function user_login(){
	//会员自动登录及输出
	
	if($GLOBALS['wx_info'])
	{
		$userinfo = get_user_has("wx_openid",$GLOBALS['wx_info']['openid']);
// 		print_r($userinfo);
		$cookie_uname = $userinfo['user_name'];
		$cookie_upwd = $userinfo['user_pwd'];
		
		//logUtils::log_str($cookie_uname);
		//logUtils::log_str($cookie_upwd);
// 		echo $cookie_uname." ".$cookie_upwd;exit;
		if($cookie_uname!=''&&$cookie_upwd!='')
		{
			//logUtils::log_str("=======1=======");
			$cookie_uname = addslashes(trim(htmlspecialchars($cookie_uname)));
			$cookie_upwd = addslashes(trim(htmlspecialchars($cookie_upwd)));
			require_once APP_ROOT_PATH."system/model/user.php";
			
			//require_once APP_ROOT_PATH."app/Lib/common.php";
			auto_do_login_user($cookie_uname,$cookie_upwd,false);
		
			//logUtils::log_str("========2=========");
		}
	}
	else 
	{
		$cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
		$cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';
		
		//logUtils::log_str($cookie_uname);
		//logUtils::log_str($cookie_upwd);
		
		if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
		{
			//logUtils::log_str("=======1=======");
			$cookie_uname = addslashes(trim(htmlspecialchars($cookie_uname)));
			$cookie_upwd = addslashes(trim(htmlspecialchars($cookie_upwd)));
			require_once APP_ROOT_PATH."system/model/user.php";
			//require_once APP_ROOT_PATH."app/Lib/common.php";
			auto_do_login_user($cookie_uname,$cookie_upwd);
		
			//logUtils::log_str("========2=========");
		}
	}
	
	
}



//解析URL标签
// $str = u:shop|acate#index|id=10&name=abc
function parse_wap_url_tag($str)
{
	$key = md5("WAP_URL_TAG_".$str);
	if(isset($GLOBALS[$key]))
	{
		return $GLOBALS[$key];
	}

	$url = load_dynamic_cache($key);
	$url=false;
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	$str = substr($str,2);
	$str_array = explode("|",$str);
	$app_index = $str_array[0];
	$route = $str_array[1];
	$param_tmp = explode("&",$str_array[2]);
	$param = array();

	foreach($param_tmp as $item)
	{
		if($item!='')
			$item_arr = explode("=",$item);
		if($item_arr[0]&&$item_arr[1])
			$param[$item_arr[0]] = $item_arr[1];
	}
	$GLOBALS[$key]= wap_url($app_index,$route,$param);
	set_dynamic_cache($key,$GLOBALS[$key]);
	return $GLOBALS[$key];
}
//以下微信支付有调用getMConfig and get_user_has
function get_user_has($key,$value){
	$row=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."user where $key='".$value."'");
	
	if($row){
		return $row;
	}else{
		return false;
	}
}

function getMConfig(){

	$m_config = $GLOBALS['cache']->get("m_config_sj");

	if($m_config===false || true)
	{
		$m_config = array();
		$sql = "select code,val from ".DB_PREFIX."m_config";
		$list = $GLOBALS['db']->getAll($sql);
		foreach($list as $item){
			$m_config[$item['code']] = $item['val'];
		}

		$catalog_id = intval($m_config['catalog_id']);
		$event_cate_id = intval($m_config['event_cate_id']);
		$shop_cate_id = intval($m_config['shop_cate_id']);

		if ($catalog_id == 0){
			$m_config["catalog_id_name"] = "全部分类";
		}else{
			$m_config["catalog_id_name"] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$catalog_id);
		}

		if ($event_cate_id == 0){
			$m_config["event_cate_id_name"] = "全部分类";
		}else{
			$m_config["event_cate_id_name"] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."event_cate where id = ".$event_cate_id);
		}

		if ($shop_cate_id == 0){
			$m_config["shop_cate_id_name"] = "全部分类";
		}else{
			$m_config["shop_cate_id_name"] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."shop_cate where id = ".$shop_cate_id);
		}

		/*
		 //支付列表
		 $sql = "select pay_id as id, code, title as name, has_calc from ".DB_PREFIX."m_config_list where `group` = 1 and is_verify = 1";
		 $list = $GLOBALS['db']->getAll($sql);
		 $payment_list = array();
		 foreach($list as $item){
			$payment_list[] = array("id"=>$item['id'],"code"=>$item['code'],"name"=>$item['name'],"has_calc"=>$item['has_calc']);
			}
			$m_config['payment_list'] = $payment_list;
			*/

		$m_config['payment_list'] = array();

		//配置方式
		$sql = "select id, id as code, name, 1 as has_calc from ".DB_PREFIX."delivery";
		$list = $GLOBALS['db']->getAll($sql);
		$delivery_list = array();
		foreach($list as $item){
			$delivery_list[] = array("id"=>$item['id'],"code"=>$item['code'],"name"=>$item['name'],"has_calc"=>$item['has_calc']);
		}
		$m_config['delivery_list'] = $delivery_list;
		//$order_parm['delivery_list'] = $MConfig['delivery_list'];//$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery");

		//发票内容
		$sql = "select id, title as name from ".DB_PREFIX."m_config_list where `group` = 6 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$invoice_list = array();
		foreach($list as $item){
			$invoice_list[] = array("id"=>$item['id'],"name"=>$item['name']);
		}
		$m_config['invoice_list'] = $invoice_list;

		//配送日期选择
		$sql = "select code, title as name from ".DB_PREFIX."m_config_list where `group` = 2 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$delivery_time_list = array();
		foreach($list as $item){
			$delivery_time_list[] = array("id"=>$item['code'],"name"=>$item['name']);
		}
		$m_config['delivery_time_list'] = $delivery_time_list;



		//购物车信息提示
		$sql = "select code, title as name,money from ".DB_PREFIX."m_config_list where `group` = 3 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$yh = array();
		foreach($list as $item){
			$yh[] = array("info"=>$item['name'],"money"=>$item['money']);
		}
		$m_config['yh'] = $yh;


		//新闻公告
		$sql = "select code as title, title as content from ".DB_PREFIX."m_config_list where `group` = 4 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$newslist = array();
		foreach($list as $item){
			$newslist[] = array("title"=>$item['title'],"content"=>$item['content']);
		}
		$m_config['newslist'] = $newslist;


		//地址标题
		$sql = "select code, title from ".DB_PREFIX."m_config_list where `group` = 5 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$addrtlist = array();
		foreach($list as $item){
			$addrtlist[] = array("code"=>$item['code'],"title"=>$item['title']);
		}
		$m_config['addr_tlist'] = $addrtlist;

		$GLOBALS['cache']->set("m_config_sj",$m_config,3600);
	}
	return $m_config;
}

?>
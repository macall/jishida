<?php 
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 非明确的操作时,禁止提交如下几个参数名(因为这个参数名，会被覆盖)
 $request_param['city_id']=$city_id;
 $request_param['city_name']=$city_name;

 $request_param['uid']=es_session::get("uid");
 $request_param['pwd']=es_session::get("user_pwd");
 $request_param['email']=es_session::get("user_name");

 $request_param['supplier_id']=es_session::get("supplier_id");
 $request_param['biz_email']=es_session::get("biz_email");
 $request_param['biz_pwd']=es_session::get("biz_pwd");

 $request_param['m_latitude']= es_session::get("m_latitude");
 $request_param['m_longitude']= es_session::get("m_longitude");
 */
define("FOLDER_NAME","");
define('TMPL_NAME','fanwe');
//define("IS_DEBUG",true);

require '../system/common.php';
require '../system/system_init.php';
require './lib/page.php';

require './lib/functions.php';

require './lib/transport.php';


require './lib/template.php';


define('AS_LOG_DIR', APP_ROOT_PATH.'wap/log/');
define('AS_DEBUG', true);
require './lib/logUtils.php';

if (es_cookie::is_set("is_pc")){
	es_cookie::delete("is_pc");
}

$transport = new transport;
$transport->use_curl = true;
//调用模板引擎
//require_once  APP_ROOT_PATH.'system/template/template.php';
if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/tpl_caches/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/tpl_caches/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/tpl_compiled/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/tpl_compiled/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/statics/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/statics/',0777);

$tmpl = new WapTemplate;
$tmpl->template_dir   = APP_ROOT_PATH . 'wap/tpl/'.TMPL_NAME;
$tmpl->cache_dir      = APP_ROOT_PATH . 'public/runtime/wap/tpl_caches';
$tmpl->compile_dir    = APP_ROOT_PATH . 'public/runtime/wap/tpl_compiled';
$tmpl->assign("TMPL_REAL", APP_ROOT_PATH . 'wap/tpl/'.TMPL_NAME);
//定义模板路径
$tmpl_path = get_domain().APP_ROOT.'/tpl/'.TMPL_NAME;
$tmpl->assign("TMPL",$tmpl_path);

if (isset($_REQUEST['i_type']))
{
	$i_type = intval($_REQUEST['i_type']);
}

$_REQUEST = array_merge($_GET,$_POST);
$request_param = $_REQUEST;

//将客户ip,传到mapi接口
$request_param['client_ip']= get_client_ip();

if(isset($request_param['ctl'])){
	$class = strtolower(strim($request_param['ctl']));
		
}else{
	$class='index';
}

if(isset($request_param['act'])){
$act2 = strtolower(strim($request_param['act']))?strtolower(strim($request_param['act'])):"";
}else{
	$act2='index';
}

if (empty($act2)) $act2='index';



$is_weixin=isWeixin();
$m_config = getMConfig();//初始化手机端配置

//用户登陆处理;
user_login();

$user_info = es_session::get('user_info');
if($class == 'index' && $act2 == 'index'){
    if($user_info['service_type_id'] == 2){//技师
        $class = 'tech_order_list';
        $act2 == 'index';
    }elseif($user_info['service_type_id'] == 3){
        $class = 'mana_tech_list';
        $act2 == 'index';
    }
}

$request_param['session_id'] = es_session::id();
require_once APP_ROOT_PATH.'system/utils/weixin.php';

if($_REQUEST['code']&&$_REQUEST['state']==1&&$m_config['wx_app_key']&&$m_config['wx_app_secret'] &&!$user_info){
	require_once APP_ROOT_PATH.'system/model/user.php';
	
	$weixin=new weixin($m_config['wx_app_key'],$m_config['wx_app_secret'],get_domain().APP_ROOT."/wap/index.php");
	global $wx_info;
	$wx_info=$weixin->scope_get_userinfo($_REQUEST['code']);
	
	$GLOBALS['tmpl']->assign('wx_info',$wx_info);
	//用户未登陆
	if($wx_info['openid']){

		$wx_user_info=get_user_has('wx_openid',$wx_info['openid']);

		if($wx_user_info){
			//如果会员存在，直接登录
			do_login_user($wx_user_info['mobile'],$wx_user_info['user_pwd']);
		}else{
			//会员不存在进入登录流程
			$class='user_wx_register';
			//app_redirect(wap_url('index','user_wx_register'));
		}
	}
}else{
	
	if($is_weixin&&!$user_info&&$m_config['wx_app_key']&&$m_config['wx_app_secret']&&$class!='user_wx_register'&&$class!='register_verify_phone'&&$class!='wx_do_register'){
		
		//echo $class;exit;
		$weixin_2=new weixin($m_config['wx_app_key'],$m_config['wx_app_secret'],get_domain().$_SERVER["REQUEST_URI"]);
		$wx_url=$weixin_2->scope_get_code();
		app_redirect($wx_url);
	}

}

//获取模板文件的名称
$tmpl_dir=$class.'_'.$act2.'.html';
//=========================

//$request_url = 'http://127.0.0.1/'.str_replace('/wap', '', APP_ROOT).'/sjmapi/index.php';
$request_url = get_domain().str_replace('/wap', '', APP_ROOT).'/sjmapi/index.php';
//echo $request_url;exit;
//echo get_domain()."<br>;".APP_ROOT; exit;

$city_id = intval($request_param['city_id']);//用户从前台直接选择某个城市
$city_name = strim($request_param['city_name']);

if($city_id == 0 && es_session::get("city_id")){	
	$city_id=es_session::get("city_id");
	$city_name=es_session::get("city_name");	
}

if ($city_id == 0){
	//require_once  '../app/Lib/common.php';
	$deal_city = get_cur_deal_city();//通过IP定位城市id
	$city_id = $deal_city['id'];
	$city_name = $deal_city['name'];
}

//存储当前城市
es_session::set("city_id",$city_id);
es_session::set("city_name",$city_name);

//存储邀请人的id
if($_REQUEST['r'])
{
	$rid = intval(base64_decode($_REQUEST['r']));
	$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval($rid)));
	es_cookie::set("REFERRAL_USER",intval($ref_uid));
}
else
{
	//获取存在的推荐人ID
	if(intval(es_cookie::get("REFERRAL_USER"))>0)
	$ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".intval(es_cookie::get("REFERRAL_USER"))));
	$GLOBALS['tmpl']->assign('ref_uid',$ref_uid);//邀请人的id
}

$request_param['city_id']=$city_id;
$request_param['city_name']=$city_name;
if (!isset($request_param['m_latitude']) && es_session::get("m_latitude")){
	$request_param['m_latitude']= es_session::get("m_latitude");
	$request_param['m_longitude']= es_session::get("m_longitude");
}

if($class =='login_out'){
	/*
	es_session::delete("uid");
	es_session::delete("user_name");
	es_session::delete("user_pwd");
	
	
	
	//cookie
	es_cookie::delete("uid");
	*/
	
	es_cookie::delete("user_name");
	es_cookie::delete("user_pwd");
	es_session::delete("user_info");
	
	showSuccess('退出成功!',0,wap_url('index','login#index'));
}
if($class =='app_down'){
	es_cookie::set("is_app_down",1);
	app_redirect(wap_url('index','index'));
}
if($class =='biz_unset'){
	es_session::delete("supplier_id");
	es_session::delete("biz_email");
	es_session::delete("biz_pwd");
	showSuccess('退出成功!',0,wap_url('index','biz_login#index'));
}




if($user_info)
{
	$request_param['uid']= intval($user_info["id"]);
	$request_param['pwd']=$user_info["user_pwd"];
	$request_param['email']=$user_info["user_name"];
	
	$user_info['user_avatar']=get_muser_avatar($user_info['id'],"big");
	$GLOBALS['tmpl']->assign('user_info',$user_info);
}

//logUtils::log_obj($user_info);
/*
//if(es_session::get("uid")){// && ($class != "login" && $class !='register' && $class !='register_verify_code')){
if(es_cookie::get("user_info") || es_session::get("user_info")){	
	//$request_param['uid']=es_session::get("uid");
	//$request_param['pwd']=es_session::get("user_pwd");
	//$request_param['email']=es_session::get("user_email");
	
	
	
	if(es_session::get("user_info")){
		$user_info = es_session::get('user_info');
		
		$request_param['uid']=es_cookie::get("uid");
		$request_param['pwd']=es_cookie::get("user_pwd");
		$request_param['email']=es_cookie::get("user_name");
	}else{
		$request_param['uid']=es_session::get("uid");
		$request_param['pwd']=es_session::get("user_pwd");
		$request_param['email']=es_session::get("user_name");
	}
}
*/

if(es_session::get("supplier_id")){
	$request_param['supplier_id']=es_session::get("supplier_id");
	$request_param['biz_email']=es_session::get("biz_email");
	$request_param['biz_pwd']=es_session::get("biz_pwd");
}

//如果商家已经登陆,再点：登陆按钮时,则直接转到验证界面
if($class =='biz_login' && $request_param['supplier_id'] > 0){
	app_redirect(wap_url('index','biz_input_page'));
}

//如果用户已经登陆,再点：登陆按钮时,则直接转到会员中心界面
if($class =='login' && $request_param['uid'] > 0){
	//logUtils::log_obj($request_param);
	app_redirect(wap_url('index','user_center'));
}


//logUtils::log_obj($request_param);

if($request_param['post_type']!='json'){
	
	$request_param['act']=$class;
	$request_param['r_type']=0;
	$request_param['i_type']=1;
	$request_param['from']='wap';
	
	if($request_param['post_type']=='fensi'){
		$request_param['fensi']='fensi';
	}
	

	if($class=='postcart'){
		$request_param = do_postcart($request_param);
		if ($request_param['post_type'] ==''){
			$request_data=$GLOBALS['transport']->request($request_url,$request_param);
		}
	}

	elseif($class=='calc_cart'){

		//$request_param = do_calc_cart($request_param);

		if (isset($request_param['num'])){			
			$first_calc = 1;
			$session_cart_data=es_session::get("cart_data");			
			foreach($request_param['num'] as $k=>$v){				
				if (isset($session_cart_data[$k]))
					$session_cart_data[$k]['num'] = $v;
			}
			es_session::set("cart_data",$session_cart_data);
		}else{
			$first_calc = intval($request_param['first_calc']);
		}

		
		//如果商品为空的放，跳转到首页
		$session_cart_data=es_session::get("cart_data");
		if (count($session_cart_data) == 0){
			//购物车中,没有商品了，跳转到首页；因为pay_order会清空购物车,
			showErr('购物车未有商品',0,wap_url("index"));
		}else{
			$request_param['first_calc']= $first_calc;
			$request_param['cartdata']=str_replace("+","%2B",base64_encode(serialize(es_session::get("cart_data"))));
			$request_data=$GLOBALS['transport']->request($request_url,$request_param);
		}			
	}
	elseif($class=='done_cart'){	
		//print_r(es_session::get("cart_data")); exit;
		//如果商品为空的放，跳转到首页
		$session_cart_data=es_session::get("cart_data");
		if (count($session_cart_data) == 0){
			//购物车中,没有商品了，跳转到首页；因为pay_order会清空购物车,
			showErr('购物车未有商品',0,wap_url("index"));
		}else{
			$request_param['cartdata']=str_replace("+","%2B",base64_encode(serialize(es_session::get("cart_data"))));
		
			$request_data=$GLOBALS['transport']->request($request_url,$request_param);
		}
	}
	elseif($class=='add_addr'){
		//过滤掉,不调用接口,要不然会产生一条空记录 chenfq by add 2014-08-26
	}else{//否则
		
		$request_data=$GLOBALS['transport']->request($request_url,$request_param);
//                                $str = '';
//                foreach ($request_param as $key => $value) {
//                    $str .= $key . '=' .$value . '&';
//                }
//                print_r($request_url.'?'.$str);
	}

	$data=$request_data['body'];

	$data=json_decode(base64_decode($data),1);
//	print_r($data);exit;
	if ($request_param['is_debug'] == 1){
		print_r($data);exit;
	}

	
	 //判断是否需要登陆
	if(isset($data['user_login_status']) && $data['user_login_status'] == 0 &&  $class != "biz_login" &&  $class != "app_down" &&  $class != "pwd" && $class != "login" && $class !='register' && $class !='register_verify_code') {
	
		//接口需要求登陆,并且未登陆时,提示用户登陆;
		//es_session::delete("uid");
		//es_session::delete("user_email");
		//es_session::delete("user_pwd");

	
		if ($class == "biz_input_page"){
			es_session::delete("supplier_id");
			es_session::delete("biz_email");
			es_session::delete("biz_pwd");
			
			showSuccess('请先登陆!',0,wap_url('index','biz_login#index'));
		}else{
			es_cookie::delete("user_name");
			es_cookie::delete("user_pwd");
			es_session::delete("user_info");
			
			showSuccess('请先登陆!',0,wap_url('index','login#index'));
		}
	
	}
	
	
        //如果非法用户,则重定向到找回密码页面
        if($class =='get_password_resetting' && ($data['get_password_resetting_no_user'] == 1 || $data['get_password_resetting_wrong_code'] == 1 )){
               app_redirect(wap_url('index','get_password'));
        }
	
	//$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
	//echo $domain;exit;
	
	if(isset($data['page']) && is_array($data['page'])){
		//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了		
		$page = new Page($data['page']['page_total'],$data['page']['page_size']);   //初始化分页对象 	
		//$page->parameter
		$p  =  $page->show();
		//print_r($p);exit;
		$GLOBALS['tmpl']->assign('pages',$p);
	}

	if($class=='done_cart' && $data['return']==1){
		if($data['status'] ==1){
			app_redirect(wap_url('index','pay_order&order_id='.$data['order_id']));
		}
		else{
			showSuccess($data['info'],0,wap_url('index','calc_cart#index'));
		}
	}
	if($class=='done_order' && $data['status']==1){
		app_redirect(wap_url('index','pay_order&order_id='.$data['order_id']));
	}
	
	if($class=='calc_cart' && isset($data['mobile_user_id']) && intval($data['mobile_user_id']) > 0){
		//将会员信息存在session中
		/*
		es_session::set('uid',intval($data['mobile_user_id']));
		es_session::set('user_email',$data['mobile_user_name']);
		es_session::set('user_pwd',$data['mobile_user_pwd']);
		//cookie
		es_cookie::set('uid',intval($data['mobile_user_id']),3600*24*365);
		es_cookie::set('user_email',$data['mobile_user_name'],3600*24*365);
		es_cookie::set('user_pwd',$data['mobile_user_pwd'],3600*24*365);
		*/
		
		es_cookie::set("user_name",$data['mobile_user_name'],3600*24*30);
		es_cookie::set("user_pwd",md5($data['mobile_user_pwd']."_EASE_COOKIE"),3600*24*30);	

		//用户登陆处理;
		user_login();
	}
	
	if($class=='pay_order'){
		

		//微信v3版跳转
		//print_r($data['is_wap_url']); echo "<br>";echo $data['wap_notify_url'];exit;
		if($data['wap_notify_url'] && $data['is_wap_url']==1)
		{
			Header("location:".$data['wap_notify_url']);
			exit;
		}
		//在支付界面时,清空购买车,但如果清空了,用户点：返回 后，再去购买时,会购买空商品，这个需要注意处理一下
		$session_cart_data=es_session::get("cart_data");
		unset($session_cart_data);
		es_session::set("cart_data",$session_cart_data);
		es_session::set("cart_data",array());
		es_session::delete("cart_data");
	
	}
	
	if($class=='index'){
		//已经执行过定位时，首页不再做定位操作
		if (es_session::is_set('m_latitude'))
			$GLOBALS['tmpl']->assign('has_location',1);
		else
			$GLOBALS['tmpl']->assign('has_location',0);
	}		
	//echo $tmpl_dir; exit;
	//print_r($request_param);exit;
	$GLOBALS['tmpl']->assign('request',$request_param);
	$GLOBALS['tmpl']->assign('is_ajax',intval($request_param['is_ajax']));

	$GLOBALS['tmpl']->assign('data',$data);
	$GLOBALS['tmpl']->assign('APP_ROOT',APP_ROOT);
	$GLOBALS['tmpl']->assign("PC_URL",get_domain().str_replace('/wap',"",APP_ROOT));
	
	if (es_session::get('user_info')){
		$GLOBALS['tmpl']->assign('is_login',1);//用户已登陆
	}else{
		$GLOBALS['tmpl']->assign('is_login',0);//用户未登陆
	}
	if (es_cookie::get('is_app_down')){
		$GLOBALS['tmpl']->assign('is_app_down',1);//用户已登陆
	}else{
		$GLOBALS['tmpl']->assign('is_app_down',0);//用户未登陆
	}

	//==============================
	//判断是否有缓存
	//echo $tmpl_dir; exit;
	//生成缓存的ID
	
	//$cache_id  = md5($class.$act2.trim($request_param['id']).$city_id);	
	//if (!$GLOBALS['tmpl']->is_cached($tmpl_dir, $cache_id)){}
	
	$GLOBALS['tmpl']->display($tmpl_dir);
}else{
	$request_param['from']='wap';
	$request_param['act']=$class;
	//$request_param['i_type']=2;
	//$request_param['r_type']=0;
	
	$postData = array();
	$postData['i_type']=0;
	$postData['r_type']=0;	 
	$postData['requestData'] = str_replace("+","%2B",base64_encode(json_encode($request_param)));
	
	$request_data=$GLOBALS['transport']->request($request_url,$postData);
	$data=$request_data['body'];
// 	echo $data;exit;
	//@eval("\$data = ".$data.';');
	$data=base64_decode($data);

	/*
	//判断是否需要登陆
	if(isset($data['user_login_status']) && $data['user_login_status'] == 0 && $class != "login" && $class !='register' && $class !='register_verify_code') {
		//接口需要求登陆,并且未登陆时,提示用户登陆;
		es_session::delete("uid");
		es_session::delete("user_email");
		es_session::delete("user_pwd");
	
		es_session::delete("supplier_id");
		es_session::delete("biz_email");
		es_session::delete("biz_pwd");
	
		showSuccess('请先登陆!',0,url('index','login#index'));
	
	}*/
	
	if($class=='register' || $class=='register_verify_code'){
		$i=json_decode($data);
 		if($i->return==1){
 			/*
			//将会员信息存在session中
			es_session::set('uid',$i->uid);			
			es_session::set('user_name',$i->user_name);			
			es_session::set('user_pwd',$i->user_pwd);
			*/
 			
 			//logUtils::log_obj($i);
 			
 			es_session::delete("user_info");
			es_cookie::set("user_name",$i->user_name,3600*24*30);
			es_cookie::set("user_pwd",md5($i->user_pwd."_EASE_COOKIE"),3600*24*30);
		}
	}
	if($class=='pwd'){
		$i=json_decode($data);
		if($i->return==1){
			//es_session::set('user_pwd',$request_param['newpassword']);
			es_session::delete("user_info");
			es_cookie::set("user_pwd",md5($i->user_pwd."_EASE_COOKIE"),3600*24*30);
		}
	}
	
	if($class=='login'){
		$i=json_decode($data);
 		if($i->return==1){
 			/*
			//将会员信息存在session中			
 			es_session::set('uid',$i->uid);
			es_session::set('user_name',$i->user_name);
			es_session::set('user_pwd',$request_param['pwd']);
			//cookie
			es_cookie::set('uid',$i->uid,3600*24*365);
			es_cookie::set('user_name',$i->user_name,3600*24*365);
			es_cookie::set('user_pwd',$request_param['pwd'],3600*24*365);
			*/
 			es_session::delete("user_info");
			es_cookie::set("user_name",$i->user_name,3600*24*30);
			es_cookie::set("user_pwd",md5($i->user_pwd."_EASE_COOKIE"),3600*24*30);
		}
	}
	if($class=='biz_login'){
		$i=json_decode($data);
 		if($i->status==1){
			//将会员信息存在session中
			es_session::set('supplier_id',$i->supplier_id);
			es_session::set('biz_email',$i->biz_email);
			es_session::set('biz_pwd',$i->biz_pwd);
		}
	}
	
	if($class=='changecity'){
		$i=json_decode($data);
		//print_r($i);
		if($i->status==1){
			//将城市定位信息保存在session中
			es_session::set('city_id',$i->city_id);
			es_session::set('city_name',$i->city_name);
			es_session::set('m_latitude',$i->m_latitude);
			es_session::set('m_longitude',$i->m_longitude);
		}
	}
	
	if($class=='userxypoint'){
		$i=json_decode($data);
		if($i->status==1){
			//将坐标定位信息保存在session中
			es_session::set('m_latitude',$i->m_latitude);
			es_session::set('m_longitude',$i->m_longitude);
		}
	}	
	echo $data;
}

?>
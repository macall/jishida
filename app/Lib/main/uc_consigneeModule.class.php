<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------



class uc_consigneeModule extends MainBaseModule
{
	public function index()
	{
		require APP_ROOT_PATH."system/model/uc_center_service.php";
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}	
		
		$user_id=intval($GLOBALS['user_info']['id']);
		//输出所有配送方式
		$consignee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_consignee where user_id = ".$user_id);
		foreach($consignee_list as $k=>$v){
			$consignee_list[$k]['del_url']=url('index','uc_consignee#del',array('id'=>$v['id']));
			$consignee_list[$k]['dfurl']=url('index','uc_consignee#set_default',array('id'=>$v['id']));			
			$consignee_list[$k]['region_lv2']  = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv2']);
			$consignee_list[$k]['region_lv3']  = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv3']);
			$consignee_list[$k]['region_lv4']  = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv4']);
		}

		
		$GLOBALS['tmpl']->assign("consignee_list",$consignee_list);
		
		$GLOBALS['tmpl']->assign("page_title","配送地址");
		assign_uc_nav_list();//左侧导航菜单	
		$GLOBALS['tmpl']->display("uc/uc_consignee.html");
		
	}
	
	public function add()
	{
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}
		if(intval($_REQUEST['id'])>0)$GLOBALS['tmpl']->assign("consignee_id",intval($_REQUEST['id']));		
		$GLOBALS['tmpl']->assign("page_title","配送地址");
		assign_uc_nav_list();//左侧导航菜单	
		$GLOBALS['tmpl']->display("uc/uc_consignee_add.html");	
	}
	
	public function save()	
	{
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$result['status'] = 2;
			ajax_return($result);	
		}
		
		$consignee_id = intval($_REQUEST['region_id']);
		$region_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']);
		if($region_count>=5&&$consignee_id ==0){
			$result['status'] = 3;
			ajax_return($result);				
		}
		
		if(strim($_REQUEST['consignee'])=='')
		{
			showErr($GLOBALS['lang']['FILL_CORRECT_CONSIGNEE'],1);
		}
		if(strim($_REQUEST['address'])=='')
		{
			showErr($GLOBALS['lang']['FILL_CORRECT_ADDRESS'],1);
		}
		if(strim($_REQUEST['zip'])=='')
		{
			showErr($GLOBALS['lang']['FILL_CORRECT_ZIP'],1);
		}
		if(strim($_REQUEST['mobile'])=='')
		{
			showErr($GLOBALS['lang']['FILL_MOBILE_PHONE'],1);
		}
		if(!check_mobile($_REQUEST['mobile']))
		{
			showErr($GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'],1);
		}

		$consignee_data['user_id'] = $GLOBALS['user_info']['id'];
		$consignee_data['region_lv1'] = intval($_REQUEST['region_lv1']);
		$consignee_data['region_lv2'] = intval($_REQUEST['region_lv2']);
		$consignee_data['region_lv3'] = intval($_REQUEST['region_lv3']);
		$consignee_data['region_lv4'] = intval($_REQUEST['region_lv4']);
		$consignee_data['address'] = strim($_REQUEST['address']);
		$consignee_data['mobile'] = strim($_REQUEST['mobile']);
		$consignee_data['consignee'] = strim($_REQUEST['consignee']);
		$consignee_data['zip'] = strim($_REQUEST['zip']);
		$consignee_count = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_consignee where user_id = ".$GLOBALS['user_info']['id']));
		if($consignee_count==0)
		{
			$consignee_data['is_default'] = 1;
		}
		
		if($consignee_id == 0)
		{
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$consignee_data);
		}
		else
		{			
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_consignee",$consignee_data,"UPDATE","id=".$consignee_id." and user_id=".$GLOBALS['user_info']['id']);
		}
		rm_auto_cache("consignee_info",array("consignee_id"=>intval($consignee_id)));
		$result['status'] = 1;
		$result['url'] = url('index','uc_consignee');
		ajax_return($result);		
		
	}
	
	public function del(){
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$result['status'] = 2;
			ajax_return($result);	
		}
		$id=intval($_REQUEST['id']);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."user_consignee where id=".$id." and user_id=".intval($GLOBALS['user_info']['id']));
		if($GLOBALS['db']->affected_rows())
		{
			showSuccess($GLOBALS['lang']['DELETE_SUCCESS'],1);
		}
		else
		{
			showErr("删除失败",1);
		}
		
	}
	
	public function set_default(){
		global_run();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			$result['status'] = 2;
			ajax_return($result);	
		}
		$id=intval($_REQUEST['id']);
		$GLOBALS['db']->query("update ".DB_PREFIX."user_consignee set is_default=0 where user_id=".intval($GLOBALS['user_info']['id']));
		$GLOBALS['db']->query("update ".DB_PREFIX."user_consignee set is_default=1 where id=".$id." and user_id=".intval($GLOBALS['user_info']['id']));	
		if($GLOBALS['db']->affected_rows())
		{
			showSuccess("设置成功",1);
		}
		else
		{
			showErr("操作失败",1);
		}	
	}
	
}
?>
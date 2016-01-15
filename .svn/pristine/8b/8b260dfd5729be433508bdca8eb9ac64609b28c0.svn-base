<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------


class app_downloadModule extends MainBaseModule
{
	public function index()
	{	
		//用户app下载地址连接
		if (isios()){
			//$down_url = app_conf("APPLE_PATH");
			$down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'ios_down_url'");
		}else{
			//$down_url = app_conf("ANDROID_PATH");
			$down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'android_filename'");
		}	
		app_redirect($down_url);		
	}	
	
}
?>
<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

require_once APP_ROOT_PATH."system/model/city.php";
require_once APP_ROOT_PATH.'system/model/deal.php';

class dhapiModule extends MainBaseModule
{
	public function index()
	{	
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>url("index","index"));
		$site_nav[] = array('name'=>$GLOBALS['lang']['API_LIST'],'url'=>url("index","dhapi"));
		$GLOBALS['tmpl']->assign("site_nav",$site_nav);		
		
		$directory = APP_ROOT_PATH."dh/";
		
		$read_api = true;
		$dir = @opendir($directory);
	    $apis = array();		
		 
	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {
	            $tmp = require_once($directory .$file);
	            if($tmp)
	            {
	            	$apis[] = $tmp;
	            }
	        }
	    }
	    @closedir($dir);
	    unset($read_api);
	
	
		$contents_html = '<table>';
		foreach($apis as $k=>$v)
		{
			foreach($v['info'] as $kk=>$vv)
			{
				$contents_html.="<tr><td style='padding:10px 25px 10px 5px;'>";
				$contents_html.= $vv['name'].":</td><td style='padding:10px 5px 10px 5px;'><input type='text' style='width:350px;' class='f-input' value='".get_domain().APP_ROOT."/dh/".$vv['url']."' /></td>";
				$contents_html.="</tr>";
			}
		}
		$contents_html .= '</table>';
		
		$GLOBALS['tmpl']->assign("page_title", $GLOBALS['lang']['API_LIST']);
		$GLOBALS['tmpl']->assign("page_keyword",$GLOBALS['lang']['API_LIST']);
		$GLOBALS['tmpl']->assign("page_description",$GLOBALS['lang']['API_LIST']);
	
	
		$article['title'] = $GLOBALS['lang']['API_LIST'];
		$article['content'] = $contents_html;
		$GLOBALS['tmpl']->assign("article",$article);
		$GLOBALS['tmpl']->display("dhapi.html");
	    
	}	

	
	
}
?>
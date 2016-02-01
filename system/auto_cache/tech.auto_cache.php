<?php
//加载商品信息的模块
class tech_auto_cache extends auto_cache{
	public function load($param)
	{
// 		$key = $this->build_key(__CLASS__,$param);
// 		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
// 		$tech = $GLOBALS['cache']->get($key);				
// 		if($tech === false)
// 		{		
// 			$tech = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."tech where id = ".intval($param['id']));
// 			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
// 			$GLOBALS['cache']->set($key,$tech);
// 		}
		$param = array("id"=>$param['id']);
		$tech_key = strim($param['id']);
		static $tech;
		if(!$tech[$tech_key])
		{
			if($GLOBALS['distribution_cfg']['CACHE_TYPE']!="File")
			{
				$key = $this->build_key(__CLASS__,$param);
				$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
				$tech_info = $GLOBALS['cache']->get($key);
			}
			else 
			{
				$tech_info = false;
			}
			if($tech_info===false)
			{
				$tech_info =  $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".$tech_key."'");
				
				if($tech_info['uname'])
					$param['id'] = $tech_info['uname'];
				else
					$param['id'] = intval($tech_info['id']);
				
				$key = $this->build_key(__CLASS__,$param);
				$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
				$tech_info = $GLOBALS['cache']->get($key);
				if($tech_info!==false)return $tech_info;

				$tech_key = $param['id'];	
				$tech_info =  $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".$tech_key."' and is_delete = 0");				
				if($tech_info)
				{
					
					if($tech_info['uname']!='')
						$durl = url("index","deal#".$tech_info['uname']);
					else
						$durl = url("index","tech#".$tech_info['id']);
					
					$tech_info['url'] = $durl;
					if($GLOBALS['distribution_cfg']['CACHE_TYPE']!="File")
					{
						$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
						$GLOBALS['cache']->set($key,$tech_info,30);
					}
				}				
			}
			$tech[$tech_key] = $tech_info;
			
		}

		return $tech[$tech_key];	
	}
	public function rm($param)
	{
		if($GLOBALS['distribution_cfg']['CACHE_TYPE']=="File")return false;
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->rm($key);
	}
	public function clear_all()
	{
		if($GLOBALS['distribution_cfg']['CACHE_TYPE']=="File")return false;
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->clear();
	}
}
?>
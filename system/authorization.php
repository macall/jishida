<?php
ini_set("display_errors", 0);
error_reporting(0);
define("APP_TYPE","main");
function init_checker() 
{
	$domain_array = array( base64_encode(base64_encode('www.jsd.cm')), );
	$str = base64_encode(base64_encode(serialize($domain_array))."|".serialize($domain_array));
	$arr = explode("|",base64_decode($str));
	$arr = unserialize($arr[1]);
	foreach($arr as $k=>$v) 
	{
		$arr[$k] = base64_decode(base64_decode($v));
	}
	$host = $_SERVER['HTTP_HOST'];
	$host = explode(":",$host);
	$host = $host[0];
	$passed = false;
	foreach($arr as $k=>$v) 
	{
		if(substr($v,0,2)=='*.') 
		{
			$preg_str = substr($v,2);
			if(preg_match("/".$preg_str."$/",$host)>0) 
			{
				$passed = true;
				break;
			}
		}
	}
	if(!$passed) 
	{
		if(!in_array($host,$arr)) 
		{
			return false;
		}
	}
	return true;
}
$checker = init_checker();
if(!$checker)die("domain not authorized");
$cfg_file = APP_ROOT_PATH.'system/config.php';
if(file_exists($cfg_file)) 
{
	$sys_config = require_once APP_ROOT_PATH.'system/config.php';
}
if(!function_exists("app_conf")) 
{
	function app_conf($name) 
	{
		return stripslashes($GLOBALS['sys_config'][$name]);
	}
}
if(function_exists('date_default_timezone_set')) date_default_timezone_set(app_conf('DEFAULT_TIMEZONE'));
$define_file = APP_ROOT_PATH."system/define.php";
if(file_exists($define_file)) require_once $define_file;
define('DB_PREFIX', app_conf('DB_PREFIX'));
$dist_cfg = APP_ROOT_PATH."system/dist_cfg.php";
if(file_exists($dist_cfg)) $distribution_cfg = require_once $dist_cfg;
$distribution_cfg["CACHE_TYPE"] = "File";
$distribution_cfg["CACHE_LOG"] = false;
$distribution_cfg["SESSION_TYPE"] = "File";
$distribution_cfg['ALLOW_DB_DISTRIBUTE'] = false;
$distribution_cfg["CSS_JS_OSS"] = false;
$distribution_cfg["OSS_TYPE"] = "";
$distribution_cfg["ORDER_DISTRIBUTE_COUNT"] = "1";
$distribution_cfg['DOMAIN_ROOT'] = '';
$distribution_cfg['COOKIE_PATH'] = '/';
if(!function_exists("load_fanwe_cache")) 
{
	function load_fanwe_cache() 
	{
		global $distribution_cfg;
		$type = $distribution_cfg["CACHE_TYPE"];
		$cacheClass = 'Cache'.ucwords(strtolower(strim($type)))."Service";
		if(file_exists(APP_ROOT_PATH."system/cache/".$cacheClass.".php")) 
		{
			require_once APP_ROOT_PATH."system/cache/".$cacheClass.".php";
			if(class_exists($cacheClass)) 
			{
				$cache = new $cacheClass();
			}
			return $cache;
		}
		else 
		{
			$file_cache_file = APP_ROOT_PATH.'system/cache/CacheFileService.php';
			if(file_exists($file_cache_file)) require_once APP_ROOT_PATH.'system/cache/CacheFileService.php';
			if(class_exists("CacheFileService")) $cache = new CacheFileService();
			return $cache;
		}
	}
}
$cache_service_file = APP_ROOT_PATH."system/cache/Cache.php";
if(file_exists($cache_service_file)) require_once $cache_service_file;
if(class_exists("CacheService")) $cache = CacheService::getInstance();
$db_cls_file = APP_ROOT_PATH."system/db/db.php";
if(file_exists($db_cls_file)) 
{
	require_once $db_cls_file;
	if(class_exists("mysql_db")) 
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/db_caches/')) mkdir(APP_ROOT_PATH.'public/runtime/app/db_caches/',0777);
		$pconnect = false;
		$db = new mysql_db(app_conf('DB_HOST').":".app_conf('DB_PORT'), app_conf('DB_USER'),app_conf('DB_PWD'),app_conf('DB_NAME'),'utf8',$pconnect);
	}
}
$tmpl_cls_file = APP_ROOT_PATH.'system/template/template.php';
if(file_exists($tmpl_cls_file)) 
{
	require_once $tmpl_cls_file;
	if(class_exists("AppTemplate")) 
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_caches/')) mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_caches/',0777);
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/')) mkdir(APP_ROOT_PATH.'public/runtime/app/tpl_compiled/',0777);
		$tmpl = new AppTemplate;
	}
}
$lang_file = APP_ROOT_PATH.'/app/Lang/'.app_conf("SHOP_LANG").'/lang.php';
if(file_exists($lang_file)) $lang = require_once $lang_file;
if(!function_exists("replace_public")) 
{
	function replace_public($str)
	{
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE") 
		{
			$domain = $GLOBALS['distribution_cfg']['OSS_DOMAIN'];
		}
		else 
		{
			$domain = SITE_DOMAIN.APP_ROOT;
		}
		return str_replace("./public/",$domain."/public/",$str);
	}
}
if(!function_exists("format_image_path")) 
{
	function format_image_path($out) 
	{
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE") 
		{
			$domain = $GLOBALS['distribution_cfg']['OSS_DOMAIN'];
		}
		else 
		{
			$domain = SITE_DOMAIN.APP_ROOT;
		}
		$out = str_replace(APP_ROOT."./public/",$domain."/public/",$out);
		$out = str_replace("./public/",$domain."/public/",$out);
		return $out;
	}
}
if(!function_exists("syn_to_remote_image_server")) 
{
	function syn_to_remote_image_server($url) 
	{
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE") 
		{
			if($GLOBALS['distribution_cfg']['OSS_TYPE']=="ES_FILE") 
			{
				$pathinfo = pathinfo($url);
				$file = $pathinfo['basename'];
				$dir = $pathinfo['dirname'];
				$dir = str_replace("./public/", "", $dir);
				$filefull = SITE_DOMAIN.APP_ROOT."/public/".$dir."/".$file;
				$syn_url = $GLOBALS['distribution_cfg']['OSS_DOMAIN']."/es_file.php?username=".$GLOBALS['distribution_cfg']['OSS_ACCESS_ID']."&password=".$GLOBALS['distribution_cfg']['OSS_ACCESS_KEY']."&file=". $filefull."&path=".$dir."/&name=".$file."&act=0";
				@file_get_contents($syn_url);
			}
			elseif($GLOBALS['distribution_cfg']['OSS_TYPE']=="ALI_OSS") 
			{
				$pathinfo = pathinfo($url);
				$file = $pathinfo['basename'];
				$dir = $pathinfo['dirname'];
				$dir = str_replace("./public/", "public/", $dir);
				$ali_oss_sdk = APP_ROOT_PATH."system/alioss/sdk.class.php";
				if(file_exists($ali_oss_sdk)) 
				{
					require_once $ali_oss_sdk;
					if(class_exists("ALIOSS")) 
					{
						$oss_sdk_service = new ALIOSS();
						$oss_sdk_service->set_debug_mode(FALSE);
						$bucket = $GLOBALS['distribution_cfg']['OSS_BUCKET_NAME'];
						$object = $dir."/".$file;
						$file_path = APP_ROOT_PATH.$dir."/".$file;
						$oss_sdk_service->upload_file_by_file($bucket,$object,$file_path);
					}
				}
			}
		}
	}
}
if(!function_exists("syn_to_remote_file_server")) 
{
	function syn_to_remote_file_server($url) 
	{
		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE") 
		{
			if($GLOBALS['distribution_cfg']['OSS_TYPE']=="ES_FILE") 
			{
				$pathinfo = pathinfo($url);
				$file = $pathinfo['basename'];
				$dir = $pathinfo['dirname'];
				$dir = str_replace("public/", "", $dir);
				$filefull = SITE_DOMAIN.APP_ROOT."/public/".$dir."/".$file;
				$syn_url = $GLOBALS['distribution_cfg']['OSS_DOMAIN']."/es_file.php?username=".$GLOBALS['distribution_cfg']['OSS_ACCESS_ID']."&password=".$GLOBALS['distribution_cfg']['OSS_ACCESS_KEY']."&file=". $filefull."&path=".$dir."/&name=".$file."&act=0";
				@file_get_contents($syn_url);
			}
			elseif($GLOBALS['distribution_cfg']['OSS_TYPE']=="ALI_OSS") 
			{
				$pathinfo = pathinfo($url);
				$file = $pathinfo['basename'];
				$dir = $pathinfo['dirname'];
				$ali_oss_sdk = APP_ROOT_PATH."system/alioss/sdk.class.php";
				if(file_exists($ali_oss_sdk)) 
				{
					require_once $ali_oss_sdk;
					if(class_exists("ALIOSS")) 
					{
						$oss_sdk_service = new ALIOSS();
						$oss_sdk_service->set_debug_mode(FALSE);
						$bucket = $GLOBALS['distribution_cfg']['OSS_BUCKET_NAME'];
						$object = $dir."/".$file;
						$file_path = APP_ROOT_PATH.$dir."/".$file;
						$oss_sdk_service->upload_file_by_file($bucket,$object,$file_path);
					}
				}
			}
		}
	}
}
if(!class_exists("FanweSessionHandler")) 
{
	class FanweSessionHandler 
	{
		private $savePath;
		private $mem;
		private $db;
		private $table;
		function open($savePath, $sessionName) 
		{
			$this->savePath = APP_ROOT_PATH.$GLOBALS['distribution_cfg']['SESSION_FILE_PATH'];
			if($GLOBALS['distribution_cfg']['SESSION_TYPE']=="MemcacheSASL") 
			{
				$this->mem = require_once APP_ROOT_PATH."system/cache/MemcacheSASL/MemcacheSASL.php";
				$this->mem = new MemcacheSASL;
				$this->mem->addServer($GLOBALS['distribution_cfg']['SESSION_CLIENT'], $GLOBALS['distribution_cfg']['SESSION_PORT']);
				$this->mem->setSaslAuthData($GLOBALS['distribution_cfg']['SESSION_USERNAME'],$GLOBALS['distribution_cfg']['SESSION_PASSWORD']);
			}
			elseif($GLOBALS['distribution_cfg']['SESSION_TYPE']=="Db") 
			{
				$pconnect = false;
				$session_client = $GLOBALS['distribution_cfg']['SESSION_CLIENT']==""?app_conf('DB_HOST'):$GLOBALS['distribution_cfg']['SESSION_CLIENT'];
				$session_port = $GLOBALS['distribution_cfg']['SESSION_PORT']==""?app_conf('DB_PORT'):$GLOBALS['distribution_cfg']['SESSION_PORT'];
				$session_username = $GLOBALS['distribution_cfg']['SESSION_USERNAME']==""?app_conf('DB_USER'):$GLOBALS['distribution_cfg']['SESSION_USERNAME'];
				$session_password = $GLOBALS['distribution_cfg']['SESSION_PASSWORD']==""?app_conf('DB_PWD'):$GLOBALS['distribution_cfg']['SESSION_PASSWORD'];
				$session_db = $GLOBALS['distribution_cfg']['SESSION_DB']==""?app_conf('DB_NAME'):$GLOBALS['distribution_cfg']['SESSION_DB'];
				$this->db = new mysql_db($session_client.":".$session_port, $session_username,$session_password,$session_db,'utf8',$pconnect);
				$this->table = $GLOBALS['distribution_cfg']['SESSION_TABLE']==""?DB_PREFIX."session":$GLOBALS['distribution_cfg']['SESSION_TABLE'];
			}
			else 
			{
				if (!is_dir($this->savePath)) 
				{
					@mkdir($this->savePath, 0777);
				}
			}
			return true;
		}
		function close() 
		{
			return true;
		}
		function read($id) 
		{
			$sess_id = "sess_".$id;
			if($GLOBALS['distribution_cfg']['SESSION_TYPE']=="MemcacheSASL") 
			{
				return $this->mem->get("$this->savePath/$sess_id");
			}
			elseif($GLOBALS['distribution_cfg']['SESSION_TYPE']=="Db") 
			{
				$session_data = $this->db->getRow("select session_data,session_time from ".$this->table." where session_id = '".$sess_id."'",true);
				if($session_data['session_time'])$this->mem->set("$this->savePath/$sess_id",$data,SESSION_TIME);
			}
			elseif($GLOBALS['distribution_cfg']['SESSION_TYPE']=="Db") 
			{
				$session_data = $this->db->getRow("select session_data,session_time from ".$this->table." where session_id = '".$sess_id."'",true);
				if($session_data) 
				{
					$session_data['session_data'] = $data;
					$session_data['session_time'] = NOW_TIME+SESSION_TIME;
					$this->db->autoExecute($this->table, $session_data,"UPDATE","session_id = '".$sess_id."'");
				}
				else 
				{
					$session_data['session_id'] = $sess_id;
					$session_data['session_data'] = $data;
					$session_data['session_time'] = NOW_TIME+SESSION_TIME;
					$this->db->autoExecute($this->table, $session_data);
				}
				return true;
			}
			else 
			{
				return file_put_contents("$this->savePath/$sess_id", $data) === false ? false : true;
			}
		}
		function destroy($id) 
		{
			$sess_id = "sess_".$id;
			if($GLOBALS['distribution_cfg']['SESSION_TYPE']=="MemcacheSASL") 
			{
				$this->mem->delete($sess_id);
			}
			elseif($GLOBALS['distribution_cfg']['SESSION_TYPE']=="Db") 
			{
				$this->db->query("delete from ".$this->table." where session_id = '".$sess_id."'");
			}
			else 
			{
				$file = "$this->savePath/$sess_id";
				if (file_exists($file)) 
				{
					@unlink($file);
				}
			}
			return true;
		}
		function gc($maxlifetime) 
		{
			if($GLOBALS['distribution_cfg']['SESSION_TYPE']=="MemcacheSASL") 
			{
			}
			elseif($GLOBALS['distribution_cfg']['SESSION_TYPE']=="Db") 
			{
				$this->db->query("delete from ".$this->table." where session_time < ".NOW_TIME);
			}
			else 
			{
				foreach (glob("$this->savePath/sess_*") as $file) 
				{
					if (filemtime($file) + SESSION_TIME < time() && file_exists($file)) 
					{
						unlink($file);
					}
				}
			}
			return true;
		}
	}
}
if(!function_exists("es_session_start")) 
{
	function es_session_start($session_id) 
	{
		session_set_cookie_params(0,$GLOBALS['distribution_cfg']['COOKIE_PATH'],$GLOBALS['distribution_cfg']['DOMAIN_ROOT'],false,true);
		if($GLOBALS['distribution_cfg']['SESSION_FILE_PATH']!=""||$GLOBALS['distribution_cfg']['SESSION_TYPE']=="MemcacheSASL"||$GLOBALS['distribution_cfg']['SESSION_TYPE']=="Db") 
		{
			$handler = new FanweSessionHandler();
			session_set_save_handler( array($handler, 'open'), array($handler, 'close'), array($handler, 'read'), array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc') );
		}
		if($session_id) session_id($session_id);
		@session_start();
	}
}
?>
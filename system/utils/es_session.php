<?php 
class es_session
{
	static $sess_id = "";
	static function id()
	{
		self::start();
		self::close();
		return self::$sess_id;;
	}
	static function set_sessid($sess_id)
	{
		self::$sess_id = $sess_id;
	}
	static function start()
	{
		es_session_start(self::$sess_id);
		self::$sess_id = session_id();
	}

	// 判断session是否存在
	static function is_set($name) {
		self::start();
		$tag = isset($_SESSION[app_conf("AUTH_KEY").$name]);
		self::close();
		return $tag;
	}

	// 获取某个session值
	static function get($name) {
		self::start();
		$value   = $_SESSION[app_conf("AUTH_KEY").$name];
		self::close();
		return $value;
	}

	// 设置某个session值
	static function set($name,$value) {
		self::start();
		$_SESSION[app_conf("AUTH_KEY").$name]  =   $value;
		self::close();
	}

	// 删除某个session值
	static function delete($name) {
		self::start();
		unset($_SESSION[app_conf("AUTH_KEY").$name]);
		self::close();
	}

	// 清空session
	static function clear() {
		@session_destroy();
	}

	//关闭session的读写
	static function close()
	{
		@session_write_close();
	}

}
//end session
?>
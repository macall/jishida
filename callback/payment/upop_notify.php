<?php
define("FILE_PATH","/callback/payment"); //文件目录
require_once '../../system/system_init.php';
require_once APP_ROOT_PATH.'app/Lib/MainApp.class.php';

global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = "notify";
$pay_req['class_name'] = "Upop";

//实例化一个网站应用实例
$AppWeb = new MainApp();
?>
<?php

class get_password_find_code 
{
    public function index() {
        $mobile = strim($GLOBALS['request']['mobile']); 

        if (app_conf("SMS_ON") == 0) {
            $root['status'] = 0;
            $root['info'] = '短信功能关闭';
            output($root);
        }

        if ($mobile == '') {
            $root['status'] = 0;
            $root['info'] = '手机号码不能为空';
            output($root);
        }

        if (!check_mobile($mobile)) {
            $root['status'] = 0;
            $root['info'] = "请输入正确的手机号码";
            output($root);
        }

        if (!check_ipop_limit(CLIENT_IP, "register_verify_phone", 60, 0)) {
            $root['status'] = 0;
            $root['info'] = '发送太快了';
            output($root);
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "user WHERE mobile = " . $mobile;
        $user = $GLOBALS['db']->getRow($sql);

        if (empty($user)) {
            $root['status'] = 0;
            $root['info'] = "手机号未在本站注册过";
            output($root);
        }

        //删除超过5分钟的验证码
        $sql = "DELETE FROM " . DB_PREFIX . "sms_mobile_verify WHERE mobile_phone = '$mobile' and add_time <=" . (get_gmtime() - 300);
        $GLOBALS['db']->query($sql);

        $code = rand(100000, 999999);
        $message = "您正在找回密码,验证码：" . $code . ",如非本人操作,请忽略本短信【" . app_conf("SHOP_TITLE") . "】";
        require_once APP_ROOT_PATH . "system/utils/es_sms.php";
        $sms = new sms_sender();
        $send = $sms->sendSms($mobile, $message);
        if ($send['status']) {
            $add_time = get_gmtime();
            $GLOBALS['db']->query("insert into " . DB_PREFIX . "sms_mobile_verify(mobile_phone,code,add_time,send_count,ip) values('$mobile','$code','$add_time',1," . "'" . CLIENT_IP . "')");
            /* 插入一条发送成功记录到队列表中 */
            $msg_data['dest'] = $mobile;
            $msg_data['send_type'] = 0;
            $msg_data['content'] = addslashes($message);
            $msg_data['send_time'] = $add_time;
            $msg_data['is_send'] = 1;
            $msg_data['is_success'] = 1;
            $msg_data['create_time'] = $add_time;
            $msg_data['user_id'] = intval($user['id']);
            $msg_data['title'] = "密码找回验证";
            $GLOBALS['db']->autoExecute(DB_PREFIX . "deal_msg_list", $msg_data);
            $root['info'] = "验证码发出,请注意查收";
            $root['status'] = 1;
        } else {
            $root['info'] = "发送失败" . $send['msg'];
            $root['status'] = 0;
        }

        output($root);
    }

}

?>
<?php

class get_password_verify 
{
    public function index() {
        $mobile=trim($GLOBALS['request']['mobile']);
        $code = strim($GLOBALS['request']['code']);/*验证码*/

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

        if ($code == '') {
            $root['info'] = "请输入验证码!";
            $root['status'] = 0;
            output($root);
        }
        $db_code = $GLOBALS['db']->getRow("select id,code,add_time from " . DB_PREFIX . "sms_mobile_verify where mobile_phone = '$mobile' order by id desc");
        if ($db_code['code'] != $code) {
            $root['info'] = "请输入正确的验证码!";
            $root['status'] = 0;
            output($root);
        }
        $new_time = get_gmtime();
        if (($new_time - $db_code['add_time']) > 60 * 30)/* 30分钟失效 */ {
            $root['info'] = "验证码已失效,请重新获取!";
            $root['status'] = 0;
            $GLOBALS['db']->query("delete from " . DB_PREFIX . "sms_mobile_verify  where mobile_phone = " . $mobile . "");
            output($root);
        }
        
        $GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where id=".$db_code['id']."");
        
        $sql = "SELECT * FROM " . DB_PREFIX . "user WHERE mobile = " . $mobile;
        $user = $GLOBALS['db']->getRow($sql);
        $res = $GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify=".$code." where id=".$user['id']);
        
        if($res){
            $root['info'] = "验证成功";
            $root['status'] = 1;
            $root['user_id'] = $user['id'];
        }else{
            $root['info'] = "验证出错";
            $root['status'] = 0;
        }

        output($root);
    }

}

?>
<?php

class get_password_modify 
{
    public function index() {
        $password=trim($GLOBALS['request']['password']);
        $rep_password=trim($GLOBALS['request']['rep_password']);
        $user_id=trim($GLOBALS['request']['user_id']);
        
        if ($password == '') {
            $root['status'] = 0;
            $root['info'] = '密码不能为空';
            output($root);
        }
        
        if ($password != $rep_password) {
            $root['status'] = 0;
            $root['info'] = '重复密码错误';
            output($root);
        }
        
        $password = md5($password);
        
        $res = $GLOBALS['db']->query("update ".DB_PREFIX."user set user_pwd='".$password."', password_verify='' , update_time=".  time()." where id=".$user_id);
        
        if($res){
            $root['info'] = "重置密码成功";
            $root['status'] = 1;
        }else{
            $root['info'] = "重置密码失败";
            $root['status'] = 0;
        }

        output($root);
    }

}

?>
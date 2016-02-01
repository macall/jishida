<?php

class get_password_resetting
{
    public function index() 
    {
        $root['page_title'] = '重置密码';
        $user_id=trim($GLOBALS['request']['user_id']);
        $code = strim($GLOBALS['request']['code']);

        $user_info  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
        
        $root['get_password_resetting_no_user'] = 0;
        if(empty($user_info)){
            $root['get_password_resetting_no_user'] = 1;
        }
        
        $root['get_password_resetting_wrong_code'] = 0;    
        if($user_info['password_verify'] == '' || $user_info['password_verify'] != $code )
        {
            $root['get_password_resetting_wrong_code'] = 1;    
        }
        
        $root['user_id'] = $user_id;
        output($root);
    }

}

?>
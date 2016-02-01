<?php

class get_password 
{
    public function index() 
    {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '找回密码';

        $root['return'] = 1;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
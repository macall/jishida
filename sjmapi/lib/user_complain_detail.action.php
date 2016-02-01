<?php

class user_complain_detail 
{
    public function index() {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '投诉详情';
        $root['mana_page_title'] = '投诉详情';

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);


        $root['return'] = 1;
        if ($user_id > 0) {
            $complain_id = $GLOBALS['request']['complain_id'];

            $root['user_login_status'] = 1;
            $sql = "SELECT 
                        u.`id` AS user_id,
                        u.`user_name`,
                        u.`mobile` AS user_mobile,
                        u.`province_id` AS user_province_id,
                        u.`city_id` AS user_city_id,
                        u.`addr_detail` AS user_addr_detail,
                        doc.`content` AS complain_content,
                        doc.`create_time` AS complain_time,
                        tech.`id` AS tech_id,
                        tech.`user_name` AS tech_name,
                        do.`id` AS order_id,
                        do.`order_sn` AS order_sn
                      FROM
                        " . DB_PREFIX . "deal_order_complain doc 
                        LEFT JOIN " . DB_PREFIX . "deal_order DO 
                          ON doc.`order_id` = do.`id`
                        LEFT JOIN " . DB_PREFIX . "user u 
                          ON doc.`user_id` = u.`id` 
                        LEFT JOIN " . DB_PREFIX . "user tech 
                          ON doc.`tech_id` = tech.`id`
                      WHERE doc.`id`=" . $complain_id;

            $complain = $GLOBALS['db']->getRow($sql);

            //地址
            $p = $GLOBALS['db']->getRow("select name from " . DB_PREFIX . "region_conf where id = " . $complain['user_province_id']);
            $c = $GLOBALS['db']->getRow("select name from " . DB_PREFIX . "region_conf where id = " . $complain['user_city_id']);
            $complain['addr'] = $p['name'] . '-' . $c['name'] . '-' . $complain['user_addr_detail'];
            
            $complain['complain_time'] = date('Y-m-d H:i',$complain['complain_time']);
            
            $root['complain'] = $complain;
        } else {
            $root['user_login_status'] = 0;
        }

        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
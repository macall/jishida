<?php

class user_refund_list 
{
    public function index() 
    {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '退款列表';

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']); //经理id

        $root['return'] = 1;
        if ($user_id > 0) {
            $root['user_login_status'] = 1;

            //配置分页参数
            require_once APP_ROOT_PATH . "wap/lib/page.php";
            $page_size = 10;
            $page = intval($GLOBALS['request']['page']);
            if ($page == 0)
                $page = 1;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            $sql = "SELECT 
                        d.`sub_name` AS service_name,
                        d.`icon` AS service_icon,
                        d.`id` AS deal_id,
                        do.`order_sn`,
                        do.`create_time`,
                        do.`refund_status`,
                        do.`id` AS order_id 
                      FROM
                        " . DB_PREFIX . "deal_order DO 
                        LEFT JOIN " . DB_PREFIX . "deal_order_item doi 
                          ON do.`id` = doi.`order_id` 
                        LEFT JOIN " . DB_PREFIX . "deal d 
                          ON doi.`deal_id` = d.`id` 
                      WHERE do.`pay_status` = 2 
                        AND do.`user_id` = ".$user_id." 
                      GROUP BY do.`id` 
                      ORDER BY do.`create_time` limit " . $limit;

            $sql_count = "SELECT 
                        count(*) 
                      FROM
                        " . DB_PREFIX . "deal_order DO 
                        LEFT JOIN " . DB_PREFIX . "deal_order_item doi 
                          ON do.`id` = doi.`order_id` 
                        LEFT JOIN " . DB_PREFIX . "deal d 
                          ON doi.`deal_id` = d.`id` 
                      WHERE do.`pay_status` = 2 
                        AND do.`user_id` = ".$user_id." 
                      GROUP BY do.`id` ";

            $refund_list = $GLOBALS['db']->getAll($sql);

            foreach ($refund_list as $key => $value) {
                $value['service_icon'] = get_abs_img_root(get_spec_image($value['service_icon'], 360, 288, 0));
                $value['create_time'] = date('Y-m-d H:i:s', $value['create_time']);

                $refund_list[$key] = $value;
            }

            //配置分页
            $count = $GLOBALS['db']->getOne($sql_count);
            $page_total = ceil($count / $page_size);
            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size);

            $root['refund_list'] = $refund_list;
        } else {
            $root['user_login_status'] = 0;
        }

        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
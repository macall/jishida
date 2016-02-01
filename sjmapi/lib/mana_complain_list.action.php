<?php

class mana_complain_list 
{
    public function index() 
    {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '投诉列表';
        $root['mana_page_title'] = '投诉列表';

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
                        doc.`id` AS complain_id,
                        doi.`number`,
                        doc.`create_time`,
                        d.`id` AS deal_id,
                        d.`current_price` AS price,
                        d.`sub_name` AS service_name,
                        d.`img` AS service_icon,
                        do.`create_time` AS order_create_time 
                      FROM
                        " . DB_PREFIX . "deal_order_complain doc 
                        LEFT JOIN " . DB_PREFIX . "deal_order DO 
                          ON doc.`order_id` = do.`id` 
                        LEFT JOIN " . DB_PREFIX . "deal_order_item doi 
                          ON do.`id` = doi.`order_id` 
                        LEFT JOIN " . DB_PREFIX . "deal d 
                          ON doi.`deal_id`=d.`id` 
                      WHERE do.`type` = 0 
                        AND doc.`tech_id` IN 
                        (SELECT 
                          id 
                        FROM
                          " . DB_PREFIX . "user fu 
                        WHERE fu.`p_id` = " . $user_id . " 
                          AND fu.`service_type_id` = 2 
                          AND fu.`is_delete` = 0) GROUP BY doc.`id` ORDER BY doc.`create_time` DESC limit " . $limit;

            $sql_count = "SELECT 
                        count(*) 
                      FROM
                        " . DB_PREFIX . "deal_order_complain doc 
                        LEFT JOIN " . DB_PREFIX . "deal_order DO 
                          ON doc.`order_id` = do.`id` 
                        LEFT JOIN " . DB_PREFIX . "deal_order_item doi 
                          ON do.`id` = doi.`order_id` 
                        LEFT JOIN " . DB_PREFIX . "deal d 
                          ON doi.`deal_id`=d.`id` 
                      WHERE do.`type` = 0 
                        AND doc.`tech_id` IN 
                        (SELECT 
                          id 
                        FROM
                          " . DB_PREFIX . "user fu 
                        WHERE fu.`p_id` = " . $user_id . " 
                          AND fu.`service_type_id` = 2 
                          AND fu.`is_delete` = 0) GROUP BY doc.`id` ";

            $complain_list = $GLOBALS['db']->getAll($sql);

            foreach ($complain_list as $key => $value) {
                $value['total_price'] = format_price($value['price'] * $value['number']);
                $value['price'] = format_price($value['price']);
                $value['service_icon'] = get_abs_img_root(get_spec_image($value['service_icon'], 360, 288, 0));
                $value['order_create_time'] = date('Y-m-d H:i', $value['order_create_time']);
                $value['complain_time'] = date('Y-m-d H:i', $value['complain_time']);

                $complain_list[$key] = $value;
            }

            //配置分页
            $count = $GLOBALS['db']->getOne($sql_count);
            $page_total = ceil($count / $page_size);
            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size);

            $root['complain_list'] = $complain_list;
        } else {
            $root['user_login_status'] = 0;
        }

        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
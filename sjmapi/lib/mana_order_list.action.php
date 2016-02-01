<?php

class mana_order_list 
{
    public function index() 
    {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '订单列表';
        $root['mana_page_title'] = '订单列表';

        //检查用户
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);

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
                    d.`sub_name` AS deal_name,
                    d.`current_price` AS deal_price,
                    d.`id` AS deal_id,
                    d.`icon`,
                    o.`id` AS order_id,
                    o.`total_price`,
                    o.`create_time`,
                    o.`is_get_bonus`,
                    doi.`number` 
                  FROM
                    " . DB_PREFIX . "deal_order o 
                    LEFT JOIN " . DB_PREFIX . "deal d 
                      ON o.`deal_ids` = d.`id` 
                    LEFT JOIN " . DB_PREFIX . "deal_order_item doi 
                      ON o.`id` = doi.`order_id` 
                  WHERE TYPE = 0 
                    AND technician_id IN 
                    (SELECT 
                      id 
                    FROM
                      " . DB_PREFIX . "user 
                    WHERE p_id = " . $user_id . " 
                      AND service_type_id = 2 
                      AND is_delete = 0 
                      AND is_effect = 1) ORDER BY o.`create_time` DESC limit " . $limit;

            $sql_count = "SELECT count(o.`id`) 
                  FROM
                    " . DB_PREFIX . "deal_order o 
                    LEFT JOIN " . DB_PREFIX . "deal d 
                      ON o.`deal_ids` = d.`id` 
                    LEFT JOIN " . DB_PREFIX . "deal_order_item doi 
                      ON o.`id` = doi.`order_id` 
                  WHERE TYPE = 0 
                    AND technician_id IN 
                    (SELECT 
                      id 
                    FROM
                      " . DB_PREFIX . "user 
                    WHERE p_id = " . $user_id . " 
                      AND service_type_id = 2 
                      AND is_delete = 0 
                      AND is_effect = 1)";

            $order_list = $GLOBALS['db']->getAll($sql);

            foreach ($order_list as $key => $value) {
                $value['mana_fee'] = format_price($value['total_price'] * $user['manager_commission_fee_percent'] * 0.01);
                $value['deal_price'] = format_price($value['deal_price']);
                $value['total_price'] = format_price($value['total_price']);
                $value['create_time'] = date('Y-m-d H:i', $value['create_time']);
                $value['icon'] = get_abs_img_root(get_spec_image($value['icon'], 360, 288, 0));
                
                $order_list[$key] = $value;
            }

            //配置分页
            $count = $GLOBALS['db']->getOne($sql_count);
            $page_total = ceil($count / $page_size);
            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size);

            $root['order_list'] = $order_list;
        } else {
            $root['user_login_status'] = 0;
        }

        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
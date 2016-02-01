<?php

class mana_tech_list 
{
    public function index() 
    {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '名下技师';

        //检查用户,用户密码
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

            $sql = 'SELECT 
                        * 
                      FROM
                        ' . DB_PREFIX . 'user 
                      WHERE is_effect = 1 
                        AND is_delete = 0 
                        AND service_type_id = 2
                        AND p_id =' . $user_id . ' limit ' . $limit;
            $sql_count = 'SELECT 
                        count(*) 
                      FROM
                        ' . DB_PREFIX . 'user 
                      WHERE is_effect = 1 
                        AND is_delete = 0 
                        AND service_type_id = 2
                        AND p_id =' . $user_id;

            $tech_list = $GLOBALS['db']->getAll($sql);

            foreach ($tech_list as $key => $value) {
                $p = $GLOBALS['db']->getRow("select name from " . DB_PREFIX . "region_conf where id = " . $value['province_id']);
                $c = $GLOBALS['db']->getRow("select name from " . DB_PREFIX . "region_conf where id = " . $value['city_id']);

                for ($i = 0; $i < $value['service_level_id']; $i++) {
                    $value['tech_level'][] = $i;
                }
                $value['addr'] = $p['name'] . '-' . $c['name'] . '-' . $value['addr_detail'];

                $value['user_avatar'] = get_abs_img_root(get_muser_avatar($value['id'], "big"));
                $tech_list[$key] = $value;
            }

            //配置分页
            $count = $GLOBALS['db']->getOne($sql_count);
            $page_total = ceil($count / $page_size);
            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size);

            $root['tech_list'] = $tech_list;
        } else {
            $root['user_login_status'] = 0;
        }

        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
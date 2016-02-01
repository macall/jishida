<?php

class tech_list {
 
    public function index() {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '预约技师';


        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);


        $root['return'] = 1;
        if ($user_id > 0) {
            $root['user_login_status'] = 1;
            $sql = 'SELECT * FROM ' . DB_PREFIX . 'user WHERE is_effect = 1 AND is_delete = 0 AND service_type_id = 2';

            $tech_list = $GLOBALS['db']->getAll($sql);
            
            foreach ($tech_list as $key => $value) {
                $p = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$value['province_id']);
                $c = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$value['city_id']);
                
                for ($i=0;$i<$value['service_level_id'];$i++){
                    $value['tech_level'][] = $i;
                }
                $value['addr'] = $p['name'].'-'.$c['name'].'-'.$value['addr_detail'];
                
                $value['user_avatar'] = get_abs_img_root(get_muser_avatar($value['id'],"big"));
                $tech_list[$key] = $value;
            }

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
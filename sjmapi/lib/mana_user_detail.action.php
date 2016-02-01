<?php

class mana_user_detail {
 
    public function index() {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '技师详情';
        $root['mana_page_title'] = '客户资料';

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);


        $root['return'] = 1;
        if ($user_id > 0) {
            $m_user_id = $GLOBALS['request']['m_user_id'];
            
            $root['user_login_status'] = 1;
            $sql = 'SELECT 
                        * 
                      FROM
                        ' . DB_PREFIX . 'user 
                      WHERE id =' . $m_user_id;

            $m_user = $GLOBALS['db']->getRow($sql);
            
            //地址
            $p = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$m_user['province_id']);
            $c = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$m_user['city_id']);
            $m_user['addr'] = $p['name'].'-'.$c['name'].'-'.$m_user['addr_detail'];
            
            //星级
            for ($i=0;$i<$m_user['service_level_id'];$i++){
                $m_user['tech_level'][] = $i;
            }
            
            //性别
            if($m_user['sex'] == 1){
                $m_user['sex'] = '男';
            }elseif($m_user['sex'] == 0){
                $m_user['sex'] = '女';
            }else{
                $m_user['sex'] = '保密';
            }
            
            //头像
            $m_user['user_avatar'] = get_abs_img_root(get_muser_avatar($m_user_id,"big"));
            $root['m_user'] = $m_user;
            
        } else {
            $root['user_login_status'] = 0;
        }
        
        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
<?php

class mana_tech_detail {
 
    public function index() {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '技师详情';
        $root['mana_page_title'] = '技师详情';

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);


        $root['return'] = 1;
        if ($user_id > 0) {
            $tech_id = $GLOBALS['request']['tech_id'];
            
            $root['user_login_status'] = 1;
            $sql = 'SELECT 
                        * 
                      FROM
                        ' . DB_PREFIX . 'user 
                      WHERE id =' . $tech_id;

            $tech = $GLOBALS['db']->getRow($sql);
            
            $date = date('Y-m-d H:i:s');
            $timestamp = strtotime($date);
            
            //上个月订单
            $last_month_firstday = strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1) . '-01');
            $f_date = date('Y-m-01', $last_month_firstday);
            $last_month_lastday = strtotime("$f_date +1 month -1 day");
            $last_month_order_sql = "SELECT count(*) as count FROM " . 
                                        DB_PREFIX . "deal_order WHERE create_time > " . 
                                        $last_month_firstday . " AND create_time <  " . 
                                        $last_month_lastday . " AND technician_id=" . $tech_id;
            $last_month_order_count = $GLOBALS['db']->getRow($last_month_order_sql);
            $root['last_month_order_count'] = $last_month_order_count['count'];
            //本月订单
            $firstday = strtotime(date("Y-m-01",$timestamp));
            $fi_date = date('Y-m-01',$firstday);
            $lastday = strtotime("$fi_date +1 month -1 day");
            $month_order_sql = "SELECT count(*) as count FROM " . 
                                        DB_PREFIX . "deal_order WHERE create_time > " . 
                                        $firstday . " AND create_time <  " . 
                                        $lastday . " AND technician_id=" . $tech_id;
            $month_order_count = $GLOBALS['db']->getRow($month_order_sql);
            $root['month_order_count'] = $month_order_count['count'];
            //总订单数
            $order_sql = "SELECT count(*) as count FROM " . 
                                        DB_PREFIX . "deal_order WHERE technician_id=" . $tech_id;
            $order_count = $GLOBALS['db']->getRow($order_sql);
            $root['order_count'] = $order_count['count'];
            
            
            //地址
            $p = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$tech['province_id']);
            $c = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$tech['city_id']);
            $tech['addr'] = $p['name'].'-'.$c['name'].'-'.$tech['addr_detail'];
            
            //星级
            for ($i=0;$i<$tech['service_level_id'];$i++){
                $tech['tech_level'][] = $i;
            }
            
            //性别
            if($tech['sex'] == 1){
                $tech['sex'] = '男';
            }elseif($tech['sex'] == 0){
                $tech['sex'] = '女';
            }else{
                $tech['sex'] = '保密';
            }
            
            //头像
            $tech['user_avatar'] = get_abs_img_root(get_muser_avatar($tech_id,"big"));
            $root['tech'] = $tech;
            
        } else {
            $root['user_login_status'] = 0;
        }
        
        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
<?php

class mana_order_detail {
 
    public function index() {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '订单详情';
        $root['mana_page_title'] = '订单详情';


        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);


        $root['return'] = 1;
        if ($user_id > 0) {
            $order_id = $GLOBALS['request']['order_id'];
            
            $root['user_login_status'] = 1;
            
            $sql = "select do.* from " . DB_PREFIX . "deal_order as do where do.is_delete = 0 and " .
                    " do.id = " . $order_id . " and do.type = 0";
            $order = $GLOBALS['db']->getRow($sql);
            $order['endtime'] = $order['service_time']*60;
            $order['starttime'] = time()-$order['service_start_time'];
            
            if($order['starttime']>$order['endtime']){
                $order['starttime'] = $order['endtime']-1;
            }
            
            if($order['starttime']<0){
                $order['starttime'] = 0;
            }
            //用户信息
            $c_user = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id = " . $order['user_id']);
            $p = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$c_user['province_id']);
            $c = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$c_user['city_id']);
            $c_user['addr'] = $p['name'].'-'.$c['name'].'-'.$c_user['addr_detail'];
            $order['c_user'] = $c_user;
            //服务项目
            $deals = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "deal WHERE id IN(" . $order['deal_ids'] . ") ORDER BY id DESC");
            foreach ($deals as $key => $value) {
                $comment = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX
                        . "supplier_location_dp  WHERE user_id = " . $order['user_id']
                        . "  AND deal_id = " . $value['id'] . " ORDER BY create_time DESC LIMIT 1");

                if (!empty($comment)) {
                    $order['user_comment'] = $comment['content'];
                    //星级
                    for ($i = 0; $i < $comment['point']; $i++) {
                        $order['points'][] = $i;
                    }
                    break;
                }
            }
            $order['deals'] = $deals;
            
            $root['order'] = $order;
            
            //-------------------------------------------------------技师资料-------------------------------------------------------------------
            $tech_sql = 'SELECT 
                        * 
                      FROM
                        ' . DB_PREFIX . 'user 
                      WHERE id =' . $order['technician_id'];

            $tech = $GLOBALS['db']->getRow($tech_sql);

            $date = date('Y-m-d H:i:s');
            $timestamp = strtotime($date);
            
            //上个月订单
            $last_month_firstday = strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1) . '-01');
            $f_date = date('Y-m-01', $last_month_firstday);
            $last_month_lastday = strtotime("$f_date +1 month -1 day");
            $last_month_order_sql = "SELECT count(*) as count FROM " . 
                                        DB_PREFIX . "deal_order WHERE create_time > " . 
                                        $last_month_firstday . " AND create_time <  " . 
                                        $last_month_lastday . " AND technician_id=" . $order['technician_id'];
            $last_month_order_count = $GLOBALS['db']->getRow($last_month_order_sql);
            $root['last_month_order_count'] = $last_month_order_count['count'];
            //本月订单
            $firstday = strtotime(date("Y-m-01",$timestamp));
            $fi_date = date('Y-m-01',$firstday);
            $lastday = strtotime("$fi_date +1 month -1 day");
            $month_order_sql = "SELECT count(*) as count FROM " . 
                                        DB_PREFIX . "deal_order WHERE create_time > " . 
                                        $firstday . " AND create_time <  " . 
                                        $lastday . " AND technician_id=" . $order['technician_id'];
            $month_order_count = $GLOBALS['db']->getRow($month_order_sql);
            $root['month_order_count'] = $month_order_count['count'];
            //总订单数
            $order_sql = "SELECT count(*) as count FROM " . 
                                        DB_PREFIX . "deal_order WHERE technician_id=" . $order['technician_id'];
            $order_count = $GLOBALS['db']->getRow($order_sql);
            $root['order_count'] = $order_count['count'];
            
            
            //地址
            $tech_p = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$tech['province_id']);
            $tech_c = $GLOBALS['db']->getRow("select name from ".DB_PREFIX."region_conf where id = ".$tech['city_id']);
            $tech['addr'] = $tech_p['name'].'-'.$tech_c['name'].'-'.$tech['addr_detail'];
            
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
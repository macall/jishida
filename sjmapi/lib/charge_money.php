<?php

class charge_money {

    public function index() {
        $root = array();

//        $user_data = $GLOBALS['user_info'];
//        $user_id = intval($user_data['id']);
//        if ($user_id == 0) {
//            $root['user_login_status'] = 0;
//            $root['info'] = "请先登陆";
//        } else {
//            $money = $GLOBALS['db']->getOne("select money from " . DB_PREFIX . "user where id = " . $user_id);
//            
//            $incharge_list = $GLOBALS['db']->getAll("SELECT pay_amount,create_time FROM " . DB_PREFIX . "deal_order WHERE TYPE=1 AND user_id = " . $user_id);
//            $root['money'] = $money;
//            foreach ($incharge_list as $key => $value) {
//                $value['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
//                $incharge_list[$key] = $value;
//            }
//            $root['incharge_list'] = $incharge_list;
//        }

        $root['page_title'] = '账户充值';
        output($root);
    }

}

?>
<?php

class incharge_done {

    public function index() {
        $payment_id = intval($GLOBALS['request']['payment']);
        $money = floatval($GLOBALS['request']['money']);
        
        if ($money <= 0) {
            $root['status'] = 2;
            $root['info'] = $GLOBALS['lang']['PLEASE_INPUT_CORRECT_INCHARGE'];
            output($root);
        }

        $payment_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment where id = " . $payment_id);
        if (!$payment_info) {
            $root['status'] = 2;
            $root['info'] = $GLOBALS['lang']['PLEASE_SELECT_PAYMENT'];
            output($root);
        }

        if ($payment_info['fee_type'] == 0) { //定额
            $payment_fee = $payment_info['fee_amount'];
        } else { //比率
            $payment_fee = $money * $payment_info['fee_amount'];
        }

        //开始生成订单
        $now = NOW_TIME;
        $order['type'] = 1; //充值单
        $order['user_id'] = $GLOBALS['user_info']['id'];
        $order['create_time'] = $now;
        $order['total_price'] = $money + $payment_fee;
        $order['deal_total_price'] = $money;
        $order['pay_amount'] = 0;
        $order['pay_status'] = 0;
        $order['delivery_status'] = 5;
        $order['order_status'] = 0;
        $order['payment_id'] = $payment_id;
        $order['payment_fee'] = $payment_fee;
//        $order['bank_id'] = strim($_REQUEST['bank_id']);


        do {
            $order['order_sn'] = to_date(get_gmtime(), "Ymdhis") . rand(100, 999);
            $GLOBALS['db']->autoExecute(DB_PREFIX . "deal_order", $order, 'INSERT', '', 'SILENT');
            $order_id = intval($GLOBALS['db']->insert_id());
        } while ($order_id == 0);

        require_once APP_ROOT_PATH . "system/model/cart.php";
        $payment_notice_id = make_payment_notice($order['total_price'], $order_id, $payment_info['id']);
        //创建支付接口的付款单
        
        if($payment_notice_id){
            $root['order_id'] = $order_id;
            $root['info'] = 1;
            
        }
        
//        print_r($root);exit;
        output($root);
//        $rs = order_paid($order_id);
//        if ($rs) {
//            app_redirect(url("index", "payment#incharge_done", array("id" => $order_id))); //充值支付成功
//        } else {
//            app_redirect(url("index", "payment#pay", array("id" => $payment_notice_id)));
//        }
    }

}

?>
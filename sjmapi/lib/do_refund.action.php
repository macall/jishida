<?php

class do_refund {

    public function index() {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();


        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);


        $root['return'] = 1;
        if ($user_id > 0) {

            $did = intval($GLOBALS['request']['did']);
            $cid = intval($GLOBALS['request']['cid']);
            $content = strim($GLOBALS['request']['content']);
            if (empty($content)) {
                $root['status'] = 0;
                $root['info'] = "请填写退款原因";
                output($root);
            }
            if ($did) {
                //退单
                $deal_order_item = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $did);
                $order_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order where id = '" . $deal_order_item['order_id'] . "' and order_status = 0 and user_id = " . $user_id);
                if ($order_info) {
                    if ($deal_order_item['delivery_status'] == 0 && $order_info['pay_status'] == 2 && $deal_order_item['is_refund'] == 1) {
                        if ($deal_order_item['refund_status'] == 0) {
                            //执行退单,标记：deal_order_item表与deal_order表，
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set refund_status = 1 where id = " . $deal_order_item['id']);
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set refund_status = 1 where id = " . $deal_order_item['order_id']);

                            $msg = array();
                            $msg['rel_table'] = "deal_order";
                            $msg['rel_id'] = $deal_order_item['order_id'];
                            $msg['title'] = "退款申请";
                            $msg['content'] = "退款申请：" . $content;
                            $msg['create_time'] = NOW_TIME;
                            $msg['user_id'] = $user_id;
                            $GLOBALS['db']->autoExecute(DB_PREFIX . "message", $msg);

                            update_order_cache($deal_order_item['order_id']);

                            order_log($deal_order_item['sub_name'] . "申请退款，等待审核", $deal_order_item['order_id']);

                            require_once APP_ROOT_PATH . "system/model/deal_order.php";
                            distribute_order($order_info['id']);

                            $root['status'] = 1;
                            $root['info'] = "退款申请已提交，请等待审核";
                            output($root);
                        } else {
                            $root['status'] = 0;
                            $root['info'] = "不允许退款";
                            output($root);
                        }
                    } else {
                        $root['status'] = 0;
                        $root['info'] = "非法操作";
                        output($root);
                    }
                } else {
                    $root['status'] = 0;
                    $root['info'] = "非法操作";
                    output($root);
                }
            } elseif ($cid) {
                //退券
                $coupon = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_coupon where user_id = " . $user_id . " and id = " . $cid);
                if ($coupon) {
                    if ($coupon['refund_status'] == 0 && $coupon['confirm_time'] == 0) {//从未退过款可以退款，且未使用过
                        if ($coupon['any_refund'] == 1 || ($coupon['expire_refund'] == 1 && $coupon['end_time'] > 0 && $coupon['end_time'] < NOW_TIME)) {//随时退或过期退已过期
                            //执行退券
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_coupon set refund_status = 1 where id = " . $coupon['id']);
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order_item set refund_status = 1 where id = " . $coupon['order_deal_id']);
                            $GLOBALS['db']->query("update " . DB_PREFIX . "deal_order set refund_status = 1 where id = " . $coupon['order_id']);

                            $deal_order_item = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where id = " . $coupon['order_deal_id']);

                            $msg = array();
                            $msg['rel_table'] = "deal_order";
                            $msg['rel_id'] = $coupon['order_id'];
                            $msg['title'] = "退款申请";
                            $msg['content'] = $content;
                            $msg['create_time'] = NOW_TIME;
                            $msg['user_id'] = $user_id;
                            $GLOBALS['db']->autoExecute(DB_PREFIX . "message", $msg);
                            update_order_cache($coupon['order_id']);

                            order_log($deal_order_item['sub_name'] . "申请退一张团购券，等待审核", $coupon['order_id']);

                            require_once APP_ROOT_PATH . "system/model/deal_order.php";
                            distribute_order($coupon['order_id']);
                            $root['status'] = 1;
                            $root['info'] = "退款申请已提交，请等待审核";
                            output($root);
                        } else {
                            $root['status'] = 0;
                            $root['info'] = "不允许退款";
                            output($root);
                        }
                    } else {
                        $root['status'] = 0;
                        $root['info'] = "非法操作";
                        output($root);
                    }
                } else {
                    $root['status'] = 0;
                    $root['info'] = "非法操作";
                    output($root);
                }
            } else {
                $root['status'] = 0;
                $root['info'] = "非法操作";
                output($root);
            }
        } else {
            $root['user_login_status'] = 0;
        }

        output($root);
    }

}

?>
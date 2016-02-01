<?php

class mana_tech_order_list 
{
    public function index() 
    {
        $city_name = strim($GLOBALS['request']['city_name']); //城市名称
        $root = array();

        $root['page_title'] = '技师订单';
        $root['mana_page_title'] = '技师订单';

        //检查用户,用户密码
        $user = $GLOBALS['user_info'];
        $user_id = intval($user['id']);

        $root['return'] = 1;
        if ($user_id > 0) {
            $tech_id = $GLOBALS['request']['tech_id'];
            $tech = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX . "user WHERE id =" . $tech_id);

            $root['tech_name'] = $tech['user_name'];
            $root['user_login_status'] = 1;

            //配置分页参数
            require_once APP_ROOT_PATH . "wap/lib/page.php";
            $page_size = 10;
            $page = intval($GLOBALS['request']['page']);
            if ($page == 0)
                $page = 1;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            $sql = "select do.* from " . DB_PREFIX . "deal_order as do where do.is_delete = 0 and " .
                    " do.technician_id = " . $tech_id . " and do.type = 0 order by do.create_time desc limit " . $limit;
            $sql_count = "select count(*) from " . DB_PREFIX . "deal_order as do where do.is_delete = 0 and " .
                    " do.technician_id = " . $tech_id . " and do.type = 0 ";

            $list = $GLOBALS['db']->getAll($sql);
            foreach ($list as $k => $v) {
                $c_user = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id = " . $v['user_id']);
                $list[$k]['user_id'] = $v['user_id'];
                $list[$k]['user_name'] = $c_user['user_name'];
                $list[$k]['user_mobile'] = $c_user['mobile'];

                //服务项目
                $deals = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "deal WHERE id IN(" . $v['deal_ids'] . ") ORDER BY id DESC");
                $deal_order_item_for_dp = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $v['id'] . ' limit 1');
                foreach ($deals as $key => $value) {
                    $comment = $GLOBALS['db']->getRow("SELECT * FROM " . DB_PREFIX
                            . "supplier_location_dp  WHERE id = " . $deal_order_item_for_dp['dp_id'] . " ORDER BY create_time DESC LIMIT 1");

                    if (!empty($comment)) {
                        $list[$k]['user_comment'] = $comment['content'];
                        //星级
                        for ($i = 0; $i < $comment['point']; $i++) {
                            $list[$k]['points'][] = $i;
                        }
                        break;
                    }
                }
                $list[$k]['deals'] = $deals;

                $list[$k]['create_time'] = to_date($v['create_time']);
                $list[$k]['pay_amount'] = format_price($v['pay_amount']);
                $list[$k]['total_price'] = format_price($v['total_price']);
                if ($v['deal_order_item']) {
                    $list[$k]['deal_order_item'] = unserialize($v['deal_order_item']);
                } else {
                    $order_id = $v['id'];
                    update_order_cache($order_id);
                    $list[$k]['deal_order_item'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "deal_order_item where order_id = " . $order_id);
                }
                $list[$k]['c'] = count($list[$k]['deal_order_item']);
                foreach ($list[$k]['deal_order_item'] as $kk => $vv) {
                    $list[$k]['deal_order_item'][$kk]['total_price'] = format_price($vv['total_price']);
                    $deal_info = load_auto_cache("deal", array("id" => $vv['deal_id']));
                    $list[$k]['deal_order_item'][$kk]['url'] = $deal_info['url'];
                }
            }

            $list[$k]['complain'] = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_order_complain where order_id= " . $v['id'] . " limit 1");

            //配置分页
            $count = $GLOBALS['db']->getOne($sql_count);
            $page_total = ceil($count / $page_size);
            $root['page'] = array("page" => $page, "page_total" => $page_total, "page_size" => $page_size);

            $root['list'] = $list;
        } else {
            $root['user_login_status'] = 0;
        }

        $root['user'] = $user;
        $root['city_name'] = $city_name;
        output($root);
    }

}

?>
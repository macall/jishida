<?php

class review {

    public function index() {
//        global_run();
//        init_app_page();
//        $GLOBALS['tmpl']->assign("no_nav", true); //无分类下拉
//        if (empty($GLOBALS['user_info'])) {
//            app_redirect(url("index", "user#login"));
//        }

        require_once APP_ROOT_PATH . "system/model/review.php";

        $order_item_id = intval($_REQUEST['order_item_id']);  //订单商品ID
//        $youhui_log_id = intval($_REQUEST['youhui_log_id']);  //优惠券领取日志ID
//        $event_submit_id = intval($_REQUEST['event_submit_id']); //活动报名日志ID

        if ($order_item_id > 0) {
            $deal_id = intval($GLOBALS['db']->getOne("select deal_id from " . DB_PREFIX . "deal_order_item where id = " . $order_item_id));
        } else {
            $deal_id = intval($_REQUEST['deal_id']);
        }

//        if ($youhui_log_id > 0) {
//            $youhui_id = intval($GLOBALS['db']->getOne("select youhui_id from " . DB_PREFIX . "youhui_log where id = " . $youhui_log_id));
//        } else {
//            $youhui_id = intval($_REQUEST['youhui_id']);
//        }
//
//        if ($event_submit_id > 0) {
//            $event_id = intval($GLOBALS['db']->getOne("select event_id from " . DB_PREFIX . "event_submit where id = " . $event_submit_id));
//        } else {
//            $event_id = intval($_REQUEST['event_id']);
//        }
//
//        $location_id = intval($_REQUEST['location_id']);

        if ($deal_id > 0) {
            require_once APP_ROOT_PATH . "system/model/deal.php";
            $deal_info = get_deal($deal_id);
            if ($deal_info) {
                //验证是否可以点评
                $checker = check_dp_status($GLOBALS['user_info']['id'], array("deal_id" => $deal_id, "order_item_id" => $order_item_id));
                if (!$checker['status']) {
                    $root['info']=$checker['info'];
                    $root['status']=0;
                }


                $dp_data = load_dp_info(array("deal_id" => $deal_id));
                if ($deal_info['is_shop'] == 1)
                    $dp_cfg = load_dp_cfg(array("scate_id" => $deal_info['shop_cate_id']));
                else
                    $dp_cfg = load_dp_cfg(array("cate_id" => $deal_info['cate_id']));

                $item_info['id'] = $deal_info['id'];
                $item_info['key'] = 'deal_id';
                $item_info['ex_key'] = 'order_item_id';
                $item_info['ex_id'] = $order_item_id;
                $item_info['name'] = $deal_info['sub_name'];
                $item_info['detail'] = $deal_info['name'];
                $item_info['url'] = $deal_info['url'];
                $item_info['image'] = $goods_item['image']=get_abs_img_root(get_spec_image($deal_info['icon'],160,160,0));

//                $GLOBALS['tmpl']->assign("dp_data", $dp_data);
                $root['dp_data'] = $dp_data;
//                $GLOBALS['tmpl']->assign("dp_cfg", $dp_cfg);
                $root['dp_cfg'] = $dp_cfg;
//                $GLOBALS['tmpl']->assign("item_info", $item_info);
                $root['item_info'] = $item_info;
                //print_r($dp_cfg);
                //输出导航
                $site_nav[] = array('name' => $GLOBALS['lang']['HOME_PAGE'], 'url' => url("index"));
                $site_nav[] = array('name' => $deal_info['sub_name'], 'url' => url("index", "review", array("deal_id" => $deal_info['id'])));
//                $GLOBALS['tmpl']->assign("site_nav", $site_nav);
                $root['site_nav'] = $site_nav;
                
                //输出seo
                $page_title = "";
                $page_keyword = "";
                $page_description = "";
                if ($deal_info['supplier_info']['name']) {
                    $page_title.="[" . $deal_info['supplier_info']['name'] . "]";
                    $page_keyword.=$deal_info['supplier_info']['name'] . ",";
                    $page_description.=$deal_info['supplier_info']['name'] . ",";
                }
                $page_title.= $deal_info['sub_name'];
                $page_keyword.=$deal_info['sub_name'];
                $page_description.=$deal_info['sub_name'];
//                $GLOBALS['tmpl']->assign("page_title", $page_title);
                $root['page_title'] = $page_title;
//                $GLOBALS['tmpl']->assign("page_keyword", $page_keyword);
                $root['page_keyword'] = $page_keyword;
//                $GLOBALS['tmpl']->assign("page_description", $page_description);
                $root['page_description'] = $page_description;

                //输出右侧的其他团购
                if ($deal_info['is_shop'] == 0)
                    $side_deal_list = get_deal_list(5, array(DEAL_ONLINE, DEAL_NOTICE), array("cid" => $deal_info['cate_id'], "city_id" => $GLOBALS['city']['id']), "", "  d.buy_type <> 1 and d.is_shop = 0 and d.id<>" . $deal_info['id']);
                elseif ($deal_info['is_shop'] == 1) {
                    if ($deal_info['buy_type'] == 1)
                        $side_deal_list = get_goods_list(5, array(DEAL_ONLINE, DEAL_NOTICE), array("cid" => $deal_info['shop_cate_id'], "city_id" => $GLOBALS['city']['id']), "", "  d.buy_type = 1 and d.is_shop = 1 and d.id<>" . $deal_info['id']);
                    else
                        $side_deal_list = get_goods_list(5, array(DEAL_ONLINE, DEAL_NOTICE), array("cid" => $deal_info['shop_cate_id'], "city_id" => $GLOBALS['city']['id']), "", "  d.buy_type <> 1 and d.is_shop = 1 and d.id<>" . $deal_info['id']);
                }


                //$side_deal_list = get_deal_list(4,array(DEAL_ONLINE));
//                $GLOBALS['tmpl']->assign("side_deal_list", $side_deal_list['list']);
                $root['side_deal_list'] = $side_deal_list['list'];
            }
            else {
                $root['info']="你要点评的商品不存在";
                $root['status']=0;
            }
        } else {
            app_redirect(url("index"));
        }

        output($root);
//        $GLOBALS['tmpl']->display("review.html");
    }

}

?>
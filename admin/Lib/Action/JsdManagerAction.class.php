<?php

// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class JsdManagerAction extends CommonAction 
{

    public function __construct() 
    {
        parent::__construct();
        require_once APP_ROOT_PATH . "/system/model/user.php";
    }

    public function index()
    {
        //地区列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
        foreach($region_lv2 as $k=>$v)
        {
            if($v['id'] == intval($_REQUEST['province_id']))
            {
                $region_lv2[$k]['selected'] = 1;
                break;
            }
        }
        $region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($_REQUEST['province_id']));  //三级地址
        foreach($region_lv3 as $k=>$v)
        {
            if($v['id'] == intval($_REQUEST['city_id']))
            {
                $region_lv3[$k]['selected'] = 1;
                break;
            }
        }
        $this->assign("region_lv2", $region_lv2);
        $this->assign("region_lv3", $region_lv3);
        
        $group_list = M("ServiceType")->findAll();
        $this->assign("group_list", $group_list);

        //定义条件
        $map[DB_PREFIX . 'user.is_delete'] = 0;
        $map[DB_PREFIX . 'user.service_type_id'] = 3;

        if (intval($_REQUEST['group_id']) > 0) {
            $map[DB_PREFIX . 'user.group_id'] = intval($_REQUEST['group_id']);
        }

        if (strim($_REQUEST['user_name']) != '') {
            $map[DB_PREFIX . 'user.user_name'] = array('eq', strim($_REQUEST['user_name']));
        }
        if (strim($_REQUEST['email']) != '') {
            $map[DB_PREFIX . 'user.email'] = array('eq', strim($_REQUEST['email']));
        }
        if (strim($_REQUEST['mobile']) != '') {
            $map[DB_PREFIX . 'user.mobile'] = array('eq', strim($_REQUEST['mobile']));
        }
        if (strim($_REQUEST['province_id']) != '') {
            $map[DB_PREFIX . 'user.province_id'] = array('eq', strim($_REQUEST['province_id']));
        }
        if (strim($_REQUEST['city_id']) != '') {
            $map[DB_PREFIX . 'user.city_id'] = array('eq', strim($_REQUEST['city_id']));
        }
        if (strim($_REQUEST['pid_name']) != '') {
            $pid = M("User")->where("user_name='" . strim($_REQUEST['pid_name']) . "'")->getField("id");
            $map[DB_PREFIX . 'user.pid'] = $pid;
        }

        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $model = D('User');
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    public function add() 
    {
        //地区列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
        $region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($_REQUEST['province_id']));  //三级地址
        $this->assign("region_lv2", $region_lv2);
        $this->assign("region_lv3", $region_lv3);
        
        $service_level_list = M("ServiceLevel")->findAll();
        $this->assign("service_level_list", $service_level_list);
        
        $this->display();
    }
    
    public function insert() 
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M('User')->create();

        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));

        if (!check_empty($data['user_pwd'])) {
            $this->error(L("USER_PWD_EMPTY_TIP"));
        }
        if ($data['user_pwd'] != $_REQUEST['user_confirm_pwd']) {
            $this->error(L("USER_PWD_CONFIRM_ERROR"));
        }
        $res = save_user($_REQUEST);
        if ($res['status'] == 0) {
            $error_field = $res['data'];
            if ($error_field['error'] == EMPTY_ERROR) {
                if ($error_field['field_name'] == 'user_name') {
                    $this->error(L("USER_NAME_EMPTY_TIP"));
                } elseif ($error_field['field_name'] == 'email') {
                    $this->error(L("USER_EMAIL_EMPTY_TIP"));
                } else {
                    $this->error(sprintf(L("USER_EMPTY_ERROR"), $error_field['field_show_name']));
                }
            }
            if ($error_field['error'] == FORMAT_ERROR) {
                if ($error_field['field_name'] == 'email') {
                    $this->error(L("USER_EMAIL_FORMAT_TIP"));
                }
                if ($error_field['field_name'] == 'mobile') {
                    $this->error(L("USER_MOBILE_FORMAT_TIP"));
                }
            }

            if ($error_field['error'] == EXIST_ERROR) {
                if ($error_field['field_name'] == 'user_name') {
                    $this->error(L("USER_NAME_EXIST_TIP"));
                }
                if ($error_field['field_name'] == 'email') {
                    $this->error(L("USER_EMAIL_EXIST_TIP"));
                }
            }
        }
        $user_id = intval($res['user_id']);
        foreach ($_REQUEST['auth'] as $k => $v) {
            foreach ($v as $item) {
                $auth_data = array();
                $auth_data['m_name'] = $k;
                $auth_data['a_name'] = $item;
                $auth_data['user_id'] = $user_id;
                M("UserAuth")->add($auth_data);
            }
        }


        foreach ($_REQUEST['cate_id'] as $cate_id) {
            $link_data = array();
            $link_data['user_id'] = $user_id;
            $link_data['cate_id'] = $cate_id;
            M("UserCateLink")->add($link_data);
        }

        // 更新数据
        $log_info = $data['user_name'];
        save_log($log_info . L("INSERT_SUCCESS"), 1);
        $this->success(L("INSERT_SUCCESS"));
    }
    public function edit() 
    {
        $id = intval($_REQUEST ['id']);
        $condition['is_delete'] = 0;
        $condition['id'] = $id;
        $vo = M('User')->where($condition)->find();
        $this->assign('vo', $vo);

        //地区列表
        $region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
        foreach($region_lv2 as $k=>$v)
        {
            if($v['id'] == intval($vo['province_id']))
            {
                $region_lv2[$k]['selected'] = 1;
                break;
            }
        }
        $region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($vo['province_id']));  //三级地址
        foreach($region_lv3 as $k=>$v)
        {
            if($v['id'] == intval($vo['city_id']))
            {
                $region_lv3[$k]['selected'] = 1;
                break;
            }
        }
        $this->assign("region_lv2", $region_lv2);
        $this->assign("region_lv3", $region_lv3);
        
//        $group_list = M("UserGroup")->findAll();
//        $this->assign("group_list", $group_list);
//
//        $cate_list = M("TopicTagCate")->findAll();
//        foreach ($cate_list as $k => $v) {
//            $cate_list[$k]['checked'] = M("UserCateLink")->where("user_id=" . $vo['id'] . " and cate_id = " . $v['id'])->count();
//        }
//        $this->assign("cate_list", $cate_list);
//        $field_list = M("UserField")->order("sort desc")->findAll();
//        foreach ($field_list as $k => $v) {
//            $field_list[$k]['value_scope'] = preg_split("/[ ,]/i", $v['value_scope']);
//            $field_list[$k]['value'] = M("UserExtend")->where("user_id=" . $id . " and field_id=" . $v['id'])->getField("value");
//        }
//        $this->assign("field_list", $field_list);

        $service_level_list = M("ServiceLevel")->findAll();
        foreach ($service_level_list as $key => $value) {
            if($value['id'] == $vo['service_level_id']){
                $service_level_list[$key]['selected'] = 1;
            }
        }
        $this->assign('service_level_list', $service_level_list);
        
        $this->assign('service_level_list', $service_level_list);
        $service_type_list = M("ServiceType")->findAll();
        foreach ($service_type_list as $key => $value) {
            if($value['id'] == $vo['service_type_id']){
                $service_type_list[$key]['selected'] = 1;
            }
        }
        $this->assign('service_type_list', $service_type_list);
        
        $rs = M("UserAuth")->where("user_id=" . $id . " and rel_id = 0")->findAll();
        foreach ($rs as $row) {
            $auth_list[$row['m_name']][$row['a_name']] = 1;
        }
        $this->assign("auth_list", $auth_list);
        $this->display();
    }
    public function update() 
    {
        $data = M('User')->create();

        $log_info = M('User')->where("id=" . intval($data['id']))->getField("user_name");
        //开始验证有效性
//        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));
        $this->assign("jumpUrl", u(MODULE_NAME . "/index"));
        if (!check_empty($data['user_pwd']) && $data['user_pwd'] != $_REQUEST['user_confirm_pwd']) {
            $this->error(L("USER_PWD_CONFIRM_ERROR"));
        }
        $res = save_user($_REQUEST, 'UPDATE');
        if ($res['status'] == 0) {
            $error_field = $res['data'];
            if ($error_field['error'] == EMPTY_ERROR) {
                if ($error_field['field_name'] == 'user_name') {
                    $this->error(L("USER_NAME_EMPTY_TIP"));
                } elseif ($error_field['field_name'] == 'email') {
                    $this->error(L("USER_EMAIL_EMPTY_TIP"));
                } else {
                    $this->error(sprintf(L("USER_EMPTY_ERROR"), $error_field['field_show_name']));
                }
            }
            if ($error_field['error'] == FORMAT_ERROR) {
                if ($error_field['field_name'] == 'email') {
                    $this->error(L("USER_EMAIL_FORMAT_TIP"));
                }
                if ($error_field['field_name'] == 'mobile') {
                    $this->error(L("USER_MOBILE_FORMAT_TIP"));
                }
            }

            if ($error_field['error'] == EXIST_ERROR) {
                if ($error_field['field_name'] == 'user_name') {
                    $this->error(L("USER_NAME_EXIST_TIP"));
                }
                if ($error_field['field_name'] == 'email') {
                    $this->error(L("USER_EMAIL_EXIST_TIP"));
                }
            }
        }
        //开始更新is_effect状态
        M("User")->where("id=" . intval($_REQUEST['id']))->setField("is_effect", intval($_REQUEST['is_effect']));
        
        save_log($log_info . L("UPDATE_SUCCESS"), 1);
        $this->success(L("UPDATE_SUCCESS"));
    }
    public function export_csv($page = 1) 
    {
        set_time_limit(0);
        $limit = (($page - 1) * intval(app_conf("BATCH_PAGE_SIZE"))) . "," . (intval(app_conf("BATCH_PAGE_SIZE")));

        //定义条件
        $map[DB_PREFIX . 'user.is_delete'] = 0;
        $map[DB_PREFIX . 'user.service_type_id'] = 3;

        if (intval($_REQUEST['group_id']) > 0) {
            $map[DB_PREFIX . 'user.group_id'] = intval($_REQUEST['group_id']);
        }

        if (strim($_REQUEST['user_name']) != '') {
            $map[DB_PREFIX . 'user.user_name'] = array('eq', strim($_REQUEST['user_name']));
        }
        if (strim($_REQUEST['email']) != '') {
            $map[DB_PREFIX . 'user.email'] = array('eq', strim($_REQUEST['email']));
        }
        if (strim($_REQUEST['mobile']) != '') {
            $map[DB_PREFIX . 'user.mobile'] = array('eq', strim($_REQUEST['mobile']));
        }
        if (strim($_REQUEST['pid_name']) != '') {
            $pid = M("User")->where("user_name='" . strim($_REQUEST['pid_name']) . "'")->getField("id");
            $map[DB_PREFIX . 'user.pid'] = $pid;
        }

        $list = M('User')
                        ->where($map)
                        ->join(DB_PREFIX . 'user_group ON ' . DB_PREFIX . 'user.group_id = ' . DB_PREFIX . 'user_group.id')
                        ->field(DB_PREFIX . 'user.*,' . DB_PREFIX . 'user_group.name')
                        ->limit($limit)->findAll();


        if ($list) {
            register_shutdown_function(array(&$this, 'export_csv'), $page + 1);

            $user_value = array('id' => '""', 'user_name' => '""', 'email' => '""', 'mobile' => '""', 'group_id' => '""');
            if ($page == 1)
                $content = iconv("utf-8", "utf-8", "编号,用户名,电子邮箱,手机号,会员组");


            //开始获取扩展字段
            $extend_fields = M("UserField")->order("sort desc")->findAll();
            foreach ($extend_fields as $k => $v) {
                $user_value[$v['field_name']] = '""';
                if ($page == 1)
                    $content = $content . "," . iconv('utf-8', 'utf-8', $v['field_show_name']);
            }
            if ($page == 1)
                $content = $content . "\n";

            foreach ($list as $k => $v) {
                $user_value = array();
                $user_value['id'] = iconv('utf-8', 'utf-8', '"' . $v['id'] . '"');
                $user_value['user_name'] = iconv('utf-8', 'utf-8', '"' . $v['user_name'] . '"');
                $user_value['email'] = iconv('utf-8', 'utf-8', '"' . $v['email'] . '"');
                $user_value['mobile'] = iconv('utf-8', 'utf-8', '"' . $v['mobile'] . '"');
                $user_value['group_id'] = iconv('utf-8', 'utf-8', '"' . $v['name'] . '"');

                //取出扩展字段的值
                $extend_fieldsval = M("UserExtend")->where("user_id=" . $v['id'])->findAll();
                foreach ($extend_fields as $kk => $vv) {
                    foreach ($extend_fieldsval as $kkk => $vvv) {
                        if ($vv['id'] == $vvv['field_id']) {
                            $user_value[$vv['field_name']] = iconv('utf-8', 'utf-8', '"' . $vvv['value'] . '"');
                            break;
                        }
                    }
                }

                $content .= implode(",", $user_value) . "\n";
            }


            header("Content-Disposition: attachment; filename=user_list.csv");
            echo $content;
        } else {
            if ($page == 1)
                $this->error(L("NO_RESULT"));
        }
    }
    public function delete() 
    {
        //删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset($id)) {
            //删除验证
            if (M("DealOrder")->where(array('user_id' => array('in', explode(',', $id))))->count() > 0) {
                $this->error(l("ORDER_EXIST_DELETE_FAILED"), $ajax);
            }
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M('User')->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['user_name'];
            }
            if ($info)
                $info = implode(",", $info);
            $list = M('User')->where($condition)->setField('is_delete', 1);
            if ($list !== false) {
                //把信息屏蔽
                M("Topic")->where("user_id in (" . $id . ")")->setField("is_effect", 0);
                M("TopicReply")->where("user_id in (" . $id . ")")->setField("is_effect", 0);
                M("Message")->where("user_id in (" . $id . ")")->setField("is_effect", 0);
                save_log($info . l("DELETE_SUCCESS"), 1);
                $this->success(l("DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("DELETE_FAILED"), 0);
                $this->error(l("DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function foreverdelete() 
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M('User')->where($condition)->findAll();
            foreach ($rel_data as $data) {
                $info[] = $data['user_name'];
            }
            if ($info)
                $info = implode(",", $info);
            $ids = explode(',', $id);
            foreach ($ids as $uid) {
                delete_user($uid);
            }
            save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);

            $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

}

?>
<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class JingliAction extends CommonAction{
	public function index()
	{
		
		$condition['type_id'] = 1;
		$this->assign("default_map",$condition);
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ("service");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();

	}
	public function add()
	{
		//输出分组列表
		$this->assign("role_list",M("service")->where("pid = 0")->findAll());
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->assign("role_list",M("Role")->where("is_delete = 0")->findAll());
		$this->display ();
	}
	//相关操作
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("adm_name");		
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		if(conf("DEFAULT_ADMIN")==$info)
		{
			$this->ajaxReturn($c_is_effect,l("DEFAULT_ADMIN_CANNOT_EFFECT"),1)	;	
		}	
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	public function insert() {
		
		B('FilterString');
		$data = M("service")->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error("请输入用户名");
		}	
		
		if(!check_empty($data['password']))
		{
			$this->error("请输入密码");
		}
		if(M("service")->where("mobile='".$data['mobile']."'")->count()>0)
		{
			$this->error("手机号已存在");
		}
		if(M("service")->where("name='".$data['name']."'")->count()>0)
		{
			$this->error("名称已存在");
		}
		if(!preg_match("/1\d{10}/i",$data['mobile'])){
			$this->error("请输入正确的手机号");
		}
		$data['adm_password'] = md5(strim($data['adm_password']));
		$data['createtime']=time();
		$list=M("service")->add($data);
		if (false !== $list) {
			$this->assign("jumpUrl",u(MODULE_NAME."/index"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			$this->error(L("INSERT_FAILED"));
		}
	}


	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("adm_name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['adm_password']))
		{
			unset($data['adm_password']);  //不更新密码
		}
		else
		{
			$data['adm_password'] = md5(strim($data['adm_password']));
		}
		if($data['role_id']==0)
		{
			$this->error(L("ROLE_EMPTY_TIP"));
		}
		if(conf("DEFAULT_ADMIN")==$log_info)
		{
			$adm_session = es_session::get(md5(conf("AUTH_KEY")));
			$adm_name = $adm_session['adm_name'];
			if($log_info!=$adm_name)
			$this->error(l("DEFAULT_ADMIN_CANNOT_MODIFY"));
			
			if($data['is_effect']==0)
			{
				$this->error(l("DEFAULT_ADMIN_CANNOT_EFFECT"));
			}
		}	
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}

	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );			
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				

				foreach($rel_data as $data)
				{
					$info[] = $data['adm_name'];	
					if(conf("DEFAULT_ADMIN")==$data['adm_name'])
					{
						$this->error ($data['adm_name'].l("DEFAULT_ADMIN_CANNOT_DELETE"),$ajax);
					}	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function set_default()
	{
		$adm_id = intval($_REQUEST['id']);
		$admin = M("Admin")->getById($adm_id);
		if($admin)
		{
			M("Conf")->where("name = 'DEFAULT_ADMIN'")->setField("value",$admin['adm_name']);
			//开始写入配置文件
			$sys_configs = M("Conf")->findAll();
			$config_str = "<?php\n";
			$config_str .= "return array(\n";
			foreach($sys_configs as $k=>$v)
			{
				$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
			}
			$config_str.=");\n ?>";
						
			$filename = get_real_path()."public/sys_config.php";
			
		    if (!$handle = fopen($filename, 'w')) {
			     $this->error(l("OPEN_FILE_ERROR").$filename);
			}
			
			    
			if (fwrite($handle, $config_str) === FALSE) {
			     $this->error(l("WRITE_FILE_ERROR").$filename);
			}
			
	    	fclose($handle);
	    
			
			save_log(l("CHANGE_DEFAULT_ADMIN"),1);
			clear_cache();
			$this->success(L("SET_DEFAULT_SUCCESS"));
		}
		else
		{
			$this->error(L("NO_ADMIN"));
		}
	}

}
?>
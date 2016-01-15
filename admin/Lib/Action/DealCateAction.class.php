<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class DealCateAction extends CommonAction{
	public function index()
	{
		$condition['is_delete'] = 0;
		$condition['pid'] = 0;
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
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['level'] = -1;
			$v['name'] = $v['name'];
			$result[$row] = $v;
			$row++;
			$sub_cate = M(MODULE_NAME)->where(array("id"=>array("in",D(MODULE_NAME)->getChildIds($v['id'])),'is_delete'=>0))->findAll();
			$sub_cate = D(MODULE_NAME)->toFormatTree($sub_cate,'name');
			foreach($sub_cate as $kk=>$vv)
			{
				$vv['name']	=	$vv['title_show'];
				$result[$row] = $vv;
				$row++;
			}
		}
		//dump($result);exit;
		$this->assign("list",$result);
		
		//输出标签分组
		$tag_group = M("TagGroup")->findAll();
		$this->assign("tag_group",$tag_group);
		
		$this->display ();
		return;
	}
	

	public function add()
	{
// 		//输出评分分组
// 		$point_group = M("PointGroup")->findAll();
// 		$this->assign("point_group",$point_group);
// 		//输出图片分组
// 		$images_group = M("ImagesGroup")->findAll();
// 		$this->assign("images_group",$images_group);
// 		//输出标签分组
// 		$tag_group = M("TagGroup")->findAll();
// 		$this->assign("tag_group",$tag_group);
		
		$this->assign("newsort",M(MODULE_NAME)->where("is_delete=0")->max("sort")+1);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("DEALCATE_NAME_EMPTY_TIP"));
		}	

		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			
// 			//标签分组
// 			$tag_group = $_REQUEST['tag_group'];
// 			foreach($tag_group as $group_id)
// 			{
// 				if($group_id>0)
// 				{
// 					$tag_group_link['tag_group_id'] = intval($group_id);
// 					$tag_group_link['category_id'] = intval($list);
// 					M("TagGroupLink")->add($tag_group_link);
// 				}
// 			}
			
// 			//图片分组
// 			$images_group = $_REQUEST['images_group'];
// 			foreach($images_group as $images_id)
// 			{
// 				if($images_id>0)
// 				{
// 					$images_group_link['images_group_id'] = intval($images_id);
// 					$images_group_link['category_id'] = intval($list);
// 					M("ImagesGroupLink")->add($images_group_link);
					
// 				}
// 			}
			
// 			//点评评分分组
// 			$point_group = $_REQUEST['point_group'];
// 			foreach($point_group as $group_id)
// 			{
// 				if($group_id>0)
// 				{
// 					$point_group_link['point_group_id'] = intval($group_id);
// 					$point_group_link['category_id'] = intval($list);
// 					M("PointGroupLink")->add($point_group_link);
					
// 				}
// 			}
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 

			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );		
		
// 		//输出评分分组
// 		$point_group = M("PointGroup")->findAll();
// 		foreach($point_group as $k=>$v)
// 		{
// 			if(M("PointGroupLink")->where("category_id=".$id." and point_group_id =".$v['id'])->count()>0)
// 			{
// 				$point_group[$k]['is_check'] = true;
// 			}
// 		}
// 		$this->assign("point_group",$point_group);
		
// 		//输出图片分组
// 		$images_group = M("ImagesGroup")->findAll();
// 		foreach($images_group as $k=>$v)
// 		{
// 			if(M("ImagesGroupLink")->where("category_id=".$id." and images_group_id =".$v['id'])->count()>0)
// 			{
// 				$images_group[$k]['is_check'] = true;
// 			}
// 		}
// 		$this->assign("images_group",$images_group);
		
// 		//输出标签分组
// 		$tag_group = M("TagGroup")->findAll();
// 		foreach($tag_group as $k=>$v)
// 		{
// 			if(M("TagGroupLink")->where("category_id=".$id." and tag_group_id =".$v['id'])->count()>0)
// 			{
// 				$tag_group[$k]['is_check'] = true;
// 			}
// 		}
		
		$this->assign("tag_group",$tag_group);
		
		
		$this->display ();
	}

	

    public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		 
		 
		 
		 
			 
			 
			 
			 
			 
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		 
			 
			 
			 
			 
			 
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("title");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			$link_condition = "category_id=".$data['id'];
// 			M("TagGroupLink")->where($link_condition)->delete();
// 			M("ImagesGroupLink")->where($link_condition)->delete();
// 			M("PointGroupLink")->where($link_condition)->delete();
			
// 			//标签分组
// 			$tag_group = $_REQUEST['tag_group'];
// 			foreach($tag_group as $group_id)
// 			{
// 				if($group_id>0)
// 				{
// 					$tag_group_link['tag_group_id'] = intval($group_id);
// 					$tag_group_link['category_id'] = intval($data['id']);
// 					M("TagGroupLink")->add($tag_group_link);
// 				}
// 			}
			
// 			//图片分组
// 			$images_group = $_REQUEST['images_group'];
// 			foreach($images_group as $images_id)
// 			{
// 				if($images_id>0)
// 				{
// 					$images_group_link['images_group_id'] = intval($images_id);
// 					$images_group_link['category_id'] = intval($data['id']);
// 					M("ImagesGroupLink")->add($images_group_link);
// 				}
// 			}
			
// 			//点评评分分组
// 			$point_group = $_REQUEST['point_group'];
// 			foreach($point_group as $group_id)
// 			{
// 				if($group_id>0)
// 				{
// 					$point_group_link['point_group_id'] = intval($group_id);
// 					$point_group_link['category_id'] = intval($data['id']);
// 					M("PointGroupLink")->add($point_group_link);
					
// 				}
// 			}
			
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			
			M("SupplierLocation")->setField("dp_group_point","");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}

	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );

				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
					M("DealCateTypeLink")->where(array ('cate_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					save_log($info.l("DELETE_SUCCESS"),1); 			 
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
}
?>
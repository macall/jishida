<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class SupplierLocationAction extends CommonAction{
	public function index()
	{
		$reminder = M("RemindCount")->find();
		$reminder['dp_count_time'] = NOW_TIME;
		M("RemindCount")->save($reminder);
		$condition = " 1=1 ";
		if(intval($_REQUEST['supplier_id'])>0)
		{
			es_session::set("admin_supplier_id",intval($_REQUEST['supplier_id']));		
			$supplier_info = M("Supplier")->where("id=".intval($_REQUEST['supplier_id']))->find();
			$this->assign("supplier_info",$supplier_info);
			$condition = " supplier_id = ".intval($_REQUEST['supplier_id']);;
		}
		
		
		
		
		$page_idx = intval($_REQUEST['p'])==0?1:intval($_REQUEST['p']);
		$page_size = C('PAGE_LISTROWS');
		$limit = (($page_idx-1)*$page_size).",".$page_size;
		
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		}
		
		
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location where $condition");
		
		if($total<50000)
		{
			if($order=="")
			$order = "id";
			$orderby = "order by ".$order." ".$sort;

		}
		else
		{
			if($order!='is_effect'&&$order!='is_recommend'&&$order!='is_verify'&&$order!='is_main'&&$order!='new_dp_count')
			{
			   $orderby = "";
			}
			else
			{
				$orderby = "order by ".$order." ".$sort;
			}
		}
		
		if(strim($_REQUEST['name'])!='')
		{			
			if($total<50000)
			{
				$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where name like '%".strim($_REQUEST['name'])."%' and $condition  $orderby limit ".$limit);
				$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location where name like '%".strim($_REQUEST['name'])."%' and $condition ");			
			}
			else
			{
				$kws_div = div_str(trim($_REQUEST['name']));
				foreach($kws_div as $k=>$item)
				{
					$kw[$k] = str_to_unicode_string($item);
				}
				$kw_unicode = implode(" ",$kw);
				$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where match(`name_match`) against('".$kw_unicode."' IN BOOLEAN MODE) and $condition  $orderby limit ".$limit);
				$total = $GLOBALS['db']->getOne("select * from ".DB_PREFIX."supplier_location where match(`name_match`) against('".$kw_unicode."' IN BOOLEAN MODE) and $condition");
				
			}
		}
		else
		{
			$list= $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where $condition $orderby limit ".$limit);
			$total = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier_location where $condition ");
		}
		$p = new Page ( $total, '' );
		$page = $p->show ();
		
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
			
		$this->assign ( 'list', $list );
		$this->assign ( "page", $page );
		$this->assign ( "nowPage",$p->nowPage);
			
		$this->display ();
		return;
		
		
		
		
	}
	public function add()
	{
		$supplier_id = intval(es_session::get("admin_supplier_id"));
		$supplier_info = M("Supplier")->getById($supplier_id);
		$this->assign("supplier_info",$supplier_info);
		
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);	
		
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);
		
		$brand_list = M("Brand")->findAll();
		$this->assign("brand_list",$brand_list);	

		$this->display();
	}
	
	public function search_supplier()
	{
		if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."supplier"))<50000)
			$sql  ="select * from ".DB_PREFIX."supplier where name like '%".strim($_REQUEST['key'])."%' limit 30";
		else
		{
	
			$kws_div = div_str(trim($_REQUEST['key']));
			foreach($kws_div as $k=>$item)
			{
				$kw[$k] = str_to_unicode_string($item);
			}
			$kw_unicode = implode(" ",$kw);
			$sql = "select * from ".DB_PREFIX."supplier where (match(name_match) against('".$kw_unicode."' IN BOOLEAN MODE)) limit 30";
		}
			
		$supplier_list = $GLOBALS['db']->getAll($sql);
		$this->assign("supplier_list",$supplier_list);
		$this->display();
	}
	
	public function area_list()
	{
		$id =  intval($_REQUEST['id']); //门店id
		$edit_type = intval($_REQUEST['edit_type'])!=2?1:2;  //1管理员数据 2商户提交数据
		$area_list = M("Area")->where("city_id=".intval($_REQUEST['city_id']))->findAll();
		
		
		if($edit_type == 1){//来自管理员
		    $location_curr_area = M("SupplierLocationAreaLink")->where("location_id = ".$id)->field("area_id")->findAll();
		    foreach ($location_curr_area as $k=>$v){
		        $f_curr_area[] = $v['area_id'];
		    }
		}
		 
		if($edit_type == 2){//来自商户提交
		    $location_curr_area = $GLOBALS['db']->getOne("select cache_supplier_location_area_link from ".DB_PREFIX."supplier_location_biz_submit where id = ".$id);
		    $f_curr_area = unserialize($location_curr_area);
		}
		
		foreach($area_list as $k=>$v)
		{
		    if(in_array($v['id'], $f_curr_area))
		    {
		        $area_list[$k]['checked'] = true;
		    }
		}
		
		$this->assign("area_list",$area_list);
		$this->display();		
	}
	

	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//对于商户请求操作
		$id = intval($_REQUEST['id']);
		$edit_type = intval($_REQUEST['edit_type']);
		
		if($id>0 && $edit_type==2){//商户申请新增团购
		    unset($data['id']);
		}
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("LOCATION_NAME_EMPTY_TIP"));
		}	
		if($data['city_id']==0)
		{
			$this->error(L("DEAL_CITY_EMPTY_TIP"));
		}
		$city_info = M("DealCity")->where("id=".intval($data['city_id']))->find();
		if($city_info['pid']==0){
			$this->error("只能选择城市，不能选择省份");
		}	
		// 更新数据
		
	    //自动创建品牌
		if($data['supplier_id'] == 0)
		{
			$supplier_data['name'] = $data['name'];
			$supplier_data['is_effect'] = 1;
			$supplier_id = M("Supplier")->add($supplier_data);
			syn_supplier_match($supplier_id);
			$data['supplier_id'] = $supplier_id;
		}
		$data['is_effect'] = 1;
		
		$log_info = $data['name'];
		if(M(MODULE_NAME)->where("name='".$data['name']."'")->find()){
			$this->error("门店名重复");
		}else{
			$list=M(MODULE_NAME)->add($data);	
		}
		if (false !== $list) {
			//成功提示
			if($data['is_main']==1)
			{
				M(MODULE_NAME)->where("supplier_id=".$data['supplier_id']." and id <> ".$list)->setField("is_main",0);
			}
			if(M(MODULE_NAME)->where("supplier_id=".$data['supplier_id']." and is_main = 1")->count()==0)
			{
				M(MODULE_NAME)->where("id=".$list)->setField("is_main",1);
			}

			$area_ids = $_REQUEST['area_id'];
			foreach($area_ids as $area_id)
			{
				$area_data['area_id'] = $area_id;
				$area_data['location_id'] = $list;
				M("SupplierLocationAreaLink")->add($area_data);
			}
			
			$brand_ids = $_REQUEST['brand_id'];
			foreach($brand_ids as $brand_id)
			{
				$brand_data['brand_id'] = $brand_id;
				$brand_data['location_id'] = $list;
				M("SupplierLocationBrandLink")->add($brand_data);
			}
			
			foreach($_REQUEST['deal_cate_type_id'] as $type_id)
			{
				$link_data = array();
				$link_data['deal_cate_type_id'] = $type_id;
				$link_data['location_id'] = $list;
				M("DealCateTypeLocationLink")->add($link_data);
			}
			
			$group_ids = $_REQUEST['group_id'];
        	foreach($group_ids as $gid=>$preset)
        	{
        		if(strim($preset)!='')
        		{
        			$link['group_id'] = $gid;
        			$link['supplier_location_id'] = $list;
        			$link['preset'] = strim($preset);
        			M("SupplierTagGroupPreset")->add($link);
        		}
        	}
        	
        	
        	$show_group_ids = $_REQUEST['show_group_id'];
        	$exist_tags_array = array();
        	foreach($show_group_ids as $gid=>$row)
        	{
        		if(strim($row)!='')
        		{
        			$show_tags_array = preg_split("[ |,]",$row);
        			foreach($show_tags_array as $kk=>$rr)
        			{
        				$rs = explode("|",$rr);
        				$tag = strim($rs[0]);
        				$count = intval($rs[1]);
        				$exist_tags_array[] = "'".$tag."'";
						if(strim($tag)!='')
						{
        					$tag_rs = array();
        					$tag_rs['tag_name'] = $tag;
        					$tag_rs['supplier_location_id'] = $list;
        					$tag_rs['group_id'] = $gid;
        					$tag_rs['total_count'] = $count;
        					M("SupplierTag")->add($tag_rs);
						}
        				       				
        			}
        		}
        	}
			 
			syn_supplier_location_match($list);
			save_log($log_info.L("INSERT_SUCCESS"),1);
			
			if($id>0 && $edit_type == 2){ //商户提交审核
			    //同步商户数据表
			    $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_biz_submit",array("location_id"=>$list,"admin_check_status"=>1),"UPDATE","id=".$id);
			    //同步商户发布的图集
			    $this->syn_location_images($list);
			}
			
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	
	
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$supplier_info = M("Supplier")->getById($vo['supplier_id']);
		
		if(!$supplier_info)
		{
			$this->error(l("SUPPLIER_NOT_EXIST"));
		}
		$this->assign("supplier_info",$supplier_info);
		
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);	
		
		$brand_list = M("Brand")->findAll();			
		foreach($brand_list as $k=>$v)
		{
			if(M("SupplierLocationBrandLink")->where("location_id=".$vo['id']." and brand_id=".$v['id'])->count()>0)
			{
				$brand_list[$k]['checked'] = true;
			}
		}
		$this->assign("brand_list",$brand_list);
		
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);		
		
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		//对于商户请求操作
		if(intval($_REQUEST['edit_type']) == 2 && intval($_REQUEST['location_id'])>0){ //商户提交修改审核
		    $location_submit_id = intval($_REQUEST['id']);
		    $data['id'] = intval($_REQUEST['location_id']);
		}
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("LOCATION_NAME_EMPTY_TIP"));
		}	
		if($data['supplier_id']==0)
		{
			$this->error(L("SUPPLIER_NOT_EXIST"));
		}
		if($data['city_id']==0)
		{
			$this->error(L("DEAL_CITY_EMPTY_TIP"));
		}
		$city_info = M("DealCity")->where("id=".intval($data['city_id']))->find();
		if($city_info['pid']==0){
			$this->error("只能选择城市，不能选择省份");
		}
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			
			M("SupplierLocationAreaLink")->where("location_id=".$data['id'])->delete();
			$area_ids = $_REQUEST['area_id'];
			foreach($area_ids as $area_id)
			{
				$area_data['area_id'] = $area_id;
				$area_data['location_id'] = $data['id'];
				M("SupplierLocationAreaLink")->add($area_data);
			}
			M("SupplierLocationBrandLink")->where("location_id=".$data['id'])->delete();
			$brand_ids = $_REQUEST['brand_id'];
			foreach($brand_ids as $brand_id)
			{
				$brand_data['brand_id'] = $brand_id;
				$brand_data['location_id'] = $data['id'];
				M("SupplierLocationBrandLink")->add($brand_data);
			}
			M("DealCateTypeLocationLink")->where("location_id=".$data['id'])->delete();
			foreach($_REQUEST['deal_cate_type_id'] as $type_id)
			{
				$link_data = array();
				$link_data['deal_cate_type_id'] = $type_id;
				$link_data['location_id'] = $data['id'];
				M("DealCateTypeLocationLink")->add($link_data);
			}
			
			M("SupplierTagGroupPreset")->where("supplier_location_id=".$data['id'])->delete();
        	$group_ids = $_REQUEST['group_id'];
        	foreach($group_ids as $gid=>$preset)
        	{
        		if(strim($preset)!='')
        		{
        			$link['group_id'] = $gid;
        			$link['supplier_location_id'] = $data['id'];
        			$link['preset'] = strim($preset);
        			M("SupplierTagGroupPreset")->add($link);
        		}
        	}
			
        	$show_group_ids = $_REQUEST['show_group_id'];
        	$exist_tags_array = array("''");
        	foreach($show_group_ids as $gid=>$row)
        	{
        		if(strim($row)!='')
        		{
        			$show_tags_array = preg_split("[ |,]",$row);

        			foreach($show_tags_array as $kk=>$rr)
        			{
        				$rs = explode("|",$rr);
        				$tag = strim($rs[0]);
        				$count = intval($rs[1]);
        				$exist_tags_array[] = "'".$tag."'";
        				$tag_rs = M("SupplierTag")->where("tag_name = '".$tag."' and supplier_location_id = ".$data['id']." and group_id = ".$gid)->find();
        				if($tag_rs)
        				{
        					$tag_rs['total_count'] = $count;    
        					$GLOBALS['db']->query("update ".DB_PREFIX."supplier_tag set total_count = ".$count." where tag_name = '".$tag."' and supplier_location_id = ".$data['id']." and group_id = ".$gid); 
        				}
        				else
        				{
        					if(strim($tag)!='')
        					{
        					$tag_rs = array();
        					$tag_rs['tag_name'] = $tag;
        					$tag_rs['supplier_location_id'] = $data['id'];
        					$tag_rs['group_id'] = $gid;
        					$tag_rs['total_count'] = $count;
        					M("SupplierTag")->add($tag_rs);
        					}
        				}        				
        			}
        		}
        	}
        	
        	$GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_tag where tag_name not in (".implode(",",$exist_tags_array).") and supplier_location_id = ".$data['id']);
        	
			syn_supplier_location_match($data['id']);
			 
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			
			//对于商户请求操作
			if(intval($_REQUEST['edit_type']) == 2 && $location_submit_id>0){ //商户提交修改审核
			    /*同步商户发布表状态*/
			    $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_biz_submit",array("admin_check_status"=>1),"UPDATE","id=".$location_submit_id); // 1 通过 2 拒绝',
			    $this->syn_location_images($data['id']);
			}
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
				if(M("SupplierLocationDp")->where(array ('supplier_location_id' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error ("请先清空商户的点评",$ajax);
				}
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
				//删除相关预览图
//				foreach($rel_data as $data)
//				{
//					@unlink(get_real_path().$data['preview']);
//				}			
				if ($list!==false) {
					 
					M("SupplierTagGroupPreset")->where(array ('supplier_location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("SupplierTag")->where(array ('supplier_location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("SupplierLocationPointResult")->where(array ('supplier_location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("DealLocationLink")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("YouhuiLocationLink")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("DealCateTypeLocationLink")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("SupplierAccountLocationLink")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("TagUserVote")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("SupplierLocationImages")->where(array ('supplier_location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					
					M("SupplierLocationAreaLink")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("SupplierLocationBrandLink")->where(array ('location_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					 
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
	//商户提交
	public function publish()
	{
	    if(isset($_REQUEST['admin_check_status']) && $_REQUEST['admin_check_status']==0){
	        $map['admin_check_status'] = intval($_REQUEST['admin_check_status']);
	    }

	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	    $name="SupplierLocationBizSubmit";
	    $model = D ($name);
	    if (! empty ( $model )) {
	        $this->_list ( $model, $map );
	    }

	    $this->assign("show_status_check_btn",U("SupplierLocation/publish",array("admin_check_status"=>0)));
	    $this->display ();
	    return;
	}
	
	public function biz_apply_edit(){
	    $id = intval($_REQUEST ['id']);
		$condition['id'] = $id;
		$name = "supplier_location_biz_submit";
		$vo = M($name)->where($condition)->find();
		$supplier_info = M("Supplier")->getById($vo['supplier_id']);

		if(!$supplier_info)
		{
			$this->error(l("SUPPLIER_NOT_EXIST"));
		}
		$this->assign("supplier_info",$supplier_info);
		
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);	
		
		$brand_list = M("Brand")->findAll();			
		foreach($brand_list as $k=>$v)
		{
			if(M("SupplierLocationBrandLink")->where("location_id=".$vo['id']." and brand_id=".$v['id'])->count()>0)
			{
				$brand_list[$k]['checked'] = true;
			}
		}
		$this->assign("brand_list",$brand_list);
		
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);		
		
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	/**
	 * 图库编辑
	 */
	public function location_images_edit(){
	    $id = intval($_REQUEST['id']);

	    $location_sub_info = $GLOBALS['db']->getRow("select id,name,cache_supplier_location_images from ".DB_PREFIX."supplier_location_biz_submit where id=".$id);  //序列化的字段
	    $imglist = unserialize($location_sub_info['cache_supplier_location_images']);
	    
	    $this->assign('location_sub_info',$location_sub_info);
	    $this->assign('imglist',$imglist);
	    $this->display ();
	}
	/**
	 * 修改更新图集
	 */
	public function update_biz_submit_images(){
	    $id = intval($_REQUEST['id']);
	    $data['cache_supplier_location_images'] = serialize($_REQUEST['location_images']);
	    if($id){
	        M("SupplierLocationBizSubmit")->where('id='.$id)->save($data);
	    }
	    $this->success(L("INSERT_SUCCESS"));
	}
	/**
	 * 同步图集
	 */
    public function syn_location_images($id){
        //获取图集
//         echo $id,$supplier_id;
        $images = unserialize($GLOBALS['db']->getOne("select cache_supplier_location_images from ".DB_PREFIX."supplier_location_biz_submit where location_id=".$id));  
        if(count($images)>0){
            M("SupplierLocationImages")->where('supplier_location_id='.$supplier_id)->delete();
            foreach($images as $k=>$v){
                $data = array();
                $data['image'] = $v;
                $data['sort'] = 100;
                $data['create_time'] = NOW_TIME;
                $data['supplier_location_id'] = $id;
                $data['status'] = 1;
                M("SupplierLocationImages")->add($data);
            }
        }
        M("SupplierLocation")->where("id=".$id)->save(array("image_count"=>count($images)));
    }
    /**
     * 删除商户提交数据
     */
    public function biz_submit_del() {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
    
            $rel_data = M("SupplierLocationBizSubmit")->where($condition)->findAll();
            foreach($rel_data as $data)
            {
                $info[] = $data['name'];
    
    
            }
            if($info) $info = implode(",",$info);
            $list = M("SupplierLocationBizSubmit")->where ( $condition )->delete();
    
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
    
    /**
     * 拒绝商户申请
     */
    public function refused_apply(){
        $id = intval($_REQUEST['id']);
        $location_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location_biz_submit where id = ".$id);
        if($location_submit_info['admin_check_status'] == 0){
            //更新商户表状态为拒绝
             
            $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_biz_submit",array("admin_check_status"=>2),"UPDATE","id=".$id);
            $result['status'] = 1;
            $result['info'] = "已经拒绝用户申请";
        }else{
            $result['status'] = 0;
            $result['info'] = "申请不存在";
        }
        ajax_return($result);
    }
    
    /**
     * 下架申请
     */
    public function downline(){
        $id = intval($_REQUEST['id']);
        $location_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location_biz_submit where id = ".$id);
        if($location_submit_info && $location_submit_info['biz_apply_status']==3){
            //更新商户表状态为通过
            $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_biz_submit",array("admin_check_status"=>1),"UPDATE","id=".$id);
            //更新门店数据表
            $GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location",array("is_effect"=>0),"UPDATE","id=".$location_submit_info['location_id']);
            $result['status'] = 1;
            $result['info'] = "门店已经成功下架";
        }else{
            $result['status'] = 0;
            $result['info'] = "申请不存在";
        }
        ajax_return($result);
    }
    
	
	function load_sub_cate()
	{
		$cate_id = intval($_REQUEST['cate_id']);
		$id = intval($_REQUEST['id']);
		$edit_type = intval($_REQUEST['edit_type'])!=2?1:2;  //1管理员数据 2商户提交数据
		
		$sub_cate_list = $GLOBALS['db']->getAll("select c.* from ".DB_PREFIX."deal_cate_type as c left join ".DB_PREFIX."deal_cate_type_link as l on l.deal_cate_type_id = c.id where l.cate_id = ".$cate_id);
		
		if($edit_type == 1){ //管理员添加数据
		    $sub_cate_arr_data = $GLOBALS['db']->getAll("select deal_cate_type_id from ".DB_PREFIX."deal_cate_type_location_link where location_id = ".$id);
		    foreach ($sub_cate_arr_data as $k=>$v){
		        $sub_cate_arr[] = $v['deal_cate_type_id'];
		    }
		
		}elseif ($edit_type == 2){//商户提交数据
		    $sub_cate_arr = unserialize($GLOBALS['db']->getOne("select cache_deal_cate_type_location_link from ".DB_PREFIX."supplier_location_biz_submit where id=".$id));  //序列化的字段
		}
	
		foreach($sub_cate_list as $k=>$v)
		{
		    if(in_array($v['id'], $sub_cate_arr)){
		        $sub_cate_list[$k]['checked'] =1 ;
		    }
		}
		$this->assign("sub_cate_list",$sub_cate_list);
		
		if($sub_cate_list)
		$result['status'] = 1;
		else
		$result['status'] = 0;
		$result['html'] = $this->fetch();
		$this->ajaxReturn($result['html'],"",$result['status']);
	}
	
	function load_tag_list()
	{
		$cate_id = intval($_REQUEST['cate_id']);
		$id= intval($_REQUEST['id']);
		$group_list = M()->query("select g.* from ".DB_PREFIX."tag_group as g left join ".DB_PREFIX."tag_group_link as gl on g.id = gl.tag_group_id where gl.category_id = ".$cate_id);
		foreach($group_list as $k=>$v)
		{
			$sql = "select * from ".DB_PREFIX."supplier_tag where supplier_location_id = ".$id." and group_id = ".$v['id'];
			$res = $GLOBALS['db']->getAll($sql);
			$tags=array();
			foreach($res as $kk=>$vv)
			{
				$tags[] = $vv['tag_name']."|".$vv['total_count'];
			}
			$group_list[$k]['show_value'] = implode(" ",$tags);
			$group_list[$k]['value'] = $GLOBALS['db']->getOne("select `preset` from ".DB_PREFIX."supplier_tag_group_preset where group_id = ".$v['id']." and supplier_location_id = ".$id);
		}
		$this->assign("group_list",$group_list);
		$this->display();
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = $id;
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	
}
?>
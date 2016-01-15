<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class CommonAction extends AuthAction{
	public function index() {		
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
		$this->display ();
		return;
	}
	
	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		$name=$this->getActionName();
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where ( $map )->count ( 'id' );

		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据
                        if((isset ( $_REQUEST ['_order'] ) && $_REQUEST ['_order'] == 'finished_order_count')
                            || (isset ( $_REQUEST ['_order'] ) && $_REQUEST ['_order'] == 'unfinished_order_count')
                            || (isset ( $_REQUEST ['_order'] ) && $_REQUEST ['_order'] == 'tech_count')
                            || (isset ( $_REQUEST ['_order'] ) && $_REQUEST ['_order'] == 'cost_amount')
                            || (isset ( $_REQUEST ['_order'] ) && $_REQUEST ['_order'] == 'comp_amount')){
                            $order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
                        }
			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->findAll ( );
                        
                         //如果是会员列表
                        if($map[DB_PREFIX . 'user.service_type_id'] == 1){
                            foreach ($voList as $key=>$value) {
                                //拼接地址
                                $province = M('RegionConf')->where(array('id'=>$value['province_id']))->find();
                                $city = M('RegionConf')->where(array('id'=>$value['city_id']))->find();
                                $addr = $province['name'].' '.$city['name'].' '.$value['addr_detail'];
                                $value['province_id'] = $addr;
                                
                                //性别
                                if($value['sex'] == 1){
                                    $value['sex'] = '男';
                                }elseif($value['sex'] == 0){
                                    $value['sex'] = '女';
                                }else{
                                    $value['sex'] = '保密';
                                }
                                
                                //星级
                                $level = M('ServiceLevel')->where(array('id'=>$value['service_level_id']))->find();
                                $value['service_level_id'] = $level['levelname'];
                                
                                //已完成订单总消费
                                $finished[DB_PREFIX . 'deal_order.user_id'] = $value['id'];
                                $finished[DB_PREFIX . 'deal_order.order_status'] = 1;
                                $finished[DB_PREFIX . 'deal_order.extra_status'] = 0;
                                $finished[DB_PREFIX . 'deal_order.after_sale'] = 0;
                                $cost_amount = M('DealOrder')->where ( $finished )->sum('pay_amount');
                                $value['cost_amount'] = $cost_amount;
                                
                                $comp_amount = M('Complain')->where ( array('user_id'=>$value['id']) )->count('complain_id');
                                $value['comp_amount'] = $comp_amount;
                                
                                $voList[$key] = $value;
                            }
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'cost_amount' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 1){
                                
                                $cost_amount_list = array();
                                foreach ($voList as $value) {
                                    $cost_amount_list[] = $value['cost_amount'];
                                }

                                array_multisort($cost_amount_list, SORT_ASC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'cost_amount' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 0){
                                
                                $cost_amount_list = array();
                                foreach ($voList as $value) {
                                    $cost_amount_list[] = $value['cost_amount'];
                                }

                                array_multisort($cost_amount_list, SORT_DESC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'comp_amount' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 1){
                                
                                $comp_amount_list = array();
                                foreach ($voList as $value) {
                                    $comp_amount_list[] = $value['comp_amount'];
                                }

                                array_multisort($comp_amount_list, SORT_ASC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'comp_amount' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 0){
                                
                                $comp_amount_list = array();
                                foreach ($voList as $value) {
                                    $comp_amount_list[] = $value['comp_amount'];
                                }

                                array_multisort($comp_amount_list, SORT_DESC, $voList);
                            }
                        }
                        
                        //如果是技师列表
                        if($map[DB_PREFIX . 'user.service_type_id'] == 2){
                            foreach ($voList as $key=>$value) {
                                //拼接地址
                                $province = M('RegionConf')->where(array('id'=>$value['province_id']))->find();
                                $city = M('RegionConf')->where(array('id'=>$value['city_id']))->find();
                                $addr = $province['name'].' '.$city['name'].' '.$value['addr_detail'];
                                $value['province_id'] = $addr;
                                
                                //性别
                                if($value['sex'] == 1){
                                    $value['sex'] = '男';
                                }elseif($value['sex'] == 0){
                                    $value['sex'] = '女';
                                }else{
                                    $value['sex'] = '保密';
                                }
                                
                                
                                $level = M('ServiceLevel')->where(array('id'=>$value['service_level_id']))->find();
                                $value['service_level_id'] = $level['levelname'];
                                
                                
                                //已完成
                                $finished[DB_PREFIX . 'deal_order.technician_id'] = $value['id'];
                                $finished[DB_PREFIX . 'deal_order.order_status'] = 1;
                                $finished_order_count = M('DealOrder')->where ( $finished )->count ( 'id' );
                                //未完成
                                $unfinished[DB_PREFIX . 'deal_order.technician_id'] = $value['id'];
                                $unfinished[DB_PREFIX . 'deal_order.order_status'] = 0;
                                $unfinished_order_count = M('DealOrder')->where ( $unfinished )->count ( 'id' );
                                
                                $value['finished_order_count'] = $finished_order_count;
                                $value['unfinished_order_count'] = $unfinished_order_count;
                                
                                
                                $voList[$key] = $value;
                            }
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'finished_order_count' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 1){
                                
                                $finished_order_sort_list = array();
                                foreach ($voList as $value) {
                                    $finished_order_sort_list[] = $value['finished_order_count'];
                                }

                                array_multisort($finished_order_sort_list, SORT_ASC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'finished_order_count' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 0){
                                
                                $finished_order_sort_list = array();
                                foreach ($voList as $value) {
                                    $finished_order_sort_list[] = $value['finished_order_count'];
                                }

                                array_multisort($finished_order_sort_list, SORT_DESC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'unfinished_order_count' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 1){
                                
                                $unfinished_order_sort_list = array();
                                foreach ($voList as $value) {
                                    $unfinished_order_sort_list[] = $value['unfinished_order_count'];
                                }

                                array_multisort($unfinished_order_sort_list, SORT_ASC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'unfinished_order_count' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 0){
                                
                                $unfinished_order_sort_list = array();
                                foreach ($voList as $value) {
                                    $unfinished_order_sort_list[] = $value['unfinished_order_count'];
                                }

                                array_multisort($unfinished_order_sort_list, SORT_DESC, $voList);
                            }
                        }
                        
                        //如果是经理列表
                        if($map[DB_PREFIX . 'user.service_type_id'] == 3){
                            foreach ($voList as $key=>$value) {
                                //拼接地址
                                $province = M('RegionConf')->where(array('id'=>$value['province_id']))->find();
                                $city = M('RegionConf')->where(array('id'=>$value['city_id']))->find();
                                $addr = $province['name'].' '.$city['name'].' '.$value['addr_detail'];
                                $value['province_id'] = $addr;
                                
                                //性别
                                if($value['sex'] == 1){
                                    $value['sex'] = '男';
                                }elseif($value['sex'] == 0){
                                    $value['sex'] = '女';
                                }else{
                                    $value['sex'] = '保密';
                                }
                                
                                
                                $level = M('ServiceLevel')->where(array('id'=>$value['service_level_id']))->find();
                                $value['service_level_id'] = $level['levelname'];
                                
                                $tech_count = M('User')->where ( array('p_id'=>$value['id']) )->count ( 'id' );
                                $value['tech_count'] = $tech_count;
                                
                                $voList[$key] = $value;
                            }
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'tech_count' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 1){
                                
                                $tech_count_sort_list = array();
                                foreach ($voList as $value) {
                                    $tech_count_sort_list[] = $value['tech_count'];
                                }

                                array_multisort($tech_count_sort_list, SORT_ASC, $voList);
                            }
                            
                            if(isset ( $_REQUEST ['_order'] ) 
                                    && $_REQUEST ['_order'] == 'tech_count' 
                                    && isset ( $_REQUEST ['_sort'] )
                                    && $_REQUEST ['_sort'] == 0){
                                
                                $tech_count_sort_list = array();
                                foreach ($voList as $value) {
                                    $tech_count_sort_list[] = $value['tech_count'];
                                }

                                array_multisort($tech_count_sort_list, SORT_DESC, $voList);
                            }
                        }
			
//			echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示

			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			$this->assign ( "nowPage",$p->nowPage);
		}
		return;
	}
	
	
	/**
	 * 上传图片的通公基础方法
	 *
	 * @return array
	 */
	protected function uploadImage()
	{		
		if(conf("WATER_MARK")!="")
		$water_mark = get_real_path().conf("WATER_MARK");  //水印
		else
		$water_mark = "";
	    $alpha = conf("WATER_ALPHA");   //水印透明
	    $place = conf("WATER_POSITION");  //水印位置
	    
		$upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = conf('MAX_IMAGE_SIZE') ;  /* 配置于config */
        //设置上传文件类型
		
        $upload->allowExts  =  explode(',',conf('ALLOW_IMAGE_EXT')); /* 配置于config */        
       
        $dir_name = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name, 0777);
	        }
	        
	    $dir_name = $dir_name."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name, 0777);
	        }
	     
	    $dir_name = $dir_name."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name, 0777);
	        }
        
        
        
       	$save_rec_Path = "/public/attachment/".$dir_name."/origin/";  //上传时先存放原图          	      
        $savePath = APP_ROOT_PATH."public/attachment/".$dir_name."/origin/"; //绝对路径
		if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name."/origin/")) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name."/origin/");
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name."/origin/", 0777);
	    }        
        $domain_path = SITE_DOMAIN.APP_ROOT.$save_rec_Path;
			
		$upload->saveRule = "uniqid";   //唯一
		$upload->savePath = $savePath;
        if($upload->upload())
        {
        	$uploadList = $upload->getUploadFileInfo();    
         	foreach($uploadList as $k=>$fileItem)
        	{        			
        			$file_name = $fileItem['savepath'].$fileItem['savename'];  //上图原图的地址
        			//水印图
        			$big_save_path = str_replace("origin/","",$savePath);  //大图存放图径
					$big_file_name = str_replace("origin/","",$file_name);	
					
//					Image::thumb($file_name,$big_file_name,'',$big_width,$big_height);
					@file_put_contents($big_file_name,@file_get_contents($file_name));					
        			if(file_exists($water_mark))
	        		{
	        			Image::water($big_file_name,$water_mark,$big_file_name,$alpha,$place);	
	        		}	        		        			
        			$big_save_rec_Path = str_replace("origin/","",$save_rec_Path);  //上传的图存放的相对路径
        			$uploadList[$k]['recpath'] = $save_rec_Path;
        			$uploadList[$k]['bigrecpath'] = $big_save_rec_Path;        	

        			
        			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
        			{
	        			syn_to_remote_image_server("./public/attachment/".$dir_name."/origin/".$fileItem['savename']);
        				syn_to_remote_image_server("./public/attachment/".$dir_name."/".$fileItem['savename']);        			
        			}
        	} 
        	return array("status"=>1,'data'=>$uploadList,'info'=>L("UPLOAD_SUCCESS"));
        }
        else 
        {
        	return array("status"=>0,'data'=>null,'info'=>$upload->getErrorMsg());
        }
	}
	
	
	/**
	 * 上传文件公共基础方法
	 *
	 * @return array
	 */
	protected function uploadFile()
	{	    
		$upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = conf('MAX_IMAGE_SIZE') ;  /* 配置于config */
        //设置上传文件类型
		
        $upload->allowExts  =  explode(',',conf('ALLOW_IMAGE_EXT')); /* 配置于config */        
       
		$dir_name = to_date(NOW_TIME,"Ym");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name, 0777);
	        }
	        
	    $dir_name = $dir_name."/".to_date(NOW_TIME,"d");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name, 0777);
	        }
	     
	    $dir_name = $dir_name."/".to_date(NOW_TIME,"H");
	    if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir_name)) { 
	             @mkdir(APP_ROOT_PATH."public/attachment/".$dir_name);
	             @chmod(APP_ROOT_PATH."public/attachment/".$dir_name, 0777);
	        }
        
        
        
       	$save_rec_Path = "/public/attachment/".$dir_name."/";  //上传时先存放原图          	      
        $savePath = APP_ROOT_PATH."public/attachment/".$dir_name."/"; //绝对路径
        $domain_path = get_domain().APP_ROOT.$save_rec_Path;
        
			
		$upload->saveRule = "uniqid";   //唯一
		$upload->savePath = $savePath;
        if($upload->upload())
        {
        	$uploadList = $upload->getUploadFileInfo();   
        	foreach($uploadList as $k=>$fileItem)
        	{
      			$uploadList[$k]['recpath'] = $save_rec_Path;      			
      			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
      			{
      				syn_to_remote_image_server("./public/attachment/".$dir_name."/".$fileItem['savename']);
      			}
        	} 	
        	return array("status"=>1,'data'=>$uploadList,'info'=>L("UPLOAD_SUCCESS"));
        }
        else 
        {
        	return array("status"=>0,'data'=>null,'info'=>$upload->getErrorMsg());
        }
	}
	
	public function _before_update()
	{
		$uname = $_REQUEST['uname'];
		if($uname&&strim($uname)!='')
		{
			$rs = M(MODULE_NAME)->where("uname='".$uname."' and id <> ".intval($_REQUEST['id']))->count();
			if($rs > 0)
			{
				$this->error(l("UNAME_EXISTS"));
			}
		}
	}
	
	public function _before_insert()
	{
		$uname = $_REQUEST['uname'];
		if($uname&&strim($uname)!='')
		{
			$rs = M(MODULE_NAME)->where("uname='".$uname."' and id <> ".intval($_REQUEST['id']))->count();
			if($rs > 0)
			{
				$this->error(l("UNAME_EXISTS"));
			}
		}
	}
	
	
	public function toogle_status()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$field = $_REQUEST['field'];
		$info = $id."_".$field;
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField($field);  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		//推荐、置顶、精华增加积分等
		
		if($field == 'is_index' || $field == 'is_best')
		{
			if(!$c_is_effect)
			{
				if($field == 'is_index')
				$log_txt = '管理员操作置顶';
				if($field == 'is_best')
				$log_txt = '管理员操作精华';
				$money = doubleval(app_conf("USER_ADD_MONEY"));
				$score = intval(app_conf("USER_ADD_SCORE"));
				$point = intval(app_conf("USER_ADD_POINT"));
			}
			else
			{
				if($field == 'is_index')
				$log_txt = '管理员操作取消置顶';
				if($field == 'is_best')
				$log_txt = '管理员操作取消精华';
				$money = -doubleval(app_conf("USER_ADD_MONEY"));
				$score = -intval(app_conf("USER_ADD_SCORE"));
				$point = -intval(app_conf("USER_ADD_POINT"));
			}
			if($money != 0 || $score != 0 || $point != 0)
			{
				$user_id = M(MODULE_NAME)->where("id=".$id)->getField('user_id');
				require_once  APP_ROOT_PATH."system/model/user.php";
				modify_account(array("money"=>$money,"score"=>$score,"point"=>$point),$user_id,$log_txt);	
			}
		}
		M(MODULE_NAME)->where("id=".$id)->setField($field,$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
}
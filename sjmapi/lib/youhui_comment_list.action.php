<?php
class youhui_comment_list
{
	public function index()
	{
		$root = array();
		$root['return'] = 1;

		$yh_id = intval($GLOBALS['request']['yh_id']);
		$merchant_id = intval($GLOBALS['request']['merchant_id']);
		$city_name = strim($GLOBALS['request']['city_name']);//城市分类ID
		//添加点评数据
		$content = addslashes(trim($GLOBALS['request']['content']));
		if($GLOBALS['request']['from']=="wap"){
				if ($content != null && $content != ""){
					
					//检查用户,用户密码
					$user = $GLOBALS['user_info'];
					$user_id  = intval($user['id']);
					if($merchant_id > 0){
						$supplier_location_id = $merchant_id;
						$merchant_youhui_comment = array(
									'user_id' => $user_id,
									'supplier_location_id' => $supplier_location_id,
									'title' => $content,
									'content' => $content,
									'status' => 1,
									'create_time' => get_gmtime(),
						);
					$GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_dp", $merchant_youhui_comment, 'INSERT');
					
					}else{
						$rel_table = 'youhui';
						$rel_id = $yh_id;
						if($yh_id == 0) {
							//$rel_id = $merchant_id;
							//rel_table = 'supplier_location';
						}else{
							//$supplier_location_id = intval($GLOBALS['db']->getOne("select supplier_location_id from ".DB_PREFIX."youhui where id='".$yh_id."'"));
						}
						$merchant_youhui_comment = array(
									'user_id' => $user_id,
									'rel_id' => $rel_id,
									'rel_table' => $rel_table,
									//'supplier_location_id' => $supplier_location_id,
									'title' => $content,
									'content' => $content,
									'is_effect' => 1,
									'create_time' => get_gmtime(),
						);
		
						$GLOBALS['db']->autoExecute(DB_PREFIX."message", $merchant_youhui_comment, 'INSERT');
					}
				}
		}else{
			if ($content){
			//检查用户,用户密码
				$user = $GLOBALS['user_info'];
				$user_id  = intval($user['id']);
	
				if($merchant_id > 0){
					$supplier_location_id = $merchant_id;
					$merchant_youhui_comment = array(
								'user_id' => $user_id,
								'supplier_location_id' => $supplier_location_id,
								'title' => $content,
								'content' => $content,
								'status' => 1,
								'create_time' => get_gmtime(),
					);
					$GLOBALS['db']->autoExecute(DB_PREFIX."supplier_location_dp", $merchant_youhui_comment, 'INSERT');
				}else{
					$rel_table = 'youhui';
					$rel_id = $yh_id;
					if($yh_id == 0) {
						//$rel_id = $merchant_id;
						//rel_table = 'supplier_location';
					}else{
						//$supplier_location_id = intval($GLOBALS['db']->getOne("select supplier_location_id from ".DB_PREFIX."youhui where id='".$yh_id."'"));
					}
					$merchant_youhui_comment = array(
								'user_id' => $user_id,
								'rel_id' => $rel_id,
								'rel_table' => $rel_table,
								//'supplier_location_id' => $supplier_location_id,
								'title' => $content,
								'content' => $content,
								'is_effect' => 1,
								'create_time' => get_gmtime(),
					);
	
					$GLOBALS['db']->autoExecute(DB_PREFIX."message", $merchant_youhui_comment, 'INSERT');
				}
			}
		
			$id = $GLOBALS['db']->insert_id();
			$root['id'] = $id;
			if($id > 0)
			{
				$root['status'] = 1;
				$root['info'] = "添加成功";
			}else{
				$root['status'] = 0;
				$root['info'] = "添加失败";
			}
		}// Fwb add 2014-08-27

		$page = intval($GLOBALS['request']['page']); //分页

		$page=$page==0?1:$page;

		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		$youhui_comment_list = array();
		if($merchant_id > 0){
			$supplier_locationinfo = $GLOBALS['db']->getRow("select name,id,new_dp_count_time from ".DB_PREFIX."supplier_location where id = ".$merchant_id);
			
			syn_supplier_locationcount($supplier_locationinfo);
			
			$condition = " dp.status = 1 and dp.supplier_location_id = ".$merchant_id." ";
			$sql_count = "select count(*) from ".DB_PREFIX."supplier_location_dp dp where ".$condition;
			$total = $GLOBALS['db']->getOne($sql_count);
		
			$page_total = ceil($total/$page_size);
			
			
			
			
			//$root['sql_count'] = $sql_count;
			
			$sql= "select dp.*,u.user_name from ".DB_PREFIX."supplier_location_dp as dp left outer join ".DB_PREFIX."user as u on u.id = dp.user_id  where ".$condition." order by dp.is_top desc, dp.create_time desc limit ".$limit;				
			$root['sql'] = $sql;
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $item){
				$youhui_comment_list[] = array("id"=>$item['id'],
										"merchant_id"=>$item['supplier_location_id'],
										"content"=> trim($item['content']),
										"point"=>intval($item['point']),
										"user_name"=>trim($item['user_name']),
										"create_time"=>$item['create_time'],
										"create_time_format"=>getBeforeTimelag($item['create_time'])
	
				);
			}
		}else{
			$sql_count = "select count(*) from ".DB_PREFIX."message as a ".
			   " left outer join ".DB_PREFIX."youhui as b on b.id = a.rel_id ".
			   " left outer join ".DB_PREFIX."user as c on c.id = a.user_id ";

			$sql = "select a.id, a.rel_id as yh_id, a.content,a.create_time, c.user_name from ".DB_PREFIX."message as a ".			   
				   " left outer join ".DB_PREFIX."user as c on c.id = a.user_id ";
	
			$where = " a.is_effect = 1 and a.rel_table = 'youhui' and a.rel_id = $yh_id";
			$sql_count.=" where ".$where;
			$sql.=" where ".$where." order by a.create_time desc";
			$sql.=" limit ".$limit;
				
			$total = $GLOBALS['db']->getOne($sql_count);
			$page_total = ceil($total/$page_size);
	
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $item){
			/*
				$title = trim($item['youhui_title']);
				if ($title == "" || empty($title)){
					$title = trim($item['m_name']);
				}
	*/
				$youhui_comment_list[] = array("id"=>$item['id'],
									    "yh_id"=>$item['yh_id'],
										//"merchant_id"=>$item['merchant_id'],
										"content"=> trim($item['content']),
										//"youhui_title"=>$title,
										"user_name"=>trim($item['user_name']),
										"create_time"=>$item['create_time'],
										"create_time_format"=>getBeforeTimelag($item['create_time'])
	
				);
			}
		}

		//echo $sql; exit;
		$root['item'] = $youhui_comment_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		$root['merchant_id']=$merchant_id;//fwb add 2014-08-27
		$root['yh_id']=$yh_id;
		$root['page_title']="评论详情";
		$root['city_name']=$city_name;
		output($root);
	}
}
?>
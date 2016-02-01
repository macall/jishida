<?php
//输出接口数据
function output($data)
{
	header("Content-Type:text/html; charset=utf-8");
	$r_type = intval($_REQUEST['r_type']);//返回数据格式类型; 0:base64;1;json_encode;2:array
	$data['act'] = ACT;
	$data['act_2'] = ACT_2;
	//print_r($r_type);exit;
	if($GLOBALS['request']['from']=="wap"){
		//$data['config']=$GLOBALS['m_config'];
		//$data['config']['index_logo']=get_abs_img_root($GLOBALS['m_config']['index_logo']);
		//$data['city_name']=strim($GLOBALS['request']['city_name']);//城市名称
		//$data['email']=addslashes($GLOBALS['request']['email']);//用户名或邮箱

		//var_dump($user_agent);
		if (isios()){
			// $down_url = app_conf("BIZ_APPLE_PATH");
			$down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'ios_down_url'");
		}else{
			//$down_url = app_conf("BIZ_ANDROID_PATH");
			
			$down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'android_filename'");
		}
		
		$data['mobile_btns_download']=$down_url;
		//$data['mobile_btns_download']= get_domain().APP_ROOT.'/../downapp.php';
			
		$data['f_link_data']=get_link_list();
	}
		
	if ($r_type == 0)
	{
		echo base64_encode(json_encode($data));
	}else if ($r_type == 1)
	{
		print_r(json_encode($data));
	}else if ($r_type == 2)
	{
		print_r($data);
	};
	exit;
}


//以下微信支付有调用getMConfig and get_user_has
function get_user_has($key,$value){
	$row=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."user where $key='".$value."'");

	if($row){
		return $row;
	}else{
		return false;
	}
}


function getMConfig(){
	
	
	$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/sjmapi/");
	$m_config = $GLOBALS['cache']->get("m_config_sj");

	if($m_config===false || true)
	{
		$m_config = array();
		$sql = "select code,val from ".DB_PREFIX."m_config";
		$list = $GLOBALS['db']->getAll($sql);
		foreach($list as $item){
			$m_config[$item['code']] = $item['val'];
		}
		$catalog_id = intval($m_config['catalog_id']);
		$event_cate_id = intval($m_config['event_cate_id']);
		$shop_cate_id = intval($m_config['shop_cate_id']);
		
		if ($catalog_id == 0){
			$m_config["catalog_id_name"] = "全部分类";
		}else{
			$m_config["catalog_id_name"] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$catalog_id);
		}
		
		if ($event_cate_id == 0){
			$m_config["event_cate_id_name"] = "全部分类";
		}else{
			$m_config["event_cate_id_name"] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."event_cate where id = ".$event_cate_id);
		}
		
		if ($shop_cate_id == 0){
			$m_config["shop_cate_id_name"] = "全部分类";
		}else{
			$m_config["shop_cate_id_name"] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."shop_cate where id = ".$shop_cate_id);
		}		
		
		/*
		//支付列表
		$sql = "select pay_id as id, code, title as name, has_calc from ".DB_PREFIX."m_config_list where `group` = 1 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$payment_list = array();
		foreach($list as $item){
			$payment_list[] = array("id"=>$item['id'],"code"=>$item['code'],"name"=>$item['name'],"has_calc"=>$item['has_calc']);
		}
		$m_config['payment_list'] = $payment_list;
		*/
		
		$m_config['payment_list'] = array();
		
		//配置方式
		$sql = "select id, id as code, name, 1 as has_calc from ".DB_PREFIX."delivery";			
		$list = $GLOBALS['db']->getAll($sql);
		$delivery_list = array();
		foreach($list as $item){
			$delivery_list[] = array("id"=>$item['id'],"code"=>$item['code'],"name"=>$item['name'],"has_calc"=>$item['has_calc']);
		}
		$m_config['delivery_list'] = $delivery_list;		
		//$order_parm['delivery_list'] = $MConfig['delivery_list'];//$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."delivery");
		
		//发票内容
		$sql = "select id, title as name from ".DB_PREFIX."m_config_list where `group` = 6 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$invoice_list = array();
		foreach($list as $item){
			$invoice_list[] = array("id"=>$item['id'],"name"=>$item['name']);
		}
		$m_config['invoice_list'] = $invoice_list;
		
		//配送日期选择
		$sql = "select code, title as name from ".DB_PREFIX."m_config_list where `group` = 2 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$delivery_time_list = array();
		foreach($list as $item){
			$delivery_time_list[] = array("id"=>$item['code'],"name"=>$item['name']);
		}
		$m_config['delivery_time_list'] = $delivery_time_list;



		//购物车信息提示
		$sql = "select code, title as name,money from ".DB_PREFIX."m_config_list where `group` = 3 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$yh = array();
		foreach($list as $item){
			$yh[] = array("info"=>$item['name'],"money"=>$item['money']);
		}
		$m_config['yh'] = $yh;


		//新闻公告
		$sql = "select code as title, title as content from ".DB_PREFIX."m_config_list where `group` = 4 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$newslist = array();
		foreach($list as $item){
			$newslist[] = array("title"=>$item['title'],"content"=>$item['content']);
		}
		$m_config['newslist'] = $newslist;


		//地址标题
		$sql = "select code, title from ".DB_PREFIX."m_config_list where `group` = 5 and is_verify = 1";
		$list = $GLOBALS['db']->getAll($sql);
		$addrtlist = array();
		foreach($list as $item){
			$addrtlist[] = array("code"=>$item['code'],"title"=>$item['title']);
		}
		$m_config['addr_tlist'] = $addrtlist;

		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/sjmapi/");
		$GLOBALS['cache']->set("m_config_sj",$m_config,3600);
		
	}
	return $m_config;
}


/**
* 过滤SQL查询串中的注释。该方法只过滤SQL文件中独占一行或一块的那些注释。
*
* @access  public
* @param   string      $sql        SQL查询串
* @return  string      返回已过滤掉注释的SQL查询串。
*/
function remove_comment($sql)
{
	/* 删除SQL行注释，行注释不匹配换行符 */
	$sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);

	/* 删除SQL块注释，匹配换行符，且为非贪婪匹配 */
	//$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
	$sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);

	return $sql;
}





function m_toTree($list=null, $pk='id',$pid = 'pid',$child = '_child')
 {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();

            foreach ($list as $key => $data) {
                $_key = is_object($data)?$data->$pk:$data[$pk];
                $refer[$_key] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = is_object($data)?$data->$pid:$data[$pid];
                $is_exist_pid = false;
                foreach($refer as $k=>$v)
                {
                	if($parentId==$k)
                	{
                		$is_exist_pid = true;
                		break;
                	}
                }
                if ($is_exist_pid) {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                } else {
                    $tree[] =& $list[$key];
                }
            }
        }
        return $tree;
 }




//获取所有子集的类
class m_child
{
	public function __construct($tb_name)
	{
		$this->tb_name = $tb_name;
	}
	private $tb_name;
	private $childIds;
	private function _getChildIds($pid = '0', $pk_str='id' , $pid_str ='pid')
	{
		$childItem_arr = $GLOBALS['db']->getAll("select id from ".DB_PREFIX.$this->tb_name." where ".$pid_str."=".$pid);
		if($childItem_arr)
		{
			foreach($childItem_arr as $childItem)
			{
				$this->childIds[] = $childItem[$pk_str];
				$this->_getChildIds($childItem[$pk_str],$pk_str,$pid_str);
			}
		}
	}
	public function getChildIds($pid = '0', $pk_str='id' , $pid_str ='pid')
	{
		$this->childIds = array();
		$this->_getChildIds($pid,$pk_str,$pid_str);
		return $this->childIds;
	}
}



function getAttrArray($id){
	/**
	 *
	 * selected_attr_1: 默认选择属性a中的值
	 * selected_attr_2: 默认选择属性b中的值
	 *
	 * attr_id: 属性a 关键字 (注：可能会作为商品图片中的颜色选择，关联id。比如：选择红色时，就显示红色的商品图片)
	 * attr_name: 属性a 的显示名称如：红色、黄色等等
	 * attr_image: 属性a 的显示小图标
	 *
	 *
	 * 	价格: attr_price_{$attr_1_id}_{$attr_2_id}
	 *	积分：attr_score_{$attr_1_id}_{$attr_2_id}
	 *	购买限制数量：attr_limit_num_{$attr_1_id}_{$attr_2_id}
	 */
	//echo 'aa';exit;
	$attrArray =$GLOBALS['cache']->get("m_goods_attr_".$id);
	if($attrArray === false )
	{

	$sql = "select id,deal_goods_type as goods_type,max_bought,buy_count,current_price as shop_price,return_score as score from ".DB_PREFIX."deal where id = ".intval($id);
	$goods = $GLOBALS['db']->getRow($sql);
	$attrArray = array();

	$attrArray['has_attr_1']=0; //0:无属性; 1:有属性
	$attrArray['has_attr_2']=0; //0:无属性; 1:有属性

	//只取前面2个属性
	$sql = "select id, name from ".DB_PREFIX."goods_type_attr where goods_type_id = ". intval($goods['goods_type'])." order by id asc limit 2";

	$attrlist = $GLOBALS['db']->getAll($sql); //getAllCached
	//print_r($attrlist); exit;
	for ($i = 1; $i <= count($attrlist); $i++){
		$attrArray["has_attr_{$i}"]=1;//无商品属性
		$attrArray["attr_title_{$i}"]=$attrlist[$i - 1]['name']; //商品属性名称如：颜色,尺码
		$attrArray["selected_attr_{$i}"] = 0; //默认选择的属性值id

		//商品属性值：如红色，黄色等等
		$attr_Array = array();
		$sql = "select id, goods_type_attr_id as attr_id, name,price from ".DB_PREFIX."deal_attr where goods_type_attr_id = ".intval($attrlist[$i - 1]['id'])." and deal_id = ".intval($id);
		//echo $sql."<br>";
		$attr_list = $GLOBALS['db']->getAll($sql);
		foreach($attr_list as $value){
			$attr_value = array();
			$attr_value['attr_id'] = $value['id'];//属性值id
			$attr_value['attr_name'] = $value['name']; //属性值名称如：红色，黄色
			$attr_value['attr_image'] = '';//属性值,对应图片

			$attr_value['attr_price'] = floatval($value['price']);//只对下面计算时有效,不作标准返回值
			$attr_value['attr_price_format'] = format_price(floatval($value['price']));
			$attr_Array[] = $attr_value;
		}
		
		if(!$attr_Array){
			$attrArray["true_has_attr_{$i}"]=0;//有商品属性
		}
		$attrArray["attr_{$i}"]=$attr_Array;
	}


	//价格: attr_price_{$attr_1_id}_{$attr_2_id}
	//积分：attr_score_{$attr_1_id}_{$attr_2_id}
	//库存：attr_limit_num_{$attr_1_id}_{$attr_2_id}

	$attr_1_2_value = array();
	if ($attrArray['has_attr_1'] == 1){
	//echo 'aaa';exit;
		for ($i = 1; $i <= count($attrArray['attr_1']); $i++){
			if ($attrArray['has_attr_2'] == 1){
				for ($j = 1; $j <= count($attrArray['attr_2']); $j++){
					$attr_1_2_value["attr_price_".$attrArray['attr_1'][$i-1]['attr_id']."_".$attrArray['attr_2'][$j-1]['attr_id']] = $goods['shop_price'] + $attrArray['attr_1'][$i-1]['attr_price'] + $attrArray['attr_2'][$j-1]['attr_price'];
					$attr_1_2_value["attr_price_".$attrArray['attr_1'][$i-1]['attr_id']."_".$attrArray['attr_2'][$j-1]['attr_id']."_format"] = format_price(floatval($attr_1_2_value["attr_price_".$attrArray['attr_1'][$i-1]['attr_id']."_".$attrArray['attr_2'][$j-1]['attr_id']]));
					$attr_1_2_value["attr_score_".$attrArray['attr_1'][$i-1]['attr_id']."_".$attrArray['attr_2'][$j-1]['attr_id']] = $goods['score'];
					$attr_str = $attrArray['attr_1'][$i-1]['attr_name'].$attrArray['attr_2'][$j-1]['attr_name'];
					$row = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where attr_str = '".$attr_str."'");
					if($row)
					{
						if($row['stock_cfg']>0)
						$max_bought = $row['stock_cfg'] - $row['buy_count'];
						else
						$max_bought = 999;
					}
					else
					{
						if($goods['max_bought']>0)
						$max_bought = $goods['max_bought'] - $goods['buy_count'];
						else
						$max_bought = 999;
					}
					$attr_1_2_value["attr_limit_num_".$attrArray['attr_1'][$i-1]['attr_id']."_".$attrArray['attr_2'][$j-1]['attr_id']] = $max_bought;
				}
			}else{
				$attr_1_2_value["attr_price_".$attrArray['attr_1'][$i-1]['attr_id']."_0"] = $goods['shop_price'] + $attrArray['attr_1'][$i-1]['attr_price'];
				$attr_1_2_value["attr_price_".$attrArray['attr_1'][$i-1]['attr_id']."_0_format"] = format_price(floatval($attr_1_2_value["attr_price_".$attrArray['attr_1'][$i-1]['attr_id']."_0"]));
				$attr_1_2_value["attr_score_".$attrArray['attr_1'][$i-1]['attr_id']."_0"] = $goods['score'];

				$attr_str = $attrArray['attr_1'][$i-1]['attr_name'];
				$row = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where attr_str = '".$attr_str."'");
					if($row)
					{
						if($row['stock_cfg']>0)
						$max_bought = $row['stock_cfg'] - $row['buy_count'];
						else
						$max_bought = 999;
					}
					else
					{
						if($goods['max_bought']>0)
						$max_bought = $goods['max_bought'] - $goods['buy_count'];
						else
						$max_bought = 999;
					}

				$attr_1_2_value["attr_limit_num_".$attrArray['attr_1'][$i-1]['attr_id']."_0"] = $max_bought;
			}
		}
	}

	$attrArray['attr_1_2']= $attr_1_2_value;
	$GLOBALS['cache']->set("m_goods_attr_".$id,$attrArray);
	}

	return	$attrArray;
}

function emptyTag($string)
{
		if(empty($string))
			return "";

		$string = strip_tags(trim($string));
		$string = preg_replace("|&.+?;|",'',$string);

		return $string;
}

function get_abs_img_root($content)
{
	
// 	$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
// 	return str_replace("./public/",$domain."/../public/",$content);
	return format_image_path($content);
	//return str_replace('/mapi/','/',$str);
}
function get_abs_url_root($content)
{
	$content = str_replace("./",get_domain().APP_ROOT."/../",$content);	
	return $content;
}

function getTimeFormat($sysSecond){
	$second = floor($sysSecond % 60);              // 计算秒
	$minite = floor(($sysSecond / 60) % 60);       //计算分
	$hour = floor(($sysSecond / 3600) % 24);       //计算小时
	$day = floor(($sysSecond / 3600) / 24);        //计算天
	if($day > 0){
		return $day."天以上";
	}else{		
		$timeHtml = $hour."小时".$minite."分钟".$second."秒";		
		return $timeHtml;
	}
}


function getGoodsArray($item){
	/**
	 * has_attr: 0:无属性; 1:有属性
	 * 有商品属性在要购买时，要选择属性后，才能购买(用户在列表中点：购买时，要再弹出一个：商品属性选择对话框)

	 * change_cart_request_server:
	 * 编辑购买车商品时，需要提交到服务器端，让服务器端通过一些判断返回一些信息回来(如：满多少钱，可以免运费等一些提示)
	 * 0:提交，1:不提交；
	 *
	 * num_unit: 单位

	 * limit_num: 库存数量
	 *
	 */
	$goods = array();

	$goods['city_name'] = "";
	$goods['goods_id']=$item['id'];
	$goods['title']=emptyTag($item['name']);
	if (empty($item['sub_name'])){
		$goods['sub_name']=emptyTag($item['name']);
	}else{
		$goods['sub_name']=emptyTag($item['sub_name']);
	}
	
	//$goods['image']=get_abs_img_root(make_img($item['img'],0));
	$goods['image']=get_abs_img_root(get_spec_image($item['img'],320,194,1));
	//get_abs_img_root( get_spec_image($v['o_path'],160,0,0));
	$goods['buy_count']=$item['buy_count'];
	$goods['start_date']=$item['begin_time'];
	$goods['end_date']=$item['end_time'];
	$goods['ori_price']=round($item['origin_price'],2);
	$goods['cur_price']=round($item['current_price'],2);
	
	if (empty($item['brief'])){
		$goods['goods_brief'] = $item['name'];
	}else{
		$goods['goods_brief'] = $item['brief'];
	}
	
	$goods['ori_price_format']=format_price($goods['ori_price']);
	$goods['cur_price_format']=format_price($goods['cur_price']);

	$goods['discount']=$item['discount'];
	$sp_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".$item['supplier_id']." order by is_main desc");
	$goods['address']= $sp_info['address'];  // 地址未完

	$goods['num_unit']= "";//$item['num_unit'];
	$limit_num = intval($item['max_bought']);
	//如果库存为0时，不设置为：99999，不限制购买数量;
	if ($limit_num == 0){
		$limit_num = 99999;
	}
	$goods['limit_num']=$limit_num;
	$goods['goods_desc']= $item['description'];
	$goods['is_shop']= $item['is_shop'];
	$goods['is_hot']= $item['is_hot'];
	$goods['notes']= $item['notes'];
	$goods['package']= $item['package'];
	$goods['supplier_location_id']=$GLOBALS['db']->getOne("select location_id from ".DB_PREFIX."deal_location_link where deal_id = ".$item['id']);
	
	$goods['any_refund']=$item['any_refund'];
	$goods['expire_refund']=$item['expire_refund'];
	$goods['service_time']=$item['service_time'];//服务时间
	//标签列表
	//支持随时退款
	$ext_label = array();
	if ($goods['any_refund'] == 1){
		$ext_label[]= array('type'=>0,'ico'=>'','name'=>'支持随时退款');
	}
	//支持过期退款
	if ($goods['expire_refund'] == 1){
		$ext_label[]= array('type'=>1,'ico'=>'','name'=>'支持过期退款');
	}
		
	$goods['ext_label'] = $ext_label;
	
	$goods['time_status']=$item['time_status'];
	$goods['end_time']=$item['end_time'];
	$goods['begin_time']=$item['begin_time'];
	
	$goods['sp_location_id'] = $sp_info['id'];  //供应商信息
	$goods['sp_detail'] = $sp_info['name'];  //供应商信息
	$goods['sp_tel'] = $sp_info['tel']; /*供应商电话*/
	$pattern = "/<img([^>]*)\/>/i";
	$replacement = "<img width=300 $1 />";

	$goods['goods_desc'] = get_abs_img_root(format_html_content_image($goods['goods_desc'], 300));
	$goods['goods_desc'] = preg_replace($pattern, $replacement, $goods['goods_desc']);


	$goods['saving_format']= $item['save_price_format'];

	if($goods['end_date']==0){
		$goods['less_time'] = "none"; //永不过期，无倒计时
		$goods['less_time_format'] = "999天"; //永不过期，无倒计时
	}else{
		$goods['less_time'] = $goods['end_date'] - get_gmtime();		
		if ($goods['less_time'] < 0) $goods['less_time'] = 0;
		$goods['less_time_format'] = getTimeFormat($goods['less_time']);
		
	}
	
	

	$goods['has_attr']=0;//has_attr: 0:无属性; 1:有属性
	
	if ($item['is_delivery']== 1){
		$goods['has_delivery'] = 1;
		$goods['has_mcod'] = 1;
	}else{
		$goods['has_delivery'] = 0;
		$goods['has_mcod'] = 0;
	}

	if ($goods['cart_type'] == 0){
		$goods['has_cart']=1;	//1:可以跟其它商品一起放入购物车购买；0：不能放入购物车，只能独立购买
	}else{
		$goods['has_cart']=0;
	}

	$goods['change_cart_request_server']=1;
	
	$goods['is_refund'] = $item['is_refund']; /*0:不支持退款; 1:支持退款*/
	$goods['avg_point'] = $item['avg_point'];/*购买点评平均分*/

	$goods['distance'] = $item['distance'];
	$goods['attr'] = getAttrArray($item['id']);
	if (intval($goods['attr']['has_attr_1']) > 0 || intval($goods['attr']['has_attr_2']) > 0){
		$goods['has_attr']=1;
	};
	//$goods['share_content']=msubstr($topic['content']).get_domain().str_replace("mapi/","",url("shop","topic",array("id"=>$topic['id'])));
	$goods['share_url']= get_domain().str_replace("sjmapi/","",url("index","deal#".$item['id']));
	$goods['share_content']=emptyTag($item['name']).$goods['share_url'];
		
	return $goods;
}

function user_check($username_email,$pwd)
{
	//$username_email = addslashes($username_email);
	//$pwd = addslashes($pwd);
	if($username_email&&$pwd)
	{
		$sql = "select *,id as uid from ".DB_PREFIX."user where (user_name='".$username_email."' or email = '".$username_email."' or mobile = '".$username_email."') and is_delete = 0";
		$user_info = $GLOBALS['db']->getRow($sql);
		
		$is_use_pass = false;
		if (strlen($pwd) != 32){
			if($user_info['user_pwd']==md5($pwd.$user_info['code']) || $user_info['user_pwd']==md5($pwd)){
				$is_use_pass = true;
			}
		}
		else{
			if($user_info['user_pwd']==$pwd){
				$is_use_pass = true;
			}
		}
		if($is_use_pass)
		{
			return $user_info;
		}
		else
			return null;
	}
	else
	{
		return NULL;
	}
}

/**每个数据项的结构, 结构：
goods_id(int)  //购物车商品的商品ID  $_POST['cartdata'][][goods_id]
num(int)		  //购物车购买的数量   $_POST['cartdata'][][num]
attr_id_a(string)    //购买属性类型1的名称标识。 根据系统不同， 可以是属性类型ID，或名称(如 颜色，尺码)  $_POST['cartdata'][][attr_id_a]
attr_id_b(string)    //购买属性类型2的名称标识。 根据系统不同， 可以是属性类型ID，或名称(如 颜色，尺码)  $_POST['cartdata'][][attr_id_b]
attr_value_a(string) //购买属性1的名称标识。 根据系统不同， 可以是属性类型ID，或名称(如 红色,大码) $_POST['cartdata'][][attr_value_a]
attr_value_b(string) //购买属性2的名称标识。 根据系统不同， 可以是属性类型ID，或名称(如 红色,大码) $_POST['cartdata'][][attr_value_b]
*/
function insertCartData($user_id,$session_id,$cartdata)
{
	$GLOBALS['user_info']['id'] = $user_id;
	require APP_ROOT_PATH.'system/model/deal.php';
	//require APP_ROOT_PATH.'app/Lib/deal.php';

	$res = array('status'=>0,'info'=>'');
	$score_enough=true;
	foreach($cartdata as $key=>$cart)
	{
		/*
		 $cart['goods_id'] = 1;
		$cart['num'] = 1;
		$cart['attr_value_a'] = '红色';
		$cart['attr_value_b'] = '大码';
		$cart['attr_id_a'] = 255;
		$cart['attr_id_b'] = 239;
		[id] => 57
		[goods_id] => 57
		[attr_id_a] => 257
		[attr_id_b] => 259
		[attr_value_a] => 白色
		[attr_value_b] => 170
		[num] => 3
		*/
		//加入每个
		
		//file_put_contents(APP_ROOT_PATH."tmapi/log/".$key.".txt",print_r($cart,true));
				
		$id = intval($cart['goods_id']);
		$check = check_deal_time($id);
		
		if($check['status'] == 0)
		{
			$res['info'] .= $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
			continue;
		}

		$check = check_deal_number($id,$cart['num']);
		if($check['status']==0)
		{
			$res['info'] .= $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']];
			continue;
		}

		$attr_setting_str = $cart['attr_value_a'].$cart['attr_value_b'];

		if($attr_setting_str!='')
		{

			$check = check_deal_number_attr($cart['goods_id'],$attr_setting_str,$cart['num']);
			if($check['status']==0)
			{
				$res['info'] .= $check['info']." ".$GLOBALS['lang']['DEAL_ERROR_'.$check['data']].$check['attr'];
				continue;
			}
		}

		$deal_info = load_auto_cache("deal",array("id"=>$id));
		
		if($deal_info['return_score']<0)
		{
				//需要积分兑换
				$user_score = intval($GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".$user_id));
				if($user_score < abs(intval($deal_info['return_score'])*$cart['num']))
				{			
					$score_enough = false;							
				}
		}
		
		$attr_ids = array();//chenfq by add 2014-08-26
		
		if(intval($cart['attr_id_a'])>0&&intval($cart['attr_id_b'])>0)
		$attr_ids = array(intval($cart['attr_id_a']),intval($cart['attr_id_b']));
		elseif(intval($cart['attr_id_a'])>0)
		$attr_ids = array(intval($cart['attr_id_a']));



		//加入购物车处理，有提交属性， 或无属性时
		$attr_str = '0';
		$attr_name = '';
		$attr_name_str = '';
		if(count($attr_ids)>0)
		{
			$attr_str = implode(",",$attr_ids);
			$attr_names = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_attr where id in(".$attr_str.")");
			$attr_name = '';
			foreach($attr_names as $attr)
			{
				$attr_name .=$attr['name'].",";
				$attr_name_str.=$attr['name'];
			}
			$attr_name = substr($attr_name,0,-1);
		}
		$verify_code = md5($id."_".$attr_str);
		$cart_item = array();

			$attr_price = $GLOBALS['db']->getOne("select sum(price) from ".DB_PREFIX."deal_attr where id in($attr_str)");
			$cart_item['session_id'] = $session_id;
			$cart_item['user_id'] = intval($user_id);
			$cart_item['deal_id'] = $id;
			
			$cart_item['id'] = $cart['id'];//chenfq by add 2014-08-26
			
			//属性
			if($attr_name != '')
			{
				$cart_item['name'] = $deal_info['name']." [".$attr_name."]";
				$cart_item['sub_name'] = $deal_info['sub_name']." [".$attr_name."]";
			}
			else
			{
				$cart_item['name'] = $deal_info['name'];
				$cart_item['sub_name'] = $deal_info['sub_name'];
			}
			$cart_item['name'] = addslashes($cart_item['name']);
			$cart_item['sub_name'] = addslashes($cart_item['sub_name']);
			$cart_item['attr'] = $attr_str;
			$cart_item['unit_price'] = $deal_info['current_price'] + $attr_price;
			$cart_item['number'] = $cart['num'];
			$cart_item['total_price'] = $cart_item['unit_price'] * $cart_item['number'];
			$cart_item['verify_code'] = $verify_code;
			$cart_item['create_time'] = get_gmtime();
			$cart_item['update_time'] = get_gmtime();
			$cart_item['return_score'] = $deal_info['return_score'];
			$cart_item['return_total_score'] = $deal_info['return_score'] * $cart_item['number'];
			$cart_item['return_money'] = $deal_info['return_money'];
			$cart_item['return_total_money'] = $deal_info['return_money'] * $cart_item['number'];
			$cart_item['buy_type']	=	$deal_info['buy_type'];
			$cart_item['supplier_id']	=	$deal_info['supplier_id'];
			$cart_item['service_time']	=	$deal_info['service_time'];
			$cart_item['icon']	=	$deal_info['icon'];
			$cart_item['tech_id']	=$cart['tech_id'];
			$cart_item['attr_str'] = $attr_name_str;
			$cart_list[] = $cart_item;
		//end
	}
	if(!$score_enough)
	{
		$res['info'].= " ".$GLOBALS['lang']['NOT_ENOUGH_SCORE'];	
	}
	$res['data'] = $cart_list;
	$res['status'] = 1;
	return $res;
}



function getUserAddr($user_id,$all,$is_default=0){
	$sql = "select uc.*, r1.name as r1_name, r2.name as r2_name, r3.name as r3_name, r4.name as r4_name from ".DB_PREFIX."user_consignee uc ".
		   "left outer join ".DB_PREFIX."delivery_region as r1 on r1.id = uc.region_lv1 ".
		   "left outer join ".DB_PREFIX."delivery_region as r2 on r2.id = uc.region_lv2 ".
		   "left outer join ".DB_PREFIX."delivery_region as r3 on r3.id = uc.region_lv3 ".
		   "left outer join ".DB_PREFIX."delivery_region as r4 on r4.id = uc.region_lv4 ".
		   "where uc.user_id = ".intval($user_id);
	if($is_default==1){
		$sql.="  and is_default=1 ";
	}
	if ($all){
		$list = $GLOBALS['db']->getAll($sql);
		$addr_list = array();
		foreach($list as $item)
		{
			$addr_list[] = getUserAddrItem($item);
		}
		return $addr_list;
	}else{
		$sql .= " limit 1";
		$addr = $GLOBALS['db']->getRow($sql);
		return getUserAddrItem($addr);
	}
}

function getUserAddrItem($item){
	$addr = array();
	$addr['id'] = $item['id'];//联系人姓名
	$addr['consignee'] = $item['consignee'];//联系人姓名

	//不显示国家
	$addr['delivery'] = $item['r1_name'].$item['r2_name'].$item['r3_name'].$item['r4_name'];

	$addr['region_lv1'] = $item['region_lv1'];//国家
	$addr['region_lv2'] = $item['region_lv2'];//省
	$addr['region_lv3'] = $item['region_lv3'];//城市
	$addr['region_lv4'] = $item['region_lv4'];//地区/县

	$addr['delivery_detail'] = $item['address'];//详细地址
	$addr['phone'] = $item['mobile'];//手机号码
	$addr['postcode'] = $item['zip'];//邮编

	return $addr;
}


//初始化下单时的订单参数
function init_order_parm($MConfig){
	$order_parm = array();

	$order_parm['has_delivery_time'] = 0;//intval($MConfig['has_delivery_time']);//有配送日期选择
	$order_parm['has_ecv'] = 0;//intval($MConfig['has_ecv']);//有优惠券
	$order_parm['has_moblie'] = intval($MConfig['has_moblie']);//有手机号码
	$order_parm['has_invoice'] = 0;//intval($MConfig['has_invoice']);//有发票
	$order_parm['has_message'] = intval($MConfig['has_message']);//有留言框
	$order_parm['has_delivery'] = 0;//1：有配送地区选择项；0：无

	$order_parm['select_payment_id'] = $MConfig['select_payment_id'];//默认支付方式
	$order_parm['select_delivery_time_id'] = $MConfig['select_delivery_time_id'];//默认配送日期

	/**支付方式列表
	 * id: 键值
	* name: 名称
	* code: malipay,支付宝;mtenpay,财付通;mcod,货到付款
	* has_calc: 选择该支付方式，需要重新返回服务器，计算购物车价格; 0:不需要，1:需要

	$payment_list = array();
	$payment_list[] = array("id"=>19,"code"=>"malipay","name"=>"支付宝","has_calc"=>0);
	//$payment_list[] = array("id"=>2,"code"=>"mtenpay","name"=>"财付通","has_calc"=>0);
	$payment_list[] = array("id"=>20,"code"=>"mcod","name"=>"现金支付","has_calc"=>0);
	*/
	
	
	/*online_pay 支付方式：1：在线支付；0：线下支付;2:手机wap;3:手机sdk */
	if ($GLOBALS['request']['from'] == 'wap'){
		//支付列表
		$sql = "select id, class_name as code, name, 1 as has_calc from ".DB_PREFIX."payment where (online_pay = 2 or `class_name` in('Walipay','Wtenpay','Mcod')) and is_effect = 1";
	}else{
		//支付列表
		$sql = "select id, class_name as code, name, 1 as has_calc from ".DB_PREFIX."payment where (online_pay = 3 or `class_name` in('Malipay','Mtenpay','Mcod')) and is_effect = 1";
	}
	
	$list = $GLOBALS['db']->getAll($sql);
	
	$payment_list = array();
	foreach($list as $item){
		$payment_list[] = array("id"=>$item['id'],"code"=>$item['code'],"name"=>$item['name'],"has_calc"=>$item['has_calc']);
	}
	$order_parm['payment_list'] = $payment_list;
	//$order_parm['payment_list'] = $MConfig['payment_list'];	
	
	/**配送日期选择
	 * id: 键值
	* name: 名称

	$delivery_time_list = array();
	$delivery_time_list[] = array("id"=>1,"name"=>"周末");
	$delivery_time_list[] = array("id"=>2,"name"=>"都可以");
	*/
	$order_parm['delivery_time_list'] = $MConfig['delivery_time_list'];
	$order_parm['delivery_list'] = $MConfig['delivery_list'];
	$order_parm['invoice_list'] = $MConfig['invoice_list'];
	
	return $order_parm;
}

function getFeeItem($cart_total){
	$feeinfo[] = array("item"=>"应付总额","value"=>format_price($cart_total['pay_total_price']));


	if ($cart_total['return_total_score'] <> 0){
		if($cart_total['return_total_score']>0)
		{
			$score = "增加".format_score($cart_total['return_total_score']);
		}
		else
		{
			$score = "消费".format_score(abs($cart_total['return_total_score']));
		}
		$feeinfo[] = array("item"=>"积分变动","value"=>$score);
	}

	if ($cart_total['total_price'] > 0){
		$feeinfo[] = array("item"=>"商品总金额","value"=>format_price($cart_total['total_price']));
	}

	if ($cart_total['delivery_fee'] <> 0){
		$feeinfo[] = array("item"=>"运费","value"=>format_price($cart_total['delivery_fee']));
	}

	if ($cart_total['account_money'] <> 0){
		$feeinfo[] = array("item"=>"余额支付","value"=>format_price($cart_total['account_money']));
	}

	if ($cart_total['ecv_money'] <> 0){
		$feeinfo[] = array("item"=>"代金券支付","value"=>format_price($cart_total['ecv_money']));
	}

	if ($cart_total['paid_account_money'] <> 0 || $cart_total['paid_ecv_money'] <> 0){
		$feeinfo[] = array("item"=>"已收金额","value"=>format_price($cart_total['paid_account_money']+$cart_total['paid_ecv_money']));
	}

	$feeinfo[] = array("item"=>"应付金额","value"=>format_price($cart_total['pay_price']));

	return $feeinfo;
}

function get_order_goods($order_info)
{

	/**
	id(int)		//订单ID
	sn(string)	//订单序列号
	create_time(int)		//下单时间
	create_time_format(string)	//下单时间格式化
	total_money(float)		//订单总金额
	money(float)		//剩余应付金额
	total_money_format(string)  //订单总金额格式化
	money_format(string)		//剩余应付金额格式化
	status(string)		//订单状态(包含 付款状态与配送状态的文字描述)
	num(int)		//订单商品总量
	orderGoods(Array<HashMap>)		//订单商品
	HashMap结构，订单商品结构

		id(int)		//订单商品数据表ID
		goods_id(int)		//商品原ID
		name(string)		//商品名称
		num(int)			//商品数量
		price(float)		//单价
		price_format(string)		//格式化单价
		total_money(float)	//商品总价
		total_money_format(string)	//商品总价格式化
		image(string)		//商品缩略图片
		attr_content(string)	//商品属性描述
	*/
	$data['id'] = $order_info['id'];
	$data['sn'] = $order_info['order_sn'];
	$data['create_time'] = $order_info['create_time'];
	$data['create_time_format'] = to_date($order_info['create_time']);
	$data['total_money'] = $order_info['total_price'];
	$data['money'] = $order_info['total_price'] - $order_info['pay_amount'];
	$data['total_money_format'] = format_price($order_info['total_price']);
	$data['money_format'] = format_price($data['money']);
    $data['order_status'] = $order_info['order_status'];
	$data['status'] = "";
        $data['delivery_status_code'] = $order_info['delivery_status'];
        $data['pay_status'] = $order_info['pay_status'];
    $data['technician_id'] = $order_info['technician_id'];
	$data['service_start_time'] = $order_info['service_start_time'];
	$data['service_time'] = $order_info['service_time'];  
	if($order_info['pay_status']==0)
	$data['status'].="未付款";
	elseif($order_info['pay_status']==1)
	$data['status'].="部份付款";
	else
	$data['status'].="全部付款";

	if($order_info['delivery_status']==0)
	$data['delivery_status'].="未发货";
	elseif($order_info['delivery_status']==2)
	$data['status'].="已发货";
	else
	$data['status'].="";

	
	require_once APP_ROOT_PATH."system/model/deal_order.php";
	$order_item_table = get_user_order_item_table_name($order_info['user_id']);
	
	$data['num'] =  $GLOBALS['db']->getOne("select sum(number) from ".$order_item_table." where order_id = ".$order_info['id']);
	$goods_list = $GLOBALS['db']->getAll("select * from ".$order_item_table." where order_id = ".$order_info['id']);
	
	foreach($goods_list as $order_goods)
	{
		$goods_item = array();
		$goods_item['id'] = $order_goods['id'];
		$goods_item['goods_id'] = $order_goods['deal_id'];
		$goods_item['name'] = $order_goods['name'];
		$goods_item['num'] = $order_goods['number'];
		$goods_item['price'] = $order_goods['unit_price'];
		$goods_item['price_format'] =format_price($order_goods['unit_price']);
		$goods_item['total_money'] = $order_goods['total_price'];
		$goods_item['total_money_format'] = format_price($order_goods['total_price']);
		$goods_item['service_time'] = $order_goods['service_time'];
		if(preg_match("/\[([^\]]+)\]/i",$order_goods['name'],$matches))
		$goods_item['attr_content'] = $matches[1];
		else
		$goods_item['attr_content'] = "";
		$image = $GLOBALS['db']->getOne("select img from ".DB_PREFIX."deal where id = ".$goods_item['goods_id']);

		//$goods_item['image'] = get_abs_img_root(make_img($image,0));
		$goods_item['image']=get_abs_img_root(get_spec_image($image,160,160,0));
		$data['orderGoods'][] = $goods_item;
	}
	
	$coupon_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where order_id = ".$order_info['id']);
	foreach($coupon_list as $coupon)
	{
		$coupon_item = array();
		$coupon_item['id'] = $coupon['id'];
		$coupon_item['sn'] = $coupon['sn'];
		$coupon_item['password'] = $coupon['password'];
		if ($coupon['confirm_time'] == 0){
			$coupon_item['status_format'] = '未消费';
			if ($coupon['end_time'] == 0){
				$coupon_item['status_format'] .= '(不限期)';
			}else{
				$coupon_item['status_format'] .= '('.to_date($coupon['end_time'],'Y-m-d').')';
			}
		}else{
			$coupon_item['status_format'] = '已消费('.to_date($coupon['confirm_time'],'Y-m-d').')';
		}
		
		$data['coupon_list'][] = $coupon_item;
	}
	
	return $data;

}

function get_order_goods2($order_info)
{

	/**
	id(int)		//订单ID
	sn(string)	//订单序列号
	create_time(int)		//下单时间
	create_time_format(string)	//下单时间格式化
	total_money(float)		//订单总金额
	money(float)		//剩余应付金额
	total_money_format(string)  //订单总金额格式化
	money_format(string)		//剩余应付金额格式化
	status(string)		//订单状态(包含 付款状态与配送状态的文字描述)
	num(int)		//订单商品总量
	orderGoods(Array<HashMap>)		//订单商品
	HashMap结构，订单商品结构

		id(int)		//订单商品数据表ID
		goods_id(int)		//商品原ID
		name(string)		//商品名称
		num(int)			//商品数量
		price(float)		//单价
		price_format(string)		//格式化单价
		total_money(float)	//商品总价
		total_money_format(string)	//商品总价格式化
		image(string)		//商品缩略图片
		attr_content(string)	//商品属性描述
	*/
	$data['id'] = $order_info['id'];
	$data['sn'] = $order_info['order_sn'];
	$data['create_time'] = $order_info['create_time'];
	$data['create_time_format'] = to_date($order_info['create_time']);
	$data['total_money'] = $order_info['total_price'];
	$data['money'] = $order_info['total_price'] - $order_info['pay_amount'];
	$data['total_money_format'] = format_price($order_info['total_price']);
	$data['money_format'] = format_price($data['money']);
    $data['order_status'] = $order_info['order_status'];
	$data['status'] = "";
        $data['delivery_status_code'] = $order_info['delivery_status'];
        $data['pay_status'] = $order_info['pay_status'];
    $data['technician_id'] = $order_info['technician_id'];
	$data['service_start_time'] = $order_info['service_start_time'];
	$data['service_time'] = $order_info['service_time'];  
	if($order_info['pay_status']==0)
	$data['status'].="未付款";
	elseif($order_info['pay_status']==1)
	$data['status'].="部份付款";
	else
	$data['status'].="全部付款";

	if($order_info['delivery_status']==0)
	$data['delivery_status'].="未发货";
	elseif($order_info['delivery_status']==2)
	$data['status'].="已发货";
	else
	$data['status'].="";

	
	require_once APP_ROOT_PATH."system/model/deal_order.php";
	$order_item_table = get_user_order_item_table_name($order_info['user_id']);
	$order_item_table=DB_PREFIX."deal_order_item";
	$data['num'] =  $GLOBALS['db']->getOne("select sum(number) from ".$order_item_table." where order_id = ".$order_info['id']);
	$goods_list = $GLOBALS['db']->getAll("select * from ".$order_item_table." where order_id = ".$order_info['id']);
	
	foreach($goods_list as $order_goods)
	{
		$goods_item = array();
		$goods_item['id'] = $order_goods['id'];
		$goods_item['goods_id'] = $order_goods['deal_id'];
		$goods_item['name'] = $order_goods['name'];
		$goods_item['num'] = $order_goods['number'];
		$goods_item['price'] = $order_goods['unit_price'];
		$goods_item['price_format'] =format_price($order_goods['unit_price']);
		$goods_item['total_money'] = $order_goods['total_price'];
		$goods_item['total_money_format'] = format_price($order_goods['total_price']);
		$goods_item['service_time'] = $order_goods['service_time'];
		if(preg_match("/\[([^\]]+)\]/i",$order_goods['name'],$matches))
		$goods_item['attr_content'] = $matches[1];
		else
		$goods_item['attr_content'] = "";
		$image = $GLOBALS['db']->getOne("select img from ".DB_PREFIX."deal where id = ".$goods_item['goods_id']);

		//$goods_item['image'] = get_abs_img_root(make_img($image,0));
		$goods_item['image']=get_abs_img_root(get_spec_image($image,160,160,0));
		$data['orderGoods'][] = $goods_item;
	}
	
	$coupon_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_coupon where order_id = ".$order_info['id']);
	foreach($coupon_list as $coupon)
	{
		$coupon_item = array();
		$coupon_item['id'] = $coupon['id'];
		$coupon_item['sn'] = $coupon['sn'];
		$coupon_item['password'] = $coupon['password'];
		if ($coupon['confirm_time'] == 0){
			$coupon_item['status_format'] = '未消费';
			if ($coupon['end_time'] == 0){
				$coupon_item['status_format'] .= '(不限期)';
			}else{
				$coupon_item['status_format'] .= '('.to_date($coupon['end_time'],'Y-m-d').')';
			}
		}else{
			$coupon_item['status_format'] = '已消费('.to_date($coupon['confirm_time'],'Y-m-d').')';
		}
		
		$data['coupon_list'][] = $coupon_item;
	}
	
	return $data;

}

/**
* 获取指定时间与当前时间的时间间隔
*
* @access  public
* @param   integer      $time
*
* @return  string
*/
function getBeforeTimelag($time)
{
	if($time == 0)
	return "";

	static $today_time = NULL,
	$before_lang = NULL,
	$beforeday_lang = NULL,
	$today_lang = NULL,
	$yesterday_lang = NULL,
	$hours_lang = NULL,
	$minutes_lang = NULL,
	$months_lang = NULL,
	$date_lang = NULL,
	$sdate = 86400;

	if($today_time === NULL)
	{
		$today_time = get_gmtime();
		$before_lang = '前';//lang('time','before');
		$beforeday_lang = '前天';//lang('time','beforeday');
		$today_lang = '今天';//lang('time','today');
		$yesterday_lang = '昨天';//lang('time','yesterday');
		$hours_lang = '小时';//lang('time','hours');
		$minutes_lang = '分钟';//lang('time','minutes');
		$months_lang = '月';//lang('time','months');
		$date_lang = '日';//lang('time','date');
	}

	$now_day = to_timespan(to_date($today_time,"Y-m-d")); //今天零点时间
	$pub_day = to_timespan(to_date($time,"Y-m-d")); //发布期零点时间

	$timelag = $now_day - $pub_day;

	$year_time = to_date($time,'Y');
	$today_year = to_date($today_time,'Y');

	if($year_time < $today_year)
		return to_date($time,'Y:m:d H:i');

	$timelag_str = to_date($time,' H:i');

	$day_time = 0;
	if($timelag / $sdate >= 1)
	{
		$day_time = floor($timelag / $sdate);
		$timelag = $timelag % $sdate;
	}

	switch($day_time)
	{
		case '0':
			$timelag_str = $today_lang.$timelag_str;
			break;

		case '1':
			$timelag_str = $yesterday_lang.$timelag_str;
			break;

		case '2':
			$timelag_str = $beforeday_lang.$timelag_str;
			break;

		default:
			$timelag_str = to_date($time,'m'.$months_lang.'d'.$date_lang.' H:i');
		break;
	}
	return $timelag_str;
}
//优惠券信息
function m_youhuiLogItem($item){
	$is_sc = intval($item['is_sc']);
	if ($is_sc > 0) $is_sc = 1;//1:已收藏; 0:未收藏

	if (intval($item['begin_time']) > 0 && intval($item['end_time'])){
		$days = round(($item['end_time']-$item['begin_time'])/3600/24);
		if ($days < 0){
			$ycq = to_date($item['begin_time'],'Y-m-d').'至'.to_date($item['end_time'],'Y-m-d').',已过期';
		}else{
			$ycq = to_date($item['begin_time'],'Y-m-d').'至'.to_date($item['end_time'],'Y-m-d').',还有'.$days.'天';
		}
	}else{
		$ycq = '';
	}
	if(!empty($item['confirm_time']))
		$status = 1;
	else
		$status = 0;	
	return array("id"=>$item['id'],
									"title"=>$item['title'],
									"logo"=> get_abs_img_root($item['image_1']),
									//"logo_1"	=>	get_abs_img_root($item['image_2']),
									//"logo_2"	=>	get_abs_img_root($item['image_3']),
                                    //"image_3_w"=>intval($item['image_3_w']),
                                    //"image_3_h"=>intval($item['image_3_h']),
											"merchant_logo"=> get_abs_img_root($item['merchant_logo']),
											"create_time"=>$item['create_time'],
											"create_time_format"=>getBeforeTimelag($item['create_time']),
											"yl_create_time"=>$item['yl_create_time'],
											"yl_create_time_format"=>getBeforeTimelag($item['yl_create_time']),
											"yl_confirm_time"=>$item['yl_confirm_time'],
											"yl_confirm_time_format"=>getBeforeTimelag($item['yl_confirm_time']),
											"yl_sn"=>$item['yl_sn'],
											//"xpoint"=>$item['xpoint'],
											//"ypoint"=>$item['ypoint'],
											//"address"=>$item['api_address'],
											"content"=>preg_replace('/\.\//i',"http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].'/',$item['content']),
									"is_sc"=>$is_sc,
									'info'=>'您的优惠券验证码为:'.$item['yl_sn'],
								//	"distance" => $item['distance'],
									//"comment_count"=>intval($item['comment_count']),
									//"merchant_id"=>intval($item['merchant_id']),
									"begin_time_format"=>to_date($item['begin_time'],'Y-m-d'),
									"begin_time"=>$item['begin_time'],
									"end_time"=>to_date($item['end_time'],'Y-m-d'),
									"used"=>$status
									//"end_time_format"=>to_date($item['end_time'],'Y-m-d'),
									//"ycq"=>$ycq,
									//"adv_url"=>$item['url'],
									//"city_name"=>$item['city_name']

	);
}
function m_youhuiItem($item){
	$is_sc = intval($item['is_sc']);
	if ($is_sc > 0) $is_sc = 1;//1:已收藏; 0:未收藏

	if (intval($item['begin_time']) > 0 && intval($item['end_time'])){
		$days = round(($item['end_time']-get_gmtime())/3600/24);
		if ($days < 0){
			$ycq = to_date(get_gmtime(),'Y-m-d').'至'.to_date($item['end_time'],'Y-m-d').',已过期';
		}else{
			$ycq = to_date(get_gmtime(),'Y-m-d').'至'.to_date($item['end_time'],'Y-m-d').',还有'.$days.'天';
		}
	}else{
		$ycq = '';
	}
	$logo=get_spec_image($item['image_1'],$width=160,$height=0,$gen=0,$is_preview=true);
	$merchant_logo=get_spec_image($item['merchant_logo'],$width=160,$height=0,$gen=0,$is_preview=true);
	//$pattern = "/<img([^>]*)\/>/i";
	$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/i";
	//$replacement = "<img width=300 $1 />";
	$replacement = "<img src='$1' width='300' />";
	
	
	$item['content'] = preg_replace($pattern, $replacement, $item['content']);
	return array("id"=>$item['id'],
									"title"=>$item['title'],
									"logo"=> $item['image_1'],
									"logo_1"	=>	get_abs_img_root($item['image_2']),
									"logo_2"	=>	get_abs_img_root($item['image_3']),
                                    "image_3_w"=>intval($item['image_3_w']),
                                    "image_3_h"=>intval($item['image_3_h']),
											"merchant_logo"=> get_abs_img_root(get_spec_image($item['merchant_logo'],160,0)),
											"create_time"=>$item['create_time'],
											"create_time_format"=>getBeforeTimelag($item['create_time']),
											"xpoint"=>$item['xpoint'],
											"ypoint"=>$item['ypoint'],
											"address"=>$item['api_address'],
											"content"=>get_abs_img_root($item['content']),
									"is_sc"=>$is_sc,
									"distance" => round($item['distance']),
									"comment_count"=>intval($item['comment_count']),
									"merchant_id"=>intval($item['merchant_id']),
									"view_count"=>intval($item['view_count']),
									"sms_count"=>intval($item['sms_count']),
									"print_count"=>intval($item['print_count']),
									"youhui_type"=>intval($item['youhui_type']),
									"total_num"=>intval($item['total_num']),
									"use_notice"=>$item['use_notice'],
									"begin_time_format"=>to_date($item['begin_time'],'Y-m-d'),
									"end_time_format"=>to_date($item['end_time'],'Y-m-d'),
									"ycq"=>$ycq,
									"adv_url"=>$item['url'],
									"city_name"=>$item['city_name']

	);
	
}

function m_merchantItem($item){

	$is_dy = intval($item['is_dy']);
	if ($is_dy > 0) $is_dy = 1;
	$supplier_name=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id=".intval($item['supplier_id']));
	
	$logo=get_spec_image($item['logo'],$width=160,$height=0,$gen=0,$is_preview=true);
	$item['width']=$item['avg_point'] > 0 ? ($item['avg_point'] / 5) * 100 : 0;
	
	$group_point = unserialize($item['dp_group_point']);
	if ($group_point === false){
		$group_point = array();
	}
	
	return array("id"=>$item['id'],
								    "name"=>$item['name'],
									"avg_point"=>$item['avg_point'],
									"logo"=> get_abs_img_root($logo),
									"xpoint"=>$item['xpoint'],
									"ypoint"=>$item['ypoint'],
									"api_address"=>$item['api_address'],
									"address"	=>	$item['address'],
									"dp_count"	=>	$item['dp_count'],
									"avg_point"	=>	$item['avg_point'],
									"good_rate"	=>	$item['good_rate'],
									"deal_cate_id"	=>	$item['deal_cate_id'],			
									"tel"=>$item['tel'],
									"group_point"=> $group_point,
									"is_dy"=>$is_dy,
									"city_name"=>$item['city_name'],
									"city_id"=>$item['city_id'],
									"mobile_brief"=>$item['mobile_brief'],
									"comment_count"=>intval($item['comment_count']),
									"event_count"=>intval($item['event_count']),
									"youhui_count"=>intval($item['youhui_count']),
									"brand_id"=>intval($item['brand_id']),
									"distance"=>round($item['distance']),
									"brief"	=>	get_abs_url_root($item['brief']),
									"width"=>round($item['width'],1),
									"supplier_name"=>$supplier_name,
									"supplier_id"=>$item['supplier_id'],
	);
}


function m_adv_youhui($city_id){

return array();

}

function get_parse_expres($cnt)
{
	$expression_replace_array = $GLOBALS['cache']->get("MOBILE_EXPRESSION_REPLACE_ARRAY");
	if($expression_replace_array===false)
	{
		require_once APP_ROOT_PATH."system/utils/es_image.php";
		$result = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."expression");
		foreach($result as $item)
		{
			$img_info = es_image::getImageInfo(APP_ROOT_PATH."public/expression/".$item['type']."/".$item['filename']);
			$expression_replace_array[$item['emotion']] = array(
				"key" => $item['emotion'],
				"value" =>  get_abs_img_root("./public/expression/".$item['type']."/".$item['filename']),
				"width"	=>	$img_info['width'],
				"height"	=>	$img_info['height']
			);		
		}
		$GLOBALS['cache']->set("MOBILE_EXPRESSION_REPLACE_ARRAY",$expression_replace_array);
	}
	$result = array();
	if(preg_match_all("/\[[^\]]+\]/i",$cnt,$matches))
	{
		$matches[0] = array_unique($matches[0]);
		foreach($matches[0] as $key)
		{
			if(!empty($expression_replace_array[$key]))
				$result[] = $expression_replace_array[$key];
		}		
	}
		$result[] = $expression_replace_array['[爱心]'];
	return $result;	
}

function get_parse_user($cnt)
{
	$result = array();
	$name_count = preg_match_all("/@([^\f\n\r\t\v: ]+)/i",$cnt,$name_matches);
	if($name_count > 0)
	{
		$name_matches[1] = array_unique($name_matches[1]);
		foreach($name_matches[1] as $k=>$user_name)
		{				
			$uinfo = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."user where user_name = '".$user_name."' and is_effect = 1 and is_delete = 0");			
			if($uinfo)
			{
				$result[] = array("key"=>$user_name,"value"=>$uinfo['id']);					
			}
		}
			
	}
	return $result;		
}

function get_muser_avatar($id,$type)
{
	$uid = sprintf("%09d", $id);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$path = $dir1.'/'.$dir2.'/'.$dir3;
				
	$id = str_pad($id, 2, "0", STR_PAD_LEFT); 
	$id = substr($id,-2);
	$avatar_file = "./public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";
	$avatar_check_file = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";

	if(file_exists($avatar_check_file))	
	return $avatar_file;
	else
	return "./public/avatar/noavatar_".$type.".gif";
}

function m_get_topic_reply($topic_id,$page)
{
	
	if($page==0)
	$page = 1;
	$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;		
			
	$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."topic_reply where topic_id = ".$topic_id." and is_effect = 1 and is_delete = 0 order by create_time asc limit ".$limit);		
	$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_reply where topic_id = ".$topic_id." and is_effect = 1 and is_delete = 0");
	$reply_list = array();
	foreach($list as $k=>$v)
	{
		$reply_list[$k]['comment_id'] = $v['id'];
		$reply_list[$k]['share_id'] = $v['topic_id'];
		$reply_list[$k]['uid'] = $v['user_id'];
		$reply_list[$k]['parent_id'] = $v['reply_id'];
		$reply_list[$k]['content'] = $v['content'];
		$reply_list[$k]['create_time'] = $v['create_time'];
		$reply_list[$k]['user_name'] = $v['user_name'];
		$reply_list[$k]['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"big"));
		$reply_list[$k]['time'] = pass_date($v['create_time']);
		$reply_list[$k]['parse_expres'] = get_parse_expres($v['content']);
        $reply_list[$k]['parse_user'] = get_parse_user($v['content']);
		
	}	
	$page_info = array("page"=>$page,"page_total"=>ceil($count/PAGE_SIZE));
	return array("list"=>$reply_list,"page"=>$page_info,"page_size"=>PAGE_SIZE);	
}


function m_get_event_reply($event_id,$page)
{
	if($page==0)
	$page = 1;
	$limit = (($page-1)*PAGE_SIZE).",".PAGE_SIZE;		
	require_once APP_ROOT_PATH."system/model/review.php";
	
	//get_dp_list($limit,$param=array("deal_id"=>0,"youhui_id"=>0,"event_id"=>0,"location_id"=>0,"tag"=>""),$where="",$orderby="")
	//$res = get_message_list_shop($limit," rel_table='event' and rel_id = ".$event_id." and is_effect = 1");
	//get_dp_list($limit,$param=array("deal_id"=>0,"youhui_id"=>0,"event_id"=>0,"location_id"=>0,"tag"=>""),$where="",$orderby="")
	$res = get_dp_list($limit,array("deal_id"=>0,"youhui_id"=>0,"event_id"=>$event_id,"location_id"=>0,"tag"=>""),"is_effect = 1");
	$list = $res['list'];
	
	$condition = $res['condition'];
	
	$sql = "select count(*) from ".DB_PREFIX."supplier_location_dp where  ".$condition;	
	$count =  $GLOBALS['db']->getOne($sql);
	
	$reply_list = array(); 
	foreach($list as $k=>$v)
	{
		$reply_list[$k]['content'] = $v['content'];
		$reply_list[$k]['create_time'] = $v['create_time_format'];
		$reply_list[$k]['user_name'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".$v['user_id']);
		$reply_list[$k]['user_avatar'] = get_abs_img_root(get_muser_avatar($v['user_id'],"big"));
		$reply_list[$k]['time'] = pass_date($v['create_time_format']);
		$reply_list[$k]['parse_expres'] = get_parse_expres($v['content']);
        $reply_list[$k]['parse_user'] = get_parse_user($v['content']);        
		$reply_list[$k]['user_id'] =$v['user_id'];
	}	
	$page_info = array("page"=>$page,"page_total"=>ceil($count/PAGE_SIZE),"page_size"=>PAGE_SIZE);
	return array("list"=>$reply_list,"page"=>$page_info);	
}

function m_get_topic_fav($topic_id)
{
	$list = $GLOBALS['db']->getAll("select user_id as uid,user_name from ".DB_PREFIX."topic where fav_id = ".$topic_id." order by create_time desc limit 20");
	foreach($list as $k=>$v)
	{
		$list[$k]['user_avatar'] = get_abs_img_root(get_muser_avatar($v['uid'],"big"));
	}
	return $list;
}

function m_get_topic_img($topic)
{
	$images = $GLOBALS['db']->getAll("select path,o_path,width,height,id from ".DB_PREFIX."topic_image where topic_id = ".$topic['id']);
	$image_list = array();
	foreach($images as $k=>$v)
	{
		$image_list[$k]['share_id'] = $topic['id'];
		$image_list[$k]['id'] = $v['id'];
		$image_list[$k]['img'] = get_abs_img_root(get_spec_image($v['o_path'],320,0,0));
		$image_list[$k]['small_img'] = get_abs_img_root(get_spec_image($v['o_path'],160,0,0));
		$image_list[$k]['type'] = "m";
		$image_list[$k]['img_width'] = $v['width'];
		$image_list[$k]['img_height'] = $v['height'];
		if($k==0)
		{
			$group = $topic['topic_group'];
			if(file_exists(APP_ROOT_PATH."system/fetch_topic/".$group."_fetch_topic.php"))
			{
				require_once APP_ROOT_PATH."system/fetch_topic/".$group."_fetch_topic.php";
				$class_name = $group."_fetch_topic";
				if(class_exists($class_name))
				{
					$fetch_obj = new $class_name;
					$topic = $fetch_obj->decode_mobile($topic);
					$image_list[$k]['type'] = $topic['type'];
					$image_list[$k]['data_id'] = $topic['group_data']['data']['id'];
					$image_list[$k]['data_name'] = $topic['group_data']['data']['name'];
					$image_list[$k]['price_format'] =  "￥".round($topic['group_data']['data']['current_price'],2);					
				}
			}	
		}
	}
	return $image_list;
}

function m_get_topic_list_img($topic)
{
	$images = $GLOBALS['db']->getAll("select path,o_path,width,height,id from ".DB_PREFIX."topic_image where topic_id = ".$topic['id']." limit 3");
	$image_list = array();
	foreach($images as $k=>$v)
	{		
		$image_list[$k]['share_id'] = $topic['id'];
		$image_list[$k]['id'] = $v['id'];
		$image_list[$k]['img'] = get_abs_img_root($v['o_path']);
		$image_list[$k]['small_img'] = get_abs_img_root( get_spec_image($v['o_path'],160,0,0));
		$image_list[$k]['type'] = "m";
		$image_list[$k]['img_width'] = $v['width'];
		$image_list[$k]['img_height'] = $v['height'];
		$image_list[$k]['width'] = 160;
		if($k==0)
		{
			$group = $topic['topic_group'];
			if(file_exists(APP_ROOT_PATH."system/fetch_topic/".$group."_fetch_topic.php"))
			{
				require_once APP_ROOT_PATH."system/fetch_topic/".$group."_fetch_topic.php";
				$class_name = $group."_fetch_topic";
				if(class_exists($class_name))
				{
					$fetch_obj = new $class_name;
					$topic = $fetch_obj->decode_mobile($topic);
					$image_list[$k]['type'] = $topic['type'];
					$image_list[$k]['data_id'] = $topic['group_data']['data']['id'];
					$image_list[$k]['data_name'] = $topic['group_data']['data']['name'];
					$image_list[$k]['price_format'] =  "￥".round($topic['group_data']['data']['current_price'],2);					
				}
			}	
		}
		
	}
	return $image_list;
}

function m_get_topic_item($topic)
{
		$share_item['share_id'] = $topic['id'];
		$share_item['uid'] = $topic['user_id'];
		$share_item['user_name'] = $topic['user_name'];
		if($topic['fav_id']>0)
		{
			$share_item['content'] = "我喜欢这个，谢谢你的分享[爱心]";	
		}
		else
		$share_item['content'] = $topic['content'];	
		$share_item['share_content'] =  msubstr($topic['content']).get_domain().str_replace("sjmapi/","",url("index","topic",array("id"=>$topic['id'])));	
		$share_item['collect_count'] = $topic['fav_count'];	
		$share_item['comment_count'] = $topic['reply_count'];
		$share_item['relay_count'] = $topic['relay_count'];
		$share_item['click_count'] = $topic['click_count'];
		$share_item['title'] = $topic['title'];
		$share_item['type'] = 'default';
        $share_item['share_data'] ='photo';
        if($topic['source_type']==0)
        $source_name = "来自".app_conf("SHOP_TITLE").$topic['source_name'];
        else
        $source_name = "来自".$topic['source_name'];
        $share_item['source'] = $source_name;
        $share_item['time'] =  pass_date($topic['create_time']);
        $share_item['parse_expres'] = get_parse_expres($topic['content']);
        $share_item['parse_user'] = get_parse_user($topic['content']);
        $share_item['user_avatar'] =	get_abs_img_root(get_muser_avatar($topic['user_id'],"big"));
        $share_item['imgs'] = m_get_topic_list_img($topic);
        if($topic['fav_id']>0||$topic['relay_id']>0)
        $share_item['is_relay'] = 1;
        
        $share_item['user'] = array("uid"=>$topic['user_id'],"user_name"=>$topic['user_name'],"user_avatar"=>$share_item['user_avatar']);
        
        return $share_item;
	
}


function m_search_event_list($limit, $cate_id=0, $city_id=0, $where='',$orderby = '',$field_append="")
{		

			
			$count_sql = "select count(*) from ".DB_PREFIX."event " ;
			$sql = "select * $field_append from ".DB_PREFIX."event ";

	
			$count_sql .= " where is_effect = 1 ";
			$sql .= " where is_effect = 1  ";
			
			if($cate_id>0)
			{
				
				$sql .= " and cate_id = ".$cate_id." ";
				$count_sql .= " and cate_id = ".$cate_id." ";
			}
				
			if($city_id==0)
			{
				require_once APP_ROOT_PATH."system/model/city.php";
				$city = City::locate_city();
				$city_id = $city['id'];
			}

			if($city_id>0)
			{			
				$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
				if($ids)
				{
				$sql .= " and city_id in (".implode(",",$ids).")";
				$count_sql .= " and city_id in (".implode(",",$ids).")";
				}
			}
			
			$merchant_id = intval($GLOBALS['request']['merchant_id']);
			if($merchant_id>0)
			{
				$event_ids = $GLOBALS['db']->getOne("select group_concat(event_id) from ".DB_PREFIX."event_location_link where location_id = ".$merchant_id);
				if($event_ids)
				{
					$sql .= " and id in (".$event_ids.")";
					$count_sql .= " and id in (".$event_ids.")";
				}
				else
				{
					$sql .= " and id = 0 ";
					$count_sql .= " and id = 0 ";
				}
			}
			
			if($where != '')
			{
				$sql.=" and ".$where;
				$count_sql.=" and ".$where;
			}
			
			if($orderby=='')
			$sql.=" order by is_recommend desc,sort desc,id desc limit ".$limit;
			else
			$sql.=" order by ".$orderby." limit ".$limit;
						
			$events = $GLOBALS['db']->getAll($sql);				
			$events_count = $GLOBALS['db']->getOne($count_sql);
			
			$res = array('list'=>$events,'count'=>$events_count);	
		
		return $res;
}



/**
 * 获取正在团购的产品列表
 */
function m_get_deal_list($limit,$cate_id=0,$city_id=0, $type=array(DEAL_ONLINE,DEAL_HISTORY,DEAL_NOTICE), $where='',$orderby = '' , $quan_id=0,$field_append="",$cata_type_id=0)
{		
		$time = get_gmtime();
		$time_condition = ' and buy_type = 0 and publish_wait = 0 and is_shop = 0 and ( 1<>1 ';
		if(in_array(DEAL_ONLINE,$type))
		{			
			//进行中的团购
			$time_condition .= " or ((".$time.">= begin_time or begin_time = 0) and (".$time."< end_time or end_time = 0) and buy_status <> 2) ";
		}
		
		if(in_array(DEAL_HISTORY,$type))
		{
			//往期团购
			$time_condition .= " or ((".$time.">=end_time and end_time <> 0) or buy_status = 2) ";
		}
		if(in_array(DEAL_NOTICE,$type))
		{			
			//预告
			$time_condition .= " or ((".$time." < begin_time and begin_time <> 0 and notice = 1)) ";
		}
		
		$time_condition .= ')';
		
			$count_sql = "select count(*) from ".DB_PREFIX."deal where is_effect = 1 and is_delete = 0 ".$time_condition;
			$sql = "select * $field_append from ".DB_PREFIX."deal where is_effect = 1 and is_delete = 0 ".$time_condition;
			if($cate_id>0)
			{
				$sql .= " and cate_id =".$cate_id."";
				$count_sql .= " and cate_id =".$cate_id."";
				if($cata_type_id >0)
				{		
					$deal_type_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate_type where id = ".$cata_type_id);
					$deal_type_name_unicode = str_to_unicode_string($deal_type_name);

					$sql .= " and (match(deal_cate_match) against('".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
					$count_sql .= " and (match(deal_cate_match) against('".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";

				}
			}
			
			
			
			if($city_id==0)
			{
				//$city = get_current_deal_city();//
				$city_id = $city['id'];
			}
			if($city_id>0)
			{			
				$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
				if($ids)
				{
				$sql .= " and city_id in (".implode(",",$ids).")";
				$count_sql .= " and city_id in (".implode(",",$ids).")";
				}
			}
		
		if($quan_id > 0)
		{
			$ids = load_auto_cache("deal_quan_ids",array("quan_id"=>$quan_id));
			$quan_list = $GLOBALS['db']->getAll("select `name` from ".DB_PREFIX."area where id in (".implode(",",$ids).")");
			$unicode_quans = array();
			foreach($quan_list as $k=>$v){
				$unicode_quans[] = str_to_unicode_string($v['name']);
			}
			$kw_unicode = implode(" ", $unicode_quans);
			$sql .= " and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
			$count_sql .= " and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
		}
		
			$merchant_id = intval($GLOBALS['request']['merchant_id']);
			if($merchant_id>0)
			{
				$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_location_link where location_id = ".$merchant_id);
				if($deal_ids)
				{
					$sql .= " and id in (".$deal_ids.")";
					$count_sql .= " and id in (".$deal_ids.")";
				}
				else
				{
					$sql .= " and id = 0 ";
					$count_sql .= " and id = 0 ";
				}
			}
		
		if($where != '')
		{
			$sql.=" and ".$where;
			$count_sql.=" and ".$where;
		}
		
		if($orderby=='')
		$sql.=" order by sort desc limit ".$limit;
		else
		$sql.=" order by ".$orderby." limit ".$limit;
		

		$deals = $GLOBALS['db']->getAll($sql);		
		$deals_count = $GLOBALS['db']->getOne($count_sql);
		
 		if($deals)
		{
			foreach($deals as $k=>$deal)
			{
				//团购图片集
				$img_list = array();
				$img_list[] = array('img'=>$deal['img']);
				$deal['image_list'] = $img_list;
			
				//格式化数据
				$deal['begin_time_format'] = to_date($deal['begin_time']);
				$deal['end_time_format'] = to_date($deal['end_time']);
				$deal['origin_price_format'] = format_price($deal['origin_price']);
				$deal['current_price_format'] = format_price($deal['current_price']);
				$deal['success_time_format']  = to_date($deal['success_time']);
				
				if($deal['origin_price']>0&&floatval($deal['discount'])==0) //手动折扣
				$deal['save_price'] = $deal['origin_price'] - $deal['current_price'];			
				else
				$deal['save_price'] = $deal['origin_price']*((10-$deal['discount'])/10);
				if($deal['origin_price']>0&&floatval($deal['discount'])==0)
				{
					$deal['discount'] = round(($deal['current_price']/$deal['origin_price'])*10,2);					
				}
				
				$deal['discount'] = round($deal['discount'],2);
				
				if($deal['uname']!='')
				$durl = url("tuan","deal",array("id"=>$deal['uname']));
				else
				$durl = url("tuan","deal",array("id"=>$deal['id']));				
				$deal['share_url'] = get_domain().$durl;
				
				
				if($GLOBALS['user_info'])
					{
						if(app_conf("URL_MODEL")==0)
						{
							$deal['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
						}
						else
						{
							$deal['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
						}
				}	
			
				

				$deal['save_price_format'] = format_price($deal['save_price']);
				if($deal['uname']!='')
				$durl = url("tuan","deal",array("id"=>$deal['uname']));
				else
				$durl = url("tuan","deal",array("id"=>$deal['id']));
				$deal['url'] = $durl;
				$deal['deal_success_num'] = sprintf($GLOBALS['lang']['SUCCESS_BUY_COUNT'],$deal['buy_count']);
				$deal['current_bought'] = $deal['buy_count'];
				//查询抽奖号
				if($deal['is_lottery']==1)
				$deal['lottery_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."lottery where deal_id = ".intval($deal['id'])." and buyer_id <> 0 ")) + intval($deal['buy_count']);
				if($deal['buy_status']==0) //未成功
				{
					$deal['success_less'] = sprintf($GLOBALS['lang']['SUCCESS_LESS_BUY_COUNT'],$deal['min_bought'] - $deal['buy_count']);
				}
				$deals[$k] = $deal;
			}
		}				
		return array('list'=>$deals,'count'=>$deals_count,'sql'=>$sql);	
}



function m_search_youhui_list($limit, $cate_id=0, $where='',$orderby = '',$city_id=0,$field_append="")
{		
	
			if($city_id==0)
			{
				$city = get_current_deal_city();
				$city_id = $city['id'];
			}
			
			$count_sql = "select count(*) from ".DB_PREFIX."deal " ;
			$sql = "select * $field_append from ".DB_PREFIX."deal ";

			
			$time = get_gmtime();
			$time_condition = '  and (end_time = 0 or end_time > '.$time.') ';
	
			$count_sql .= " where is_effect = 1 and is_delete = 0 and is_shop = 2 ".$time_condition;
			$sql .= " where is_effect = 1 and is_delete = 0 and is_shop = 2 ".$time_condition;
			
			if($cate_id>0)
			{
				$ids =load_auto_cache("deal_sub_cate_ids",array("cate_id"=>$cate_id));
				$sql .= " and cate_id in (".implode(",",$ids).")";
				$count_sql .= " and cate_id in (".implode(",",$ids).")";
			}			

			if($city_id>0)
			{			
				$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
				if($ids)
				{
				$sql .= " and city_id in (".implode(",",$ids).")";
				$count_sql .= " and city_id in (".implode(",",$ids).")";
				}
			}
			
			$merchant_id = intval($GLOBALS['request']['merchant_id']);
			if($merchant_id>0)
			{
				$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_location_link where location_id = ".$merchant_id);
				if($deal_ids)
				{
					$sql .= " and id in (".$deal_ids.")";
					$count_sql .= " and id in (".$deal_ids.")";
				}
				else
				{
					$sql .= " and id = 0 ";
					$count_sql .= " and id = 0 ";
				}
			}
			
			if($where != '')
			{
				$sql.=" and ".$where;
				$count_sql.=" and ".$where;
			}
			
			if($orderby=='')
			$sql.=" order by sort desc limit ".$limit;
			else
			$sql.=" order by ".$orderby." limit ".$limit;
						
			
			$deals = $GLOBALS['db']->getAll($sql);				
			$deals_count = $GLOBALS['db']->getOne($count_sql);
			
	 		if($deals)
			{
				foreach($deals as $k=>$deal)
				{
				
					//格式化数据
					$deal['origin_price_format'] = format_price($deal['origin_price']);
					$deal['current_price_format'] = format_price($deal['current_price']);
	
					
					if($deal['origin_price']>0&&floatval($deal['discount'])==0) //手动折扣
					$deal['save_price'] = $deal['origin_price'] - $deal['current_price'];			
					else
					$deal['save_price'] = $deal['origin_price']*((10-$deal['discount'])/10);
					if($deal['origin_price']>0&&floatval($deal['discount'])==0)
					{
						$deal['discount'] = round(($deal['current_price']/$deal['origin_price'])*10,2);					
					}
					
					$deal['discount'] = round($deal['discount'],2);
	
	
	
					$deal['save_price_format'] = format_price($deal['save_price']);
					if($deal['uname']!='')
					$durl = url("youhui","ydetail",array("id"=>$deal['uname']));
					else
					$durl = url("youhui","ydetail",array("id"=>$deal['id']));
					$deal['url'] = $durl;
					
					$deals[$k] = $deal;
				}
			}	
			$res = array('list'=>$deals,'count'=>$deals_count);	
			
		return $res;
}

function m_get_message_list($limit,$where='',$city = 0)
{
	$city = intval($city);
				
	if($city>0)
	{
		$sql = "select m.id,m.content,m.create_time,m.point,u.user_name,m.user_id,m.title from ".DB_PREFIX."message as m left join ".DB_PREFIX."user as u on u.id=m.user_id where m.pid = 0 and m.city_id =$city";
		$sql_count = "select count(*) from ".DB_PREFIX."message as m where m.pid = 0 and m.city_id =$city";
	}
	else
	{
		$sql = "select m.id,m.content,m.create_time,m.point,u.user_name,m.user_id,m.title from ".DB_PREFIX."message as m left join ".DB_PREFIX."user as u on u.id=m.user_id where m.pid = 0";
		$sql_count = "select count(*) from ".DB_PREFIX."message as m where m.pid = 0";
	
	}

	if($where!='')
	{
		$sql .= " and ".$where;
		$sql_count .=  " and ".$where;
	}
	
	$sql.=" order by m.create_time desc ";
	$sql.=" limit ".$limit;
	
	$list = $GLOBALS['db']->getAll($sql);
	$count = $GLOBALS['db']->getOne($sql_count);
	
	foreach($list as $k => $v)
	{
		$list[$k]['create_time_format'] = to_date($v['create_time']);
	}
	
	return array('list'=>$list,'count'=>$count);
}
function get_collect_list($limit,$user_id)
	{
		$user_id = intval($user_id);
		$sql = "select c.id,c.create_time as add_time,d.name,d.sub_name,d.buy_count,d.current_price,d.origin_price,d.id as deal_id,d.is_shop,d.icon from ".DB_PREFIX."deal_collect as c left join ".DB_PREFIX."deal as d on d.id = c.deal_id where c.user_id = ".$user_id."  order by c.create_time desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."deal_collect where user_id = ".$user_id;
		$list = $GLOBALS['db']->getAll($sql);
		$count = $GLOBALS['db']->getOne($sql_count);
		foreach($list as $k=>$v)
		{
			$list[$k]['format_add_time']=to_date($v['add_time'],'Y-m-d');
			$list[$k]['icon']=get_abs_img_root(get_spec_image($v['icon'],320,200,0));
			$list[$k]['current_price']=round($v['current_price'],2);
			$list[$k]['origin_price']=round($v['origin_price'],2);
		}
		return array("list"=>$list,'count'=>$count);
	}
	
	function get_link_list(){
		$f_link_group = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."link_group where is_effect = 1 order by sort desc");
		foreach($f_link_group as $k=>$v)
		{
			$g_links = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."link where is_effect = 1 and show_index = 1 and group_id = ".$v['id']." order by sort desc");
			if($g_links)
			{
				foreach($g_links as $kk=>$vv)
				{
					if(substr($vv['url'],0,7)=='http://')
					{
						$g_links[$kk]['url'] = str_replace("http://","",$vv['url']);
					}
				}
				$f_link_group[$k]['links'] = $g_links;
			}
			else
				unset($f_link_group[$k]);
		}
	
		return $f_link_group;
	}	

	

	//获取相应规格的图片地址
	//gen=0:保持比例缩放，不剪裁,如高为0，则保证宽度按比例缩放  gen=1：保证长宽，剪裁
	function make_img($img_path,$width=0,$height=0,$gen=0,$is_preview=true)
	{
		return get_spec_image($img_path,$width,$height,$gen);		
	
	}
		
	function biz_check($email,$pwd)
	{
		if($email&&$pwd)
		{
	
			$sql = "select s.name as name,a.account_name as account_name,a.login_ip as login_ip ,a.login_time as login_time ,a.update_time as create_time, a.supplier_id as supplier_id,a.id,a.account_password,a.is_main from ".DB_PREFIX."supplier_account as a left join ".DB_PREFIX."supplier as s on a.supplier_id = s.id where a.account_name ='".$email."'";
			
			$user_info = $GLOBALS['db']->getRow($sql);
	
			$is_use_pass = false;
			if (strlen($pwd) != 32){
				if($user_info['account_password']==md5($pwd)){
					$is_use_pass = true;
				}
			}
			else{
				if($user_info['account_password']==$pwd){
					$is_use_pass = true;
				}
			}
			if($is_use_pass)
			{
				
				if ($user_info['is_main'] == 1){
					$sql = "select id as location_id from ".DB_PREFIX."supplier_location where supplier_id = ".$user_info['supplier_id'];
				}else{
					$sql = "select location_id from ".DB_PREFIX."supplier_account_location_link where account_id = ".$user_info['id'];
				}
				//echo $sql;exit;
				$account_locations = $GLOBALS['db']->getAll($sql);
				$account_location_ids = array();
				foreach($account_locations as $row)
				{
					$account_location_ids[] = $row['location_id'];
				}
				$user_info['location_ids'] =  $account_location_ids;
				es_session::set("account_info",$user_info);
					
				return $user_info;
			}
			else
				return null;
		}
		else
		{
			return NULL;
		}
	}	
	
	
	//通过手机号码注册;
	function mobile_reg($mobile,$pwd,$gender){
		$root=array();
		$have_user_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where mobile = '$mobile' or user_name = '$mobile' or email = '$mobile'");
		if($have_user_id){
			$root['info'] = '该手机号码已经注册过!';
			$root['status']=0;			
		}else{
			require_once APP_ROOT_PATH."system/model/user.php";
			
			//生成新用户
			$user_data = array();
			$user_data['mobile'] = $mobile;
			$user_data['user_pwd'] = md5($pwd);
			$user_data['sex'] = $gender;
			$rs_data = auto_create($user_data, 1);
			if(!$rs_data['status'])
			{
				$root['status'] = 0;
				$root['info']	=	$rs_data['info'];
			}else{
				$root['id'] = $rs_data['id'];
				$root['uid'] = $rs_data['id'];
				$root['user_name'] =$rs_data['user_data']['user_name'];
				//$root['user_name'] = $mobile;
				$root['mobile'] = $rs_data['user_data']['mobile'];
				$root['user_pwd'] = $rs_data['user_data']['user_pwd'];
				$root['user_email'] = '';
				$root['user_money'] = 0;
				$root['user_money_format'] = format_price($root['user_money']);//用户金额
				$root['user_avatar'] = get_abs_img_root(get_muser_avatar($root['uid'],"big"));
				//$root['user_pwd'] = $pwd;
				$root['home_user']['fans'] = 0;
				$root['home_user']['photos'] = 0;//$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic_image where user_id = ".$user_data['id']);
				$root['home_user']['goods'] = 0;//$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$user_data['id']." and topic_group = 'Fanwe' and is_delete = 0 and is_effect = 1");
				$root['home_user']['follows'] = 0;//$user_data['focus_count'];
				$root['home_user']['favs'] = 0;//$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$user_data['id']." and fav_id <> 0");
					
				$root['home_user']['user_avatar'] = get_abs_img_root(get_muser_avatar($root['uid'],"big"));
				$root['status'] = 1;
                                $root['info']	= '注册成功';
                                
                                es_session::set("user_info",$rs_data['user_data']);
				$GLOBALS['user_info'] = $rs_data['user_data'];
				es_session::set("user_logined", true);
				$GLOBALS['user_logined'] = true;
			}
			
			
		}
		
		return $root;
	}
	
	function getWebAdsUrl($type,$data){
		//2:URL广告;9:团购列表;10:商品列表;11:活动列表;12:优惠列表;14:团购明细;15:商品明细;17:优惠明细;22:商家列表;23：商家明细; 24:门店自主下单		
		/*if ($type == 2){
			$url = $data['url'];
		}else if ($type == 9){
			$url = url('index','tuanlist&catalog_id='.$data['cate_id']);
		}else if ($type == 10){
			$url = url('index','goodslist&catalog_id='.$data['cate_id']);
		}else if ($type == 11){
			$url = url('index','eventlist&cate_id='.$data['cate_id']);
		}else if ($type == 12){
			$url = url('index','youhuilist&cate_id='.$data['cate_id']);
		}else if ($type == 14){
			$url = url('index','goodsdesc&id='.$data['data_id']);
		}else if ($type == 15){
			$url = url('index','goodsdesc&id='.$data['data_id']);
		}else if ($type == 17){
			$url = url('index','youhuiitem&id='.$data['data_id']);
		}else if ($type == 22){
			$url = url('index','merchantlist&cate_id='.$data['cate_id']);
		}else if ($type == 23){
			$url = url('index','merchantitem&id='.$data['data_id']);
		}else if ($type == 24){
			$url = url('index','merchantlist&is_auto_order=1');
		}*/
		if ($type == 0){
			$url = $data['url'];
		}else if ($type == 11){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=tuanlist&catalog_id=".$data['cate_id'];
		}else if ($type == 12){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=goodslist&catalog_id=".$data['cate_id'];
		}else if ($type == 14){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=eventlist&cate_id=".$data['cate_id'];
		}else if ($type == 15){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=youhuilist&cate_id=".$data['cate_id'];
		}else if ($type == 21){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=goodsdesc&id=".$data['data_id'];
		}else if ($type == 21){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=goodsdesc&id=".$data['data_id'];
		}else if ($type == 25){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=youhuiitem&id=".$data['data_id'];
		}else if ($type == 16){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=merchantlist&id=".$data['cate_id'];
		}else if ($type == 26){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=merchantitem&id=".$data['data_id'];
		}else if ($type == 24){
			$url =get_domain().APP_ROOT."/".APP_INDEX."/index.php?ctl=merchantlist&is_auto_order=1";
		}
		
		return  str_replace("sjmapi", "wap", $url);
	}
	
	/**
	 * 里面包含二级分类
	 * [bcate_list] => Array
        (
            [0] => Array
                (
                    [id] => 0
                    [name] => 全部分类
                    [icon_img] => ''
                    [bcate_type] => Array //二级分类
                        (
                            [0] => Array
                                (
                                    [id] => 0
                                    [cate_id] => 0
                                    [name] => 全部分类
                                )
                        )
                )
               [1] => Array
                (
                    [id] => 10
                    [name] => 生活服务
                    [icon_img] =>http://localhost:8000/o2o/sjmapi/../public/attachment/201408/04/16/53df411375aaa.gif
                    [bcate_type] => Array
                        (
                            [0] => Array
                                (
                                    [id] => 0
                                    [cate_id] => 10
                                    [name] => 全部
                                )

                            [1] => Array
                                (
                                    [cate_id] => 10
                                    [id] => 5
                                    [name] => KTV
                                )

                        )
                )
		）
	 * **/
	function getCateList(){
		$bigcate_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_delete=0 and is_effect=1 and pid=0 order by sort desc,id desc");
		$bcate_list=array();
		$bcate_ids=array();
		$bcate_type=array();
		foreach($bigcate_list as $k=>$v)
		{
			$bcate_ids[]=$v['id'];
			$bcate_type[$v['id']]='';
			$bigcate_list[$k]['icon_img']='';//get_abs_img_root($v['icon_img']);
		}
		$sql1="select dctl.cate_id,dctl.deal_cate_type_id as id,dct.name from ".DB_PREFIX."deal_cate_type as dct left join ".DB_PREFIX."deal_cate_type_link as dctl on dctl.deal_cate_type_id=dct.id where dctl.cate_id in(".implode(',',$bcate_ids).") order by dct.sort desc,dct.id desc";
		$sub_cate=$GLOBALS['db']->getAll($sql1);
		
		foreach($sub_cate as $k=>$v)
		{
			$bcate_type[$v['cate_id']][]=$v;
		}
		
		$bcate_list[0]['id']=0;
		$bcate_list[0]['name']='全部分类';
		$bcate_list[0]['icon_img']='';
		$bcate_list[0]['bcate_type'][0]['id']=0;
		$bcate_list[0]['bcate_type'][0]['cate_id']=0;
		$bcate_list[0]['bcate_type'][0]['name']='全部分类';
		
		foreach($bigcate_list as $k=>$v)
		{
			if($GLOBALS['request']['from']=="wap"){
				if($bcate_type[$v['id']]==null || $bcate_type[$v['id']]=='')
					$bigcate_list[$k]['bcate_type']=array();
				else
					$bigcate_list[$k]['bcate_type']=$bcate_type[$v['id']];
			}
			else{
				$bcate_type_array['0']['id']=0;
				$bcate_type_array['0']['cate_id']=$v['id'];
				$bcate_type_array['0']['name']="全部";
				if($bcate_type[$v['id']]==null || $bcate_type[$v['id']]=='')
					$bigcate_list[$k]['bcate_type']=$bcate_type_array;
				else
					$bigcate_list[$k]['bcate_type']=array_merge($bcate_type_array,$bcate_type[$v['id']]);	
			}
			
				
			$bcate_list[]=$bigcate_list[$k];
		}
		
		return $bcate_list;
	}
	function getShopcateList(){
	$allcate_list=$GLOBALS['db']->getAll("select id,name,pid from ".DB_PREFIX."shop_cate where is_delete=0 and is_effect=1 order by pid asc,sort desc");
		$cate_list=array();
		$bcate_list=array();
		foreach($allcate_list as $k=>$v)
		{
			if($v['pid']==0)
				$cate_list[$v['id']]=$v;
			else
				$cate_list[$v['pid']]['bcate_type'][]=$v;
				
		}
		
		$bcate_list[0]['id']=0;
		$bcate_list[0]['name']='全部分类';
		$bcate_list[0]['bcate_type'][0]['id']=0;
		$bcate_list[0]['bcate_type'][0]['qid']=0;
		$bcate_list[0]['bcate_type'][0]['name']='全部分类';
		foreach($cate_list as $k=>$v)
		{
			$bcate_type_array['0']['id']=$v['id'];
			$bcate_type_array['0']['qid']=0;
			$bcate_type_array['0']['name']="全部";
			if($cate_list[$v['id']]['bcate_type']==null || $cate_list[$v['id']]['bcate_type']=='')
				$cate_list[$v['id']]['bcate_type']=$bcate_type_array;
			else
				$cate_list[$v['id']]['bcate_type']=array_merge($bcate_type_array,$cate_list[$v['id']]['bcate_type']);
				
			$bcate_list[]=$cate_list[$k];
			
				
		}
	
		return $bcate_list;
	}
	/**
	 * 获得相应城市商圈
	 * [quan_list] => Array
        (
            [0] => Array
                (
                    [id] => 0
                    [name] => 全城
                    [quan_sub] => Array
                        (
                            [0] => Array
                                (
                                    [id] => 0
                                    [pid] => 0
                                    [name] => 全城
                                )
                        )
                )

             [1] => Array
                (
                    [id] => 8
                    [name] => 鼓楼区
                    [city_id] => 15
                    [sort] => 1
                    [pid] => 0
                    [quan_sub] => Array
                        (
                            [0] => Array
                                (
                                    [id] => 8
                                    [name] => 全部
                                    [pid] => 8
                                )

                            [1] => Array
                                (
                                    [id] => 20
                                    [name] => 屏山
                                    [city_id] => 15
                                    [sort] => 13
                                    [pid] => 8
                                )                   
                        )
                )

      )
	 * **/
	
	function getCityList(){
		$all_quan_list=$GLOBALS['db']->getAll("select a.id,a.pid,a.name,a.sort, a.id as city_id from ".DB_PREFIX."deal_city a where a.pid = 1 and a.is_effect=1 and a.is_delete=0 order by id asc");
		$quan_list=array();
		$quan_sub_list=array();
	
		$quan_list[0]['id']=1;
		$quan_list[0]['name']='全国';
		$quan_list[0]['pid']=0;
		$quan_list[0]['sort']=0;
		$quan_list[0]['city_id']=1;

		$quan_list[0]['quan_sub'][0]['id']=1;
		$quan_list[0]['quan_sub'][0]['pid']=0;
		$quan_list[0]['quan_sub'][0]['name']='全国';
		foreach($all_quan_list as $k=>$v)
		{
			$quan_list[]=$v;
		}
	
		foreach ($quan_list as $k=>$v)
		{
			if ($v['id'] != 1)
			$quan_list[$k]['quan_sub'][]=array(
					'id'=>$v['id'],
					'city_id'=>$v['id'],
					'pid'=>$v['pid'],
					'sort'=>$v['sort'],
					'name'=>'全部',
					);						
		}
	
		return $quan_list;
	}
		
	function getQuanList($city_id =0){
		$all_quan_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."area where city_id=".intval($city_id)." order by sort desc");
		$quan_list=array();
		$quan_sub_list=array();
		
		$quan_list[0]['id']=0;
		$quan_list[0]['name']='全城';
		$quan_list[0]['quan_sub'][0]['id']=0;
		$quan_list[0]['quan_sub'][0]['pid']=0;
		$quan_list[0]['quan_sub'][0]['name']='全城';
		foreach($all_quan_list as $k=>$v)
		{
			if($v['pid']==0)
			{
				$quan_list[]=$v;
			}
			if($v['pid']>0)
				$quan_sub_list[$v['pid']][]=$v;
		}
		
		foreach ($quan_list as $k=>$v)
		{
			if($v['name'] !="全城")
			{
				if($GLOBALS['request']['from']=="wap"){
					if($quan_sub_list[$v['id']] ==null || $quan_sub_list[$v['id']] =='')
						$quan_list[$k]['quan_sub']=array();
					else
						$quan_list[$k]['quan_sub']=$quan_sub_list[$v['id']];
				}else{
					$quan_sub_array[0]['id']=$v['id'];
					$quan_sub_array[0]['name']="全部";
					$quan_sub_array[0]['pid']=$v['id'];
					
					if($quan_sub_list[$v['id']] ==null || $quan_sub_list[$v['id']] =='')
						$quan_list[$k]['quan_sub']=$quan_sub_array;
					else
						$quan_list[$k]['quan_sub']=array_merge($quan_sub_array,$quan_sub_list[$v['id']]);
				}
			}
		}
		
		return $quan_list;
	}
	
	/**
	 * 添加商品团购点评
	 * @param int $user_id
	 * @param string $content
	 * @param int $point
	 * @param int $rel_id
	 * @param int $city_id
	 * @param int $is_buy
	 * @param string $rel_table
	 * @param string $title
	 * @param string $contact
	 * @param string $contact_name
	 * @param string $message_group
	 * @return multitype:number NULL
	 */
	function add_deal_dp($user_id,$content, $point, $rel_id, $city_id = 0, $is_buy = 1, $rel_table='deal', $title = '', $contact = '', $contact_name = '',  $message_group = null){
		$result = array();
		$result['status'] = 0;
		$user_id = intval($user_id);
	
		if($user_id == 0)
		{
			$result['msg'] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			return $result;
		}
		if($content=='')
		{
			$result['msg'] = $GLOBALS['lang']['MESSAGE_CONTENT_EMPTY'];
			return $result;
		}
		
		$client_ip=strim($GLOBALS['request']['client_ip']);
		if (empty($client_ip))
			$client_ip = get_client_ip();
		
		if(!check_ipop_limit($client_ip,"message",intval(app_conf("SUBMIT_DELAY")),0))
		{
			$result['msg'] = $GLOBALS['lang']['MESSAGE_SUBMIT_FAST'];
			return $result;
		}
	
		$rel_table = addslashes(trim($rel_table));
		$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."' and type_name <> 'supplier'");
		if(!$message_type)
		{
			$result['msg'] = $GLOBALS['lang']['INVALID_MESSAGE_TYPE'];
			return $result;
		}
	
		//$message_group = addslashes(trim($_REQUEST['message_group']));
	
		//添加留言
		$message['title'] = $title?htmlspecialchars(addslashes(valid_str($title))):htmlspecialchars(addslashes(valid_str($content)));
		$message['content'] = htmlspecialchars(addslashes(valid_str($content)));
		$message['title'] = valid_str($message['title']);
		if($message_group)
		{
			$message['title']="[".$message_group."]:".$message['title'];
			$message['content']="[".$message_group."]:".$message['content'];
		}
	
		$message['create_time'] = get_gmtime();
		$message['rel_table'] = $rel_table;
		$message['rel_id'] = $rel_id;
		$message['user_id'] = $user_id;
		$message['city_id'] = intval($city_id);
	
		if(app_conf("USER_MESSAGE_AUTO_EFFECT")==0)
		{
			$message_effect = 0;
		}
		else
		{
			$message_effect = $message_type['is_effect'];
		}
		$message['is_effect'] = $message_effect;
	
		$message['is_buy'] = intval($is_buy);
		$message['contact'] = $contact?htmlspecialchars(addslashes($contact)):'';
		$message['contact_name'] = $_REQUEST['contact_name']?htmlspecialchars(addslashes($_REQUEST['contact_name'])):'';
		if($message['is_buy']==1)
		{
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.deal_id = ".intval($message['rel_id'])." and do.user_id = ".intval($message['user_id'])." and do.pay_status = 2")==0)
			{
				$result['msg'] = $GLOBALS['lang']['AFTER_BUY_MESSAGE_TIP'];
				return $result;
			}
		}
		$message['point'] = intval($point);
		$GLOBALS['db']->autoExecute(DB_PREFIX."message",$message);
		$message_id = intval($GLOBALS['db']->insert_id());
		if($message['is_buy']==1)
		{
			$message_id = $GLOBALS['db']->insert_id();
			$attach_list=get_topic_attach_list();
	
			$deal_info = $GLOBALS['db']->getRow("select id,is_shop,name,sub_name from ".DB_PREFIX."deal where id = ".$rel_id);
			if($deal_info['is_shop']==0)
			{
				$url_route = array(
						'rel_app_index'	=>	'tuan',
						'rel_route'	=>	'deal',
						'rel_param' => 'id='.$deal_info['id']
				);
				$type = "tuancomment";
					
				$locations = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_location_link where deal_id = ".$deal_info['id']);
				$dp_title = "对".$deal_info['sub_name']."的消费点评";
				foreach($locations as $location)
				{
					insert_dp($dp_title,$message['content'],$location['location_id'],$message['point'],$is_buy=1,$from="tuan",$url_route,$message_id);
				}
			}
			if($deal_info['is_shop']==1)
			{
				$url_route = array(
						'rel_app_index'	=>	'shop',
						'rel_route'	=>	'goods',
						'rel_param' => 'id='.$deal_info['id']
				);
				$type="shopcomment";
			}
			if($deal_info['is_shop']==2)
			{
				$url_route = array(
						'rel_app_index'	=>	'youhui',
						'rel_route'	=>	'ydetail',
						'rel_param' => 'id='.$deal_info['id']
				);
				$type="youhuicomment";
			}
			increase_user_active($user_id,"点评了一个团购");
			$title = "对".$deal_info['sub_name']."发表了点评";
			$tid = insert_topic($message['content'],$title,$type,"share",$relay_id = 0,$fav_id = 0,$group_data="",$attach_list=array(),$url_route);
			if($tid)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
	
		}
			
		$result['status'] = 1;
		$result['msg'] = $GLOBALS['lang']['MESSAGE_POST_SUCCESS'];
		return $result;
	}
	
	
	//文件上传的通用函数
	/**
	 * 通用上传，上传到attachments目录，按日期划分
	 * 错误返回 error!=0,message错误消息
	 * 正确时返回 error=0, url: ./public格式的文件相对路径  path:物理路径 name:文件名
	 */
	function upload($_files)
	{
	
		//上传处理
		//创建comment目录
		if (!is_dir(APP_ROOT_PATH."public/attachment")) {
			@mkdir(APP_ROOT_PATH."public/attachment");
			@chmod(APP_ROOT_PATH."public/attachment", 0777);
		}
	
		$dir = to_date(NOW_TIME,"Ym");
		if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
			@mkdir(APP_ROOT_PATH."public/attachment/".$dir);
			@chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
		}
		 
		$dir = $dir."/".to_date(NOW_TIME,"d");
		if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
			@mkdir(APP_ROOT_PATH."public/attachment/".$dir);
			@chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
		}
	
		$dir = $dir."/".to_date(NOW_TIME,"H");
		if (!is_dir(APP_ROOT_PATH."public/attachment/".$dir)) {
			@mkdir(APP_ROOT_PATH."public/attachment/".$dir);
			@chmod(APP_ROOT_PATH."public/attachment/".$dir, 0777);
		}
		 
		if(app_conf("IS_WATER_MARK")==1)
			$img_result = save_image_upload($_files,"file","attachment/".$dir,$whs=array(),1,1);
		else
			$img_result = save_image_upload($_files,"file","attachment/".$dir,$whs=array(),0,1);
		if(intval($img_result['error'])!=0)
		{
			return $img_result;
		}
		else
		{
			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
			{
				syn_to_remote_image_server($img_result['file']['url']);
			}
				
		}
	
		$data_result['error'] = 0;
		$data_result['url'] = $img_result['file']['url'];
		$data_result['path'] = $img_result['file']['path'];
		$data_result['name'] = $img_result['file']['name'];
		return $data_result;
	
	}
	
	
	/**
	 * 分享点评的上传，上传到comment目录，按日期划分
	 * 错误返回 error!=0,message错误消息
	 * 正确时返回 error=0, url: ./public格式的文件相对路径  path:物理路径 name:文件名
	 * thumb->preview 60x60的小图 url,path
	 */
	function upload_topic($_files)
	{
	
	
		//上传处理
		//创建comment目录
		if (!is_dir(APP_ROOT_PATH."public/comment")) {
			@mkdir(APP_ROOT_PATH."public/comment");
			@chmod(APP_ROOT_PATH."public/comment", 0777);
		}
	
		$dir = to_date(NOW_TIME,"Ym");
		if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
			@mkdir(APP_ROOT_PATH."public/comment/".$dir);
			@chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
		}
			
		$dir = $dir."/".to_date(NOW_TIME,"d");
		if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
			@mkdir(APP_ROOT_PATH."public/comment/".$dir);
			@chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
		}
	
		$dir = $dir."/".to_date(NOW_TIME,"H");
		if (!is_dir(APP_ROOT_PATH."public/comment/".$dir)) {
			@mkdir(APP_ROOT_PATH."public/comment/".$dir);
			@chmod(APP_ROOT_PATH."public/comment/".$dir, 0777);
		}
			
		if(app_conf("IS_WATER_MARK")==1)
			$img_result = save_image_upload($_files,"file","comment/".$dir,$whs=array('preview'=>array(60,60,1,0)),1,1);
		else
			$img_result = save_image_upload($_files,"file","comment/".$dir,$whs=array('preview'=>array(60,60,1,0)),0,1);
		if(intval($img_result['error'])!=0)
		{
			return $img_result;
		}
		else
		{
			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
			{
				syn_to_remote_image_server($img_result['file']['url']);
				syn_to_remote_image_server($img_result['file']['thumb']['preview']['url']);
			}
	
		}
	
		$data_result['error'] = 0;
		$data_result['url'] = $img_result['file']['url'];
		$data_result['path'] = $img_result['file']['path'];
		$data_result['name'] = $img_result['file']['name'];
		$data_result['thumb'] = $img_result['file']['thumb'];
	
		require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
		$image = new es_imagecls();
		$info = $image->getImageInfo($img_result['file']['path']);
	
		$image_data['width'] = intval($info[0]);
		$image_data['height'] = intval($info[1]);
		$image_data['name'] = valid_str($_FILES['file']['name']);
		$image_data['filesize'] = filesize($img_result['file']['path']);
		$image_data['create_time'] = NOW_TIME;
		$image_data['user_id'] = intval($GLOBALS['user_info']['id']);
		$image_data['user_name'] = strim($GLOBALS['user_info']['user_name']);
		$image_data['path'] = $img_result['file']['thumb']['preview']['url'];
		$image_data['o_path'] = $img_result['file']['url'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."topic_image",$image_data);
	
		$data_result['id'] = intval($GLOBALS['db']->insert_id());
	
		return $data_result;
	
	}
	
?>
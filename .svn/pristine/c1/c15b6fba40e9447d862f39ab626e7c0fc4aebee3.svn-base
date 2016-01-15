<?php
class goodslist{
	public function index(){
		require_once APP_ROOT_PATH.'system/model/deal.php'; 
		$root = array();
		$root['return'] = 1;
		$catalog_id = intval($GLOBALS['request']['catalog_id']);//商品分类ID
		$cata_type_id=intval($GLOBALS['request']['cata_type_id']);//商品二级分类
		$city_id = intval($GLOBALS['request']['city_id']);//城市分类ID	
		$page = intval($GLOBALS['request']['page']); //分页
		$keyword = strim($GLOBALS['request']['keyword']);
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		//print_r($GLOBALS['request']);
		$order_type=strim($GLOBALS['request']['order_type']);
		$quan_id = intval($GLOBALS['request']['quan_id']); //商圈id
		if($cata_type_id > 0)
			$catalog_id=$cata_type_id;
		
		/*输出分类*/
		$bcate_list = getShopcateList();
		$url_param['quan_id'] = $quan_id;
		$url_param['catalog_id'] = $catalog_id;
		foreach($bcate_list as $k=>$v)
		{
			/*
			if($catalog_id==$v['id'])
			{
				$bcate_list['bcate_type'][$k]['act'] = 1;
			}
			*/
			$tmp_url_param = $url_param;
			unset($tmp_url_param['catalog_id']);
			$tmp_url_param['catalog_id']=$v['id'];
			$tmp_url_param['catename']=$v['name'];
			if($quan_id>0){
				$quanname=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."area  where id=".$quan_id);
				$tmp_url_param['quanname']=$quanname;
			}else{
				$tmp_url_param['quanname']="全城";
			}
			$turl = wap_url("index","goodslist",$tmp_url_param);
			$url=str_replace('sjmapi','wap', $turl);
			$bcate_list[$k]["url"]=$url;
				
			foreach($v['bcate_type'] as $kk=>$vv){
				/*
				if($catalog_id==$vv['id'])
				{
					$bcate_list['bcate_type'][$kk]['act'] = 1;
				}
				*/
				$tmp_url_param = $url_param;
				unset($tmp_url_param['catalog_id']);
				$tmp_url_param['catalog_id']=$vv['id'];
				if($bcate_list['bcate_type'][$kk]['id']==$vv['id']){
					$tmp_url_param['catename']=$v['name'];
				}else{
					$tmp_url_param['catename']=$vv['name'];
				}
				if($quan_id>0){
					$quanname=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."area a where id=".$quan_id);
					$tmp_url_param['quanname']=$quanname;
				}else{
					$tmp_url_param['quanname']="全城";
				}
				
				$turl = wap_url("index","goodslist",$tmp_url_param);
				$url=str_replace('sjmapi','wap', $turl);
				$bcate_list[$k]["bcate_type"][$kk]["url"]=$url;
			}
				
		
		}
		
		
		//品牌列表
		if ($catalog_id > 0){
			$cate_key = load_auto_cache("shop_cate_key",array("cid"=>$catalog_id));
			$brand_list = $GLOBALS['db']->getAll("select id,name,sort,0 as city_id, from ".DB_PREFIX."brand where match(tag_match) against('".$cate_key."' IN BOOLEAN MODE)  order by sort limit 100");
		}else{
			$brand_list = $GLOBALS['db']->getAll("select id,name,sort,0 as city_id from ".DB_PREFIX."brand  order by sort limit 100");			
		}
		
		$quan_list = array();
		$quan_list[0]['id']=0;
		$quan_list[0]['name']='全部品牌';
		/*
		$quan_list[0]['quan_sub'][0]['id']=0;
		$quan_list[0]['quan_sub'][0]['pid']=0;
		$quan_list[0]['quan_sub'][0]['name']='全部';
		*/
		foreach($brand_list as $k=>$v)
		{
			$quan_list[]=$v;
		}
		
		
		foreach($quan_list as $k=>$v)
		{
			$tmp_url_param = $url_param;
			unset($tmp_url_param['quan_id']);
			$tmp_url_param['quan_id']=$v['id'];
			$tmp_url_param['quanname']=$v['name'];
			if($catalog_id>0){
				$catename=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."shop_cate  where id=".$catalog_id);
				$tmp_url_param['catename']=$catename;
			}else{
				$tmp_url_param['catename']="全部分类";
			}
			$turl = wap_url("index","goodslist",$tmp_url_param);
			$url=str_replace('sjmapi','wap', $turl);
			
			$quan_list[$k]["url"]=$url;
				
			$quan_list[$k]['quan_sub'][] = array('id'=>$v['id'],'pid'=>$v['id'],'name'=>'全部');
		}
		$ordertype[] = array("name"=>"默认排序","sc"=>"avg_point");
		$ordertype[] = array("name"=>"最新发布","sc"=>"newest");
		$ordertype[] = array("name"=>"销量最高","sc"=>"buy_count");
		$ordertype[] = array("name"=>"价格最高","sc"=>"price_desc");
		$ordertype[] = array("name"=>"价格最低","sc"=>"price_asc");
		
		foreach($ordertype as $k=>$v){
			$tmp_url_param = $url_param;
			if($quanname)
				$tmp_url_param['quanname']=$quanname;
			if($catename)
				$tmp_url_param['catename']=$catename;
			if($keyword)
				$tmp_url_param['keyword']=$keyword;
			$tmp_url_param['order_type']=$v['sc'];
			$turl = wap_url("index","goodslist",$tmp_url_param);
			$url=str_replace('sjmapi','wap', $turl);
			$ordertype[$k]["url"]=$url;
		}
		
		$root['ordertype']=$ordertype;
		
		/*排序*/
		if($order_type=='avg_point')
			$order= " avg_point desc,id desc ";
		elseif($order_type=='newest')
			$order= " create_time desc,id desc ";
		elseif($order_type=='buy_count')
			$order= " buy_count desc,id desc ";
		elseif($order_type=='price_asc')
			$order= " current_price asc,id desc ";
		elseif($order_type=='price_desc')
			$order= " current_price desc,id desc ";
		else
			$order = "sort desc,id desc ";
		
		$page=$page==0?1:$page;
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
		//buy_type = 0 普通商品;1积分商品
		$condition = " buy_type = 0  and is_shop=1";
		if($keyword)
		{
			$kws_div = div_str($keyword);
			foreach($kws_div as $k=>$item)
			{
				$kws[$k] = str_to_unicode_string($item);
			}
			$ukeyword = implode(" ",$kws);
			$condition .=" and  (match(tag_match,name_match,locate_match,shop_cate_match) against('".$ukeyword."' IN BOOLEAN MODE) or name like '%".$keyword."%')";
		}
		
		$merchant_id = intval($GLOBALS['request']['merchant_id']);
		if($merchant_id>0)
		{
			$deal_ids = $GLOBALS['db']->getOne("select group_concat(deal_id) from ".DB_PREFIX."deal_location_link where location_id = ".$merchant_id);
			if($deal_ids)
			{

				$condition .= " and id in (".$deal_ids.") ";
			}
			else
			{
				$condition .=" and id ='' ";
			}
		}
		//根据传入的商圈ID来搜索该商圈下的商品
		if ($quan_id > 0){
			$condition .=" and brand_id = ".$quan_id;
		}

		//get_goods_list($limit,$type=array(DEAL_ONLINE,DEAL_HISTORY,DEAL_NOTICE),$param=array("cid"=>0,"city_id"=>0), $join='', $where='',$orderby = '')
		$deals = get_goods_list($limit,array(DEAL_ONLINE,DEAL_HISTORY),array("cid"=>$catalog_id,"city_id"=>$city_id),'',$condition,$order);
		$condition = $deals['condition'];
		
		$sql = "select count(*) from ".DB_PREFIX."deal as d where  ".$condition;
		
		$count= $GLOBALS['db']->getOne($sql);
		
		$list = $deals['list'];
		
		
		$page_total = ceil($count/$page_size);
		
		

		
		$goodses = array();
		foreach($list as $item)
		{
			//$goods = array();
			$goods = getGoodsArray($item);
			$goods['image']=get_abs_img_root(get_spec_image($item['img'],140,85,0));
			$goodses[] = $goods;
		}
		$root['item'] = $goodses;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total,"page_size"=>$page_size);
		

		//$root['quan_list'] = getCityList();
		/*
		//输出城市
		$root['city_list']=getCityList();
		//输出商圈
		$quan_list=getQuanList($city_id);
		$root['quan_list2'] = $quan_list;
		*/
		
		//$root['bcate_list'] = $bcate_list;
		//$root['quan_list'] = $quan_list;
		
		if ($bcate_list === false){
			$root['bcate_list'] = array();
		}else{
			$root['bcate_list'] = $bcate_list;
		}
		
		if ($quan_list === false){
			$root['quan_list'] = array();
		}else{
			$root['quan_list'] = $quan_list;
		}
		$root['page_title'] = "商品列表";
		$root['city_name']=$city_name;
		output($root);
		
	}
}
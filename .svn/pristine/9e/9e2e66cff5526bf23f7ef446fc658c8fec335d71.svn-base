<?php
class mobile_qrcode{
	public function index()
	{
		$pc_url = strim($GLOBALS['request']['pc_url']);
		
		/*
		type
		
		2:URL广告
		
		9:团购列表 ok
		10:商品列表
		11:活动列表 ok
		12:优惠列表 ok
		14:团购明细
		13:代金券列表
		15:商品明细 
		16:活动明细 ok
		17:优惠明细  ok
		18:代金券明细 
		22:商家列表  ok
		23:商家明细	ok	
		*/
		
		//print_r($GLOBALS['request']);;exit;
		
		//$pc_url = 'http://o2o.fanwe.net/youhui.php?ctl=edetail&id=2';
		
		$type = 0;
		$id = 0;
		
		//判断是否为：12:优惠列表 或 17:优惠明细; 22:商家列表; 23:商家明细; 11:活动列表;16:活动明细
		$decode_data = $this->decode_yh($pc_url);
		
		if ($decode_data != false){
			$type = $decode_data['type'];
			$id = $decode_data['id'];
		}
		
		
		//判断是否为：9:团购列表;14:团购明细
		if ($decode_data == false){
			
			$decode_data = $this->decode_tuan($pc_url);
			
			$type = $decode_data['type'];
			$id = $decode_data['id'];
		}
		
		//判断是否为：10:商品列表; 15:商品明细 
		if ($decode_data == false){
			$decode_data = $this->decode_goods($pc_url);
				
			$type = $decode_data['type'];
			$id = $decode_data['id'];
		}

		if ($type == 0){
			
			if (strpos($pc_url, 'http://') === 0){
				$type = 1;
			}else if (strpos($pc_url, 'www') === 0){
				$type = 1;
				$pc_url = 'http://'.$pc_url;
			}
		}
		
		
		$type = intval($type);
		
		$root = array();
		$root['return'] = 1;
		
		$root['type'] = $type;//
		$root['pc_url'] = htmlspecialchars_decode($pc_url);
		
		$data = array();
		
		if(in_array($type,array(9,10,11,12,13,22))) //列表取分类ID
		{
			$data['cate_id'] = $id;
			if($type==9||$type==12||$type==13||$type==22) //生活服务类
			{
				$data['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$id);
			}
			elseif($type==10)  //商城
			{
				$data['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."shop_cate where id = ".$id);
			}
			elseif($type==11)  //活动
			{
				$data['cate_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."event_cate where id = ".$id);
			}
			$data['cate_name'] = $data['cate_name']?$data['cate_name']:"全部";
		}

		if(in_array($type,array(14,15,16,17,18,23))) //明细
		{
			$data['data_id'] = $id;
		}
		
		if(in_array($type,array(1))) //明细
		{
			$data['url'] = $pc_url;
		}
		
		$root['data'] = $data;
		
		output($root);
	}
	
	/**
	 * 判断是否为：10:商品列表; 15:商品明细 
	 * @param string $pc_url
	 */
	public function decode_goods($pc_url)
	{
		$type = 0;
		$id = 0;

		if (strpos($pc_url, 'ctl=deal') || strpos($pc_url, '/deal/')){
			if(strpos($pc_url, '/deal/'))
				$id = $this->get_id($pc_url,'/deal/');
			else
				$id = $this->get_id($pc_url,'act');
			if ($id > 0){
				$type = 15;
			}else{
				$type = 0;
			}
		}else if (strpos($pc_url, 'ctl=cate') || strpos($pc_url, '/cate/')){
			$id = $this->get_id($pc_url,'cid');
			$type = 10;
		}else{
			return false;
		}
		
		return array('type'=>$type,'id'=>$id);
	}
		
	/**
	 * 判断是否为：9:团购列表;14:团购明细
	 * @param string $pc_url
	 */
	public function decode_tuan($pc_url)
	{//http://www.sh591.cn/index.php?ctl=deal&act=159
		$type = 0;
		$id = 0;
		
		if (strpos($pc_url, '/tuan') != false){
			
			return false;
		}else{
			//
			
			if (strpos($pc_url, 'ctl=deal') || strpos($pc_url, '/deal')){	
				if(strpos($pc_url, '/deal/'))
				$id = $this->get_id($pc_url,'/deal/');
				else				
				$id = $this->get_id($pc_url,'act');
				if ($id > 0){
					$type = 15;
				}else{
					$type = 0;
				}
			}else{
				//$id = $this->get_id($pc_url,'act');
				$type = 9;						
			}
		}	
		return array('type'=>$type,'id'=>$id);
	}
	
	/**
	 * 判断是否为：12:优惠列表 或 17:优惠明细; 22:商家列表; 23:商家明细;11:活动列表;16:活动明细
	 * @param string $pc_url
	 */
	public function decode_yh($pc_url)
	{
		//http://o2o.fanwe.net/youhui.php
		//http://o2o.fanwe.net/youhui.php?ctl=fdetail&id=19
		
		//http://o2o.fanwe.net/youhui.php?ctl=store
		//http://o2o.fanwe.net/youhui.php?ctl=store&act=view&id=24
		
		//http://www.kaiyela.com/youhui/store-view/id-24
		
		//http://o2o.fanwe.net/youhui.php?ctl=store&cid=8&has_tuan=1&has_daijin=1
		//http://www.kaiyela.com/youhui/store/cid-20-aid-0-tid-0-qid-0-keyword--minprice-0-maxprice-0-has_tuan-0-has_daijin-0-has_youhui-0-has_event-0-has_goods-0-is_verify-0
		//http://o2o.fanwe.net/youhui.php?ctl=fdetail&id=29
		
		//商品详细http://o2o.fanwe.net/index.php?ctl=deal&act=78
		//优惠详细http://o2o.fanwe.net/index.php?ctl=youhui&act=22
		//商家详细http://o2o.fanwe.net/index.php?ctl=store&act=35
		//活动详细http://o2o.fanwe.net/index.php?ctl=event&act=4
		
		
		$type = 0;
		$id = 0;
		
		if (strpos($pc_url, '/youhuis') != false){
			
			return false;	
		}else{
			
			
			if (strpos($pc_url, '/stores') || strpos($pc_url, 'ctl=stores')){				
				//&id=19 || id-19
				//if (strpos($pc_url, '/store-view/')||strpos($pc_url, 'act=view')){
					//$id = $this->get_id($pc_url,'act');
					//if ($id > 0){
						$type = 23;
					//}else{
					//	$type = 0;
					//}
				//}else{
					//$id = $this->get_id($pc_url,'cid');
					//$type = 22;
				//}	
			}else if (strpos($pc_url, '/store/')||strpos($pc_url, 'ctl=store')){
				if(strpos($pc_url, '/store/'))
				$id = $this->get_id($pc_url,'/store/');
				else	
				$id = $this->get_id($pc_url,'act');
				
				if ($id > 0){
					$type = 22;
				}else{
					$type = 0;
				}								
			}else if (strpos($pc_url, '/event/')||strpos($pc_url, 'ctl=event')){
				if(strpos($pc_url, '/event/'))
				$id = $this->get_id($pc_url,'/event/');
				else
				$id = $this->get_id($pc_url,'act');
				if ($id > 0){
					$type = 16;
				}else{
					$type = 0;
				}								
			}else if (strpos($pc_url, '/events')|| strpos($pc_url, 'ctl=events')){				
				//&id=19 || id-19				
				//$id = $this->get_id($pc_url,'act');
				$type = 11;				
			}else{
				if (strpos($pc_url, 'youhui') == false){
					if(strpos($pc_url, '/youhui/'))
					$id = $this->get_id($pc_url,'/youhui/');
					else
					$id = $this->get_id($pc_url,'act');
					//$type = 12;
					if ($id > 0){
						$type = 12;
					}else{
						$type = 0;
					}
					
				}else{
					//&id=19 || id-19
					
					//$id = $this->get_id($pc_url,'act');
					//if ($id > 0){
						$type = 17;
					//}else{
					//	$type = 0;
					//}
				}
			}
		}

		return array('type'=>$type,'id'=>$id);
	}
	

	function get_id($pc_url,$key)
	{
		//if (strpos($pc_url,$key)){
			
			if (strpos($pc_url, '?')){
				
				//无重写
				$key2 = '&'.$key."=";
				//var_dump(strpos('http://www.sh591.cn/index.php?ctl=deal&act=159','&act='));
				if (strpos(htmlspecialchars_decode($pc_url), $key2)){
					$start = strpos(htmlspecialchars_decode($pc_url),$key2) + strlen($key2);		
				}else{
					$key2 = '?'.$key."=";
					$start = strpos(htmlspecialchars_decode($pc_url),$key2) + strlen($key2);
				}
			
				$str = substr(htmlspecialchars_decode($pc_url), $start, strlen($pc_url) - $start);
			
				if (strpos($str, '&')){
					$str = substr($str, 0, strpos($str, '&'));
				}
				return intval($str);
			}else{
				//有重写
				
				/*$key2 = '/'.$key."-";
			
				if (strpos(htmlspecialchars_decode($pc_url),$key2)){
					$start = strpos(htmlspecialchars_decode($pc_url),$key2) + strlen($key2);
				}else{
					$key2 = '-'.$key."-";
					if (strpos(htmlspecialchars_decode($pc_url),$key2)){
						$start = strpos(htmlspecialchars_decode($pc_url),$key2) + strlen($key2);
					}else{
						$key2 = $key."-";
						$start = strpos(htmlspecialchars_decode($pc_url),$key2) + strlen($key2);
					}
				}
				*/
				if (strpos(htmlspecialchars_decode($pc_url),$key)){
					$start = strpos(htmlspecialchars_decode($pc_url),$key) + strlen($key);
					
				}
			
				$str = substr(htmlspecialchars_decode($pc_url), $start, strlen($pc_url) - $start);
			
				//if (strpos($str, '-')){
					//$str = substr($str, 0, strpos($str, '-'));
				//}
				
				return intval($str);
			}
		//}else{
		//	return 0;
		//}
	}
	
}




?>
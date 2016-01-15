<?php
class map
{
	public function index()
	{	
		$root = array();
		$root['return'] = 1;
		
		/*输出分类*/
		$bigcate_list=$GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate where is_delete=0 and is_effect=1 and pid=0 order by sort desc");
		$bcate_list=array();
		$bcate_ids=array();
		$bcate_type=array();
		foreach($bigcate_list as $k=>$v)
		{
			$bcate_ids[]=$v['id'];
			$bcate_type[$v['id']]='';
		}
		$sql1="select dctl.cate_id,dctl.deal_cate_type_id as id,dct.name from ".DB_PREFIX."deal_cate_type as dct left join ".DB_PREFIX."deal_cate_type_link as dctl on dctl.deal_cate_type_id=dct.id where dctl.cate_id in(".implode(',',$bcate_ids).")";
		$sub_cate=$GLOBALS['db']->getAll($sql1);
		
		foreach($sub_cate as $k=>$v)
		{
			$bcate_type[$v['cate_id']][]=$v;
		}
		
		$bcate_list[0]['id']=0;
		$bcate_list[0]['name']='全部分类';
		$bcate_list[0]['bcate_type'][0]['id']=0;
		$bcate_list[0]['bcate_type'][0]['name']='全部分类';
		
		foreach($bigcate_list as $k=>$v)
		{
			
			$bcate_type_array['0']['id']=0;
			$bcate_type_array['0']['cate_id']=$v['id'];
			$bcate_type_array['0']['name']="全部";
			if($bcate_type[$v['id']]==null || $bcate_type[$v['id']]=='')
				$bigcate_list[$k]['bcate_type']=$bcate_type_array;
			else
				$bigcate_list[$k]['bcate_type']=array_merge($bcate_type_array,$bcate_type[$v['id']]);
			
			$bcate_list[]=$bigcate_list[$k];
		}
		$root['bcate_list'] =$bcate_list;
		output($root);
	}
}
?>
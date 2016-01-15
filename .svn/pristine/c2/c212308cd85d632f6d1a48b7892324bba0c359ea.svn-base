<?php
class quan_list{
	public function index()
	{
		$city_id = intval($GLOBALS['request']['city_id']);
		$sql = "select id, name from ".DB_PREFIX."area where pid = 0 and city_id = ".intval($city_id)." order by sort desc ";
		//echo $sql; exit;
		$root['quanlist'] = $GLOBALS['db']->getAll($sql);
	
		output($root);
	}
}
?>
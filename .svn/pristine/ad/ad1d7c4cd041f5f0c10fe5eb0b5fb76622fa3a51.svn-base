<?php
class delivery_region {
	public function index() {
		$root = array ();
		$root ['return'] = 1;
		$sql = "select id,pid,name,region_level from " . DB_PREFIX . "delivery_region order by pid";
		$region_list = $GLOBALS ['db']->getAll ( $sql );
		$root ['region_list'] = $region_list;
		output ( $root );
	}
}

?>
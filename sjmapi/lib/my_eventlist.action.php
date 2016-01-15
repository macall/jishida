<?php
// 参数:email, pwd, page
class my_eventlist
{
	public function index()
	{
		$page = intval ( $GLOBALS ['request'] ['page'] );
		if ($page == 0)
		{
			$page = 1;
		}
		
		$ypoint = $m_latitude = doubleval ( $GLOBALS ['request'] ['m_latitude'] ); // ypoint
		$xpoint = $m_longitude = doubleval ( $GLOBALS ['request'] ['m_longitude'] ); // xpoint
		                                                                             
		// 检查用户,用户密码
		$user =  $GLOBALS['user_info'];
		$user_id = intval ( $user ['id'] );
		if ($user_id > 0)
		{
			$root ['user_login_status'] = 1;
			
			$limit = (($page - 1) * PAGE_SIZE) . "," . PAGE_SIZE;
			
			// $res =
			// m_search_event_list($limit,$cate_id,$city_id,$where,$order,$field_append);
			if ($xpoint > 0)
			{
				$pi = 3.14159265; // 圆周率
				$r = 6378137; // 地球平均半径(米)
				$field_append = ", (ACOS(SIN(($ypoint * $pi) / 180 ) *SIN((ypoint * $pi) / 180 ) +COS(($ypoint * $pi) / 180 ) * COS((ypoint * $pi) / 180 ) *COS(($xpoint * $pi) / 180 - (xpoint * $pi) / 180 ) ) * $r) as distance ";
			} else
			{
				$field_append = "";
			}
			
			$count_sql = "select count(e.id) from " . DB_PREFIX . "event_submit es " . "LEFT JOIN " . DB_PREFIX . "event e on e.id= es.event_id " . " where es.user_id = " . $user_id;
			$events_count = $GLOBALS ['db']->getOne ( $count_sql );
			
			$sql = "select e.* " . $field_append . " from " . DB_PREFIX . "event_submit es " . "LEFT JOIN " . DB_PREFIX . "event e on e.id= es.event_id " . " where es.user_id = " . $user_id . " limit " . $limit;
			$events = $GLOBALS ['db']->getAll ( $sql );
			
			$res = array (
					'list' => $events,
					'count' => $events_count 
			);
			
			
			
			$pattern = "/<img([^>]*)\/>/i";
			$replacement = "<img width=300 $1 />";
			foreach ( $res ['list'] as $k => $v )
			{
				if ($v ['ypoint'] == '')
				{
					$res ['list'] [$k] ['ypoint'] = 0;
				}
				if ($v ['xpoint'] == '')
				{
					$res ['list'] [$k] ['xpoint'] = 0;
				}
				
				
				$res ['list'] [$k] ['icon'] = get_abs_img_root ( $v ['icon'] );
				$res ['list'] [$k] ['distance'] = round ( $v ['distance'] );
				$res ['list'] [$k] ['date_time'] = pass_date ( $v ['submit_begin_time'] );
				$res ['list'] [$k] ['event_begin_time'] = to_date ( $v ['event_begin_time'], 'Y-m-d' );
				$res ['list'] [$k] ['event_end_time'] = to_date ( $v ['event_end_time'], 'Y-m-d' );
				$res ['list'] [$k] ['content'] = preg_replace ( $pattern, $replacement, get_abs_img_root ( $v ['content'] ) );
			}
			
			$root = array ();
			$root ['return'] = 1;
			$root ['item'] = $res ['list'];
			$root ['page'] = array (
					"page" => $page,
					"page_total" => ceil ( $res ['count'] / PAGE_SIZE ),
					"page_size" => PAGE_SIZE 
			);
			$root ['page_title'] = "活动列表";
			output ( $root );
		} else
		{
			$root ['user_login_status'] = 0;
			$root ['status'] = 0;
			$root ['info'] = '请先登录';
			output ( $root );
		}
	}
}
?>
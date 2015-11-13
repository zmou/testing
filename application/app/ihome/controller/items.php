<?php
if( !is_login() )
{
	info_page('请登录后查看');
}
include_once( dirname( __FILE__ ) . '/function.php'   );
$data = array();
$tab_type = 'items';
$data['ci_top_title'] = '物品';
$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;
$page = array_shift( $args );
$page = intval($page) < 1 ?1:intval($page);
$limit = '48';
$start = ($page-1)*$limit;
$uid = format_uid();
$items = lazy_get_data("select sql_calc_found_rows * from `global_user_items` where `uid` = '$uid' and `count` > 0 LIMIT $start , $limit ");
$all = get_count();
$base = '/app/native/ihome/items';
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager(  $page , $page_all , $base );
$data['list'] = array();
if($items)
{
	foreach($items as $v )
	{
		$iid[] = $v['iid'];
		$count[$v['iid']] = $v['count'];
	}
	$items_info = lazy_get_data("select * from `global_items` where `id` IN(".join(',',$iid).") ");
	if($items_info)
	{
		foreach($items_info as $v)
		{
			$v['count'] = $count[$v['id']];
			$data['list'][] = $v;
		}
	}
}
$data['baggage_count'] = count($data['list']);
$weared = array();
$wears = lazy_get_data("select * from `global_items` where id IN(select iid from  `global_items_carry` where uid = '$uid' )");
$data['wear'] = $wears;
layout( $data , 'default' , 'app' );
?>

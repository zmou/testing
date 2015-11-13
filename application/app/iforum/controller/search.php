<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$page = array_shift( $args );
$type = array_shift( $args );
$type = urldecode( $type );

$data['tab_type'] = 'search';
$data['tab_array'] = array('search' => '搜索论坛' );
$page = intval($page) < 1 ?1:intval($page);
$data['page'] = $page;

$types = z(trim( v( 'search' )) );
$type = empty( $types )? $type : $types ;

$limit = 10;
$start = ($page-1)*$limit;

$where = NULL;

if( $type )
{
	$where = " AND `title` LIKE '%".$type."%'";
}

$list = lazy_get_data( "SELECT sql_calc_found_rows * FROM `app_iforum_posts` WHERE `parent_id` = '0' AND `is_active` = '1' $where ORDER BY `last_post_time` DESC  LIMIT $start , $limit " );

$type= urlencode( $type );
$all = get_count();
$base = '/app/native/iforum/search';
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager(  $page , $page_all , $base , $type );

if($list)
{
	foreach($list as $v)
	{
		$uids[$v['uid']] =  $v['uid'];
		$uids[$v['last_uid']] =  $v['last_uid'];
	}
	$data['names'] = iforum_get_names($uids);
}
$data['list'] =$list;
$data['forum'] = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '1' and `is_active` = '1' limit 1");
if(!$data['forum'])
{
	info_page('错误的讨论区id');
}
$data['ci_top_title'] = '搜索论坛';
layout( $data , 'default' , 'app' );
?>
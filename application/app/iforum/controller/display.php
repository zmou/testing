<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$id = intval(array_shift( $args ));

$page = intval(array_shift( $args ));
$data['return_page'] = intval(array_shift( $args ));
$data['return_key'] = intval(array_shift( $args ));
$page = intval($page) < 1 ?1:intval($page);
$start = ($page-1)*$limit;

$data['list'][] = lazy_get_line("SELECT * FROM `app_iforum_posts` WHERE id = '$id' AND `is_active` = 1 AND `parent_id` = '0' LIMIT 1");
if( !$data['list'] )
{
	info_page('错误的文章id');
}
$data['forum'] = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '{$data['list'][0]['fid']}' and `is_active` = '1' limit 1");
if(!$data['forum'])
{
	info_page('错误的论坛id');
}
$data['ci_top_title'] = $data['list'][0]['title'];
$extra = lazy_get_data("SELECT sql_calc_found_rows * FROM `app_iforum_posts` WHERE `is_active` = 1  AND `parent_id` ='$id' LIMIT $start , $limit");
if( $extra )
{
	$data['list'] = array_merge($data['list'] ,$extra );
}
foreach( $data['list'] as $v)
{
	$uids[$v['uid']] = $v['uid'];
}
$all = get_count();
$base = '/app/native/iforum/display/'.$id;
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager(  $page , $page_all , $base ,$data['return_page'] );

$data['names'] = iforum_get_names($uids);
lazy_run_sql("update `app_iforum_posts` set `hit` = `hit` + 1 WHERE id = '$id' AND `is_active` = 1 LIMIT 1");
$data['tab_type'] = 'display';
$data['tab_array'] = array('display' => '浏览文章' );
$data['id'] = $id;
$data['page'] = $page;
$data['limit'] =  $limit;
$data['show_del'] = is_admin()?1:($data['list'][0]['uid'] == format_uid()?1:0 );
layout( $data , 'default' , 'app' );
?>
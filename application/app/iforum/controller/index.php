<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$fid = intval(array_shift( $args ));
$page = array_shift( $args );
$type = intval(array_shift( $args ));
if($type == '-1')
{
	$where = " and `is_selected` = '1'";
}
elseif($type)
{
	$where = " and `type` = '$type'";
}
$data = array();
$data['ci_top_title'] = '主题列表';

$fid = $fid < 1?1:$fid;
$data['forum'] = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '$fid' and `is_active` = '1' limit 1");
if(!$data['forum'])
{
	info_page('错误的讨论区id');
}
$cates = iforum_get_cates( $fid );
if($cates)
{
	foreach($cates as $v)
	{
		$tab_array[$v['key']] = $v['desp'];
	}
}
$data['fid'] = $fid;
$page = intval($page) < 1 ?1:intval($page);
$data['page'] = $page;
$start = ($page-1)*$limit;
$list = lazy_get_data( "SELECT sql_calc_found_rows * FROM `app_iforum_posts` WHERE (`fid` = '$fid' AND `parent_id` = '0' AND `is_active` = 1 $where)OR(`fid` = '$fid' AND `parent_id` = '0' AND `is_active` = 1 AND `top_level` = '1') ORDER BY `top_level` DESC , `last_post_time` DESC LIMIT $start , $limit " );
$all = get_count();
$base = '/app/native/iforum/index/'.$fid;
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
$tab_type = 'index';
$data['tab_type'] = intval($type);
$data['tab_array'] = $tab_array;
layout( $data , 'default' , 'app' );

?>
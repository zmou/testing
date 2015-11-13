<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = 'ITEM列表';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$fid = intval(array_shift($args));

if( !isset( $fid ) || $fid < 1 )
{
	info_page("参数错误!");
}

$fnum = lazy_get_var("SELECT COUNT(*) FROM `app_feed` WHERE `id` = '".intval( $fid )."'");
if( $fnum == '0' )
{
	info_page("没有此条Feed!");
}

$data['items'] = lazy_get_data("SELECT sql_calc_found_rows * FROM `app_feed_item` WHERE `fid` = '".intval( $fid )."' LIMIT 10");


$data['count'] = get_count();

$data['fid'] = $fid;

layout( $data , 'default' , 'app' );
?> 


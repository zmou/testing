<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

$data['ci_top_title'] = '修改'.app_config('model_name').'评论';

if( !is_login() )
{
	info_page('请登录后查看!');
}

$cid = intval(array_shift( $args ));

$uid = format_uid();

$data['com'] = $com = lazy_get_line( "SELECT sql_calc_found_rows * FROM `u2_comment` WHERE `id` = '".intval($cid)."' " );
$num = get_count();

if( $com['uid'] != $uid )
{
	info_page('你没有权限进行此操作!');
}

if( $num == '0' )
{
	info_page('参数错误!');
}

layout( $data , 'default' , 'app' );
?>
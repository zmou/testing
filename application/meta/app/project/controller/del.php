<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$cid = intval(array_shift( $args ));

$uid = format_uid();

$num = lazy_get_var( "SELECT `uid` FROM `app_content_{$mid}` WHERE `id` = '".intval($cid)."' limit 1" );

if( !$num )
{
	info_page('错误的参数');
}

if( $num != $uid )
{
	info_page('您没有权限进行此操作');
}

$com = lazy_get_var("SELECT COUNT(*) FROM `u2_comment` WHERE `tid` = '".intval($cid)."'");

if( $com != '0' )
{
	lazy_run_sql( "DELETE FROM `u2_comment` WHERE `tid` = '".$cid."'" );
	lazy_run_sql( "DELETE FROM `u2_comment_vote` WHERE `tid` = '".$cid."'" );
	lazy_run_sql( "DELETE FROM `u2_comment_reply` WHERE `tid` = '".$cid."'" );
}

lazy_run_sql( "DELETE FROM `app_content_{$mid}` WHERE `id` = '".intval( $cid )."'" );
lazy_run_sql( "DELETE FROM `u2_comment_reply` WHERE `tid` = '".$cid."'" );
lazy_run_sql( "DELETE FROM `u2_manager` WHERE `tid` = '".intval( $cid )."' AND `u2_table` = 'app_content_{$mid}' " );
lazy_run_sql( "DELETE FROM `u2_rate` WHERE `cid` = '".intval( $cid )."' AND `mid` = '$mid' " );
info_page('成功删除'.app_config('model_name') , '/app/native/'.$GLOBALS['app'].'/' , '返回' );

?>	
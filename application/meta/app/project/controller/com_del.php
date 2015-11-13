<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$cid = intval(array_shift( $args ));

$uid = format_uid();

$lines = lazy_get_line( "SELECT sql_calc_found_rows `tid` FROM `u2_comment` WHERE `id` = '".intval($cid)."' AND `uid` = '".intval($uid)."'" );
$num = get_count();
if( $num == '0' )
{
	info_page('你没有权限进行此次操作或参数错误!');
}


lazy_run_sql( "DELETE FROM `u2_comment_vote` WHERE `cid` = '".intval( $cid )."'" );
lazy_run_sql( "DELETE FROM `u2_comment_reply` WHERE `cid` = '".intval( $cid )."'" );
lazy_run_sql( "DELETE FROM `u2_comment` WHERE `id` = '".intval( $cid )."'" );
lazy_run_sql( "UPDATE `app_content_{$mid}` SET `comnum` = `comnum`-1 WHERE `id` = '".intval($tid)."'" );

header( 'Location: /app/native/'.$GLOBALS['app'].'/display/'.$lines['tid'].'' );
die();
?>	
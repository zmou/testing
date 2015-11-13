<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$rid = intval(array_shift( $args ));
$cid = intval(array_shift( $args ));
$uid = format_uid();

if( empty($cid) || $cid < 1 )
{
	info_page('参数错误!');
}

if( empty($rid) || $rid < 1 )
{
	info_page('参数错误!');
}

$num = lazy_get_var( "SELECT COUNT(*) FROM `u2_comment_reply` WHERE `id` = '".intval( $rid )."' AND `cid` = '".intval( $cid )."' AND `uid` = '".intval( $uid )."' " );

if( $num != '0' ) 
{
	lazy_run_sql( "DELETE FROM `u2_comment_reply` WHERE `id` = '".intval( $rid )."'" );
	lazy_run_sql( "UPDATE `u2_comment` SET `rcount` = `rcount`-1 WHERE `id` = '".intval($cid)."'" );
	header( 'Location: /app/native/'.$GLOBALS['app'].'/com_display/'.$cid.'' );
}
else
{
	info_page('无此记录!');
}

die();
?>	
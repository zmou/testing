<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}


$cid = intval( v( 'cid' ) );

$tid = intval( v( 'tid' ) );

$step = intval( v( 'step' ) );

$title = z(trim(v( 'title' )));

$desp = x( v( 'desp' ) );
$uid = format_uid();


if( empty($title) || empty($desp) )
{
	info_page('标题或内容不能为空');
}

$num = lazy_get_var( "SELECT COUNT(*) FROM `u2_comment` WHERE `id` = '".intval($cid)."' AND `tid` = '".intval($tid)."' AND `uid` = '".intval($uid)."'" );

if( $num == '0' )
{
	info_page('你没有权限进行此操作或者参数错误!');
}
lazy_run_sql("update `u2_comment` set `step` = '$step' where `tid` = '$tid' and  `uid` = '$uid' and `mid` = '$mid' ");

lazy_run_sql("UPDATE `u2_comment` SET `title` = ".s($title)." , `content` = ".s($desp)." , `step` = '".intval($step)."' WHERE `id` = '".intval($cid)."' ");
lazy_run_sql( "replace into `u2_rate` (`uid` , `mid` , `cid` , `rate` , `time` )values('$uid','$mid','$tid','$step' , '".date("Y-m-d H:i:s")."')" );
header( 'Location: /app/native/'.$GLOBALS['app'].'/com_display/'.$cid.'' );
die();
?>
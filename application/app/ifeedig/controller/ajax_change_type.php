<?php
//include_once( dirname( __FILE__ ) . '/function.php'   );
header("Content-Type:text/xml;charset=utf-8");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");


if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行此次操作!');
}

$id = intval(array_shift( $args ));
$tid = intval(array_shift( $args ));

if( $id < 0 )
{
	die('0');
}

if( $tid < 1 )
{
	die('0');
}

lazy_run_sql("UPDATE `app_feed_item` SET `tid` = '".intval( $tid )."' WHERE `id` = '".intval( $id )."'");


echo $tid;

die();
?>
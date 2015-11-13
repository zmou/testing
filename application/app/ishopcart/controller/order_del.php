<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$id = intval(array_shift( $args ));
$uid = format_uid();

$num = lazy_get_line("SELECT * FROM `app_shoporder` WHERE `id` = '".intval( $id )."' AND `enter` = '0' LIMIT 1");

if( !$num )
{
	info_page('您没有此条订单');
}

if( $uid != $num['uid'] )
{
	info_page('您没有权限进行此次操作!');
}

lazy_run_sql( "DELETE FROM `app_shoporder` WHERE `id` = '".intval( $id )."'" );

header('Location: /app/native/'.$GLOBALS['app'].'/order_manager');
?>
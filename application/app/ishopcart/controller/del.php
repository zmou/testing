<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$sid = intval(array_shift( $args ));
$uid = format_uid();

if( !isset( $sid ) || empty( $sid ) )
{
	lazy_run_sql( "DELETE FROM `app_shopcart` WHERE `uid` = '".intval( $uid )."'" );
}
else
{
	$num = lazy_get_var( "SELECT COUNT(*) FROM `app_shopcart` WHERE `id` = '".intval( $sid )."' AND `uid` = '".intval( $uid )."'" );
	if( $num == '0' )
	{
		info_page('您没有此件物品!');
	}

	lazy_run_sql( "DELETE FROM `app_shopcart` WHERE `id` = '".intval( $sid )."' AND `uid` = '".intval( $uid )."'" );
}

header('Location: /app/native/'.$GLOBALS['app']);
?>
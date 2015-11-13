<?php
//include_once( dirname( __FILE__ ) . '/function.php'   );
$mid = app_config('mid');
if( !is_login() )
{
	info_page('请登录后查看');
}

$id = intval(array_shift( $args ));
$num = intval(array_shift( $args ));
$uid = format_uid();


$shop_line = lazy_get_line("SELECT * FROM `app_shopcart` WHERE `id` = '".intval( $id )."' LIMIT 1");

if( !$shop_line )
{
	die('error_id');
}

if( $shop_line['uid'] != $uid )
{
	die('error_user');
}

if( $num == '0' || empty( $num ) )
{
	die('0');
}
else
{
	lazy_run_sql("UPDATE `app_shopcart` SET `num` = '".intval( $num )."' WHERE `id` = '".intval( $id )."' AND `uid` = '".intval( $uid )."'");
	echo $num;
	die();
}

?>
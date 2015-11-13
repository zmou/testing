<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}
$wid = intval(array_shift($args));

if( !isset( $wid ) || $wid < 1 )
{
	info_page("ID错误!");
}

$wnum = lazy_get_var("SELECT * FROM `app_feed_recommend` WHERE `id` = '".intval( $wid )."'");

if( !$wnum )
{
	info_page("没有此条记录");
}

lazy_run_sql("DELETE FROM `app_feed_recommend` WHERE `id` = '".intval( $wid )."' LIMIT 1");

header('Location:/app/native/'.$GLOBALS['app'].'/feed');
?> 


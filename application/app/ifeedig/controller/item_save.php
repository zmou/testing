<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '验证文章信息成功!';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$num = lazy_get_var("SELECT COUNT(*) FROM `app_feed_item` WHERE `state` = '1'");
if( $num == '0' )
{
	info_page('没有需要验证的文章!!' , '/app/native/'.$GLOBALS['app'].'/item/1/all');
}

lazy_run_sql("UPDATE `app_feed_item` SET `state` = '2'");

info_page('验证文章信息成功!!' , '/app/native/'.$GLOBALS['app'].'/item/1/all');
?>
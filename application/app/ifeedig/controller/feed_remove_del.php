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

$fid = intval(v('fid'));

if( !isset( $fid ) || $fid < 1 )
{
	info_page("ID错误!");
}

lazy_run_sql("DELETE FROM `app_feed_item` WHERE `fid` = '".intval( $fid )."'");

lazy_run_sql("DELETE FROM `app_feed` WHERE `id` = '".intval( $fid )."' LIMIT 1");

info_page( '成功删除!' , '/app/native/'.$GLOBALS['app'].'/feed' , '点击这里返回!' );
?> 


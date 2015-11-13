<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '订单管理';
if( !is_login() )
{
	info_page('请登录后查看');
}

$uid = format_uid();

$order = lazy_get_var("SELECT COUNT(*) FROM `app_shoporder` WHERE `uid` = '".intval($uid)."'");

$data['orders'] = lazy_get_data("SELECT * FROM `app_shoporder` WHERE `uid` = '".intval($uid)."' AND `enter` = '0' ");


layout( $data , 'default' , 'app' );
?> 
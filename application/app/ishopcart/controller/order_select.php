<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
include_once( dirname( __FILE__ ) . '/carry_config.php'   );
$data['ci_top_title'] = '浏览订单';
if( !is_login() )
{
	info_page('请登录后查看');
}

$data['id'] = $id = intval(array_shift( $args ));
$uid = format_uid();

$data['orders'] = $orders = lazy_get_line("SELECT * FROM `app_shoporder` WHERE `id` = '".intval( $id )."' AND `enter` = '0' LIMIT 1");

if( !$orders )
{
	info_page('您没有此条订单');
}

if( $uid != $orders['uid'] )
{
	info_page('您没有权限进行此次操作!');
}

$data['ware'] = unserialize($orders['ware']);


$data['names'] = lazy_get_data("SELECT * FROM `app_shopuser` WHERE `uid` = '".intval( $uid )."'");
$data['carrys'] = $carrys;
$data['agio'] = $agio;
layout( $data , 'default' , 'app' );
?>
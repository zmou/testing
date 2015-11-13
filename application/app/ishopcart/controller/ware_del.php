<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$id = intval(array_shift( $args ));
$wid = intval(array_shift( $args ));
$uid = format_uid();

$order = lazy_get_line("SELECT * FROM `app_shoporder` WHERE `id` = '".intval( $id )."' AND `enter` = '0' LIMIT 1");
if( !$order )
{
	info_page('您没有此条订单');
}

if( $uid != $order['uid'] )
{
	info_page('您没有权限进行此次操作!');
}

$ware = unserialize($order['ware']);

if( !in_array( $ware[$wid] , $ware ) )
{
	info_page('参数错误!');
}

unset( $ware[$wid] );


$money_sum = 0;
foreach( $ware as $k => $v )
{
	$money_sum = $money_sum + $v['num'] * $v['money'];
}

$money_end = $money_sum * ($agio/100); //应付
$stint = $money_sum - $money_end; //节省

if( !isset( $ware ) || empty( $ware ) )
{
	lazy_run_sql("DELETE FROM `app_shoporder` WHERE `id` = '".intval($id)."'");
	info_page('订单中没有商品了,订单已被删除!','/app/native/'.$GLOBALS['app'],'反回购物车!');
}

$ware = serialize($ware);

lazy_run_sql("UPDATE `app_shoporder` SET `ware` = ".s( $ware )." , `money` = '".$money_end."' , `stint` = '".$stint."' WHERE `id` = '".intval( $id )."'");

header('Location: /app/native/'.$GLOBALS['app'].'/order_modify/'.$id);
?>
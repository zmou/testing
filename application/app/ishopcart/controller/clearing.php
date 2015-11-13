<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
include_once( dirname( __FILE__ ) . '/carry_config.php'   );
$data['ci_top_title'] = '结算';

if( !is_login() )
{
	info_page('请登录后查看');
}

$uid = format_uid();


$data['record'] = $record = lazy_get_data("SELECT sql_calc_found_rows * FROM `app_shopcart` WHERE `uid` = '".$uid."'");
$record_num = get_count();
if( $record_num == '0' )
{
	info_page('您还没有选购商品','/app/native/'.$GLOBALS['app'],'热卖物品!');
}

$money = 0;
foreach( $record as $v  )
{
	 $money = $money + $v['money']*$v['num'];
}

$data['agio'] = $agio;
$data['money_end'] = $money_end = $money * ($agio/100); //应付
$data['stint'] = $money - $money_end; //节省

$data['names'] = lazy_get_data("SELECT * FROM `app_shopuser` WHERE `uid` = '".intval( $uid )."'");

$data['carrys'] = $carrys;
layout( $data , 'default' , 'app' );
?>
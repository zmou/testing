<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '购物车';
if( !is_login() )
{
	info_page('请登录后查看');
}

$uid = format_uid();


$data['record'] = lazy_get_data("SELECT sql_calc_found_rows * FROM `app_shopcart` WHERE `uid` = '".$uid."'");
$data['record_num'] = get_count();

$data['orders'] = lazy_get_var("SELECT COUNT(*) FROM `app_shoporder` WHERE `uid` = '".intval($uid)."'");
$data['order_rest'] = lazy_get_var("SELECT COUNT(*) FROM `app_shoporder` WHERE `uid` = '".intval($uid)."' AND `enter` = '0' ");
$data['agio'] = $agio;

layout( $data , 'default' , 'app' );
?> 
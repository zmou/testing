<?php
$gold = z(v('gold'));
if( !is_login() )
{
	info_page('请登录后查看');
}
if( intval($gold) < 1 )
{
	info_page('请输入正确的金币数目');
}
$now = lazy_get_var( "SELECT gold_count FROM `app_ibank_account` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
if( $now < $gold)
{
	info_page('你银行里没有足够的金币.');
}
$money = intval($gold) * intval(c('gold_convert_value')) ;

$sql = "update app_ibank_account set gold_count = gold_count - $gold WHERE `uid` = '" . format_uid() . "' LIMIT 1";
lazy_run_sql($sql);



$sql = "select * from app_ibank_account where uid='" . format_uid() . "' limit 1";
if( is_array( lazy_get_line($sql) ) )
{
	$sql = "update app_ibank_account set g_count = g_count + $money where uid='" . format_uid() . "' limit 1 ";
}
else
{
	$$key = $money;
	$sql = "insert into app_ibank_account (uid , g_count , gold_count )values ( '" . format_uid() . "','$money','0' ) ";
}
lazy_run_sql($sql);

info_page('已成功兑换到您的银行','/app/native/ibank/convert');
?>
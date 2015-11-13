<?php

if( !is_login() )
{
	info_page('请登录后查看');
}
if( v('email') == NULL )
{
	info_page('请输入转账账号');
}
$money = intval(v('money'));
if( $money < 1 )
{
	info_page('请输入正确的金额');
}
$touid = lazy_get_var("select id from u2_user where u2_email = '".z(v('email'))."' ",db());

if(!$touid)
{
	info_page('没有此用户');
}
if( $touid == _sess('id'))
{
	info_page('不能对自己转账.');
}
$now = lazy_get_var( "SELECT g FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
if( $now < $money)
{
	info_page('你身上没有足够的现金.');
}
$sql = "update app_ihome_user set g = g - $money WHERE `uid` = '" . format_uid() . "' LIMIT 1";
lazy_run_sql($sql);
$sql = "select * from app_ibank_account where uid='$touid' limit 1";
if( is_array( lazy_get_line($sql) ) )
{
	$sql = "update app_ibank_account set g_count = g_count + $money where uid='$touid' limit 1 ";
}
else
{
	$$key = $money;
	$sql = "insert into app_ibank_account (uid , g_count , glod_count )values ( '$touid','$money','0' ) ";
}
lazy_run_sql($sql);
info_page('已成功转账到您的指定的用户.','/app/native/ibank/transfer');

?>
<?php
$code = z(v('code'));
if( !is_login() )
{
	info_page('请登录后查看');
}
if( !$code )
{
	info_page('请输入充值卡号码');
}

$sql = "select count(*) from u2_recharge_card where u2_is_use = '0' and u2_card_no = '$code' ";

if( !lazy_get_var($sql) )
{
	info_page('你输入的充值卡卡号错误,或者已经使用');
}
$money = intval(c('recharge_card_value'));
$key = c('card_paied_gold')?'gold_count':'g_count';
$sql = "select * from app_ibank_account where uid='".format_uid()."' ";
if( is_array( lazy_get_line($sql) ) )
{
	$sql = "update app_ibank_account set $key = $key + $money where uid='".format_uid()."' limit 1 ";
}
else
{
	$$key = $money;
	$sql = "insert into app_ibank_account (uid , g_count , gold_count )values ( '".format_uid()."','$g_count','$gold_count' ) ";
}
lazy_run_sql($sql);
$sql = "update u2_recharge_card set u2_is_use = '1' where u2_card_no = '$code' limit 1";
lazy_run_sql($sql);
if( c('card_paied_gold')  )
{
	$paied = $money.' 金币';
	$backurl = '/app/native/ibank/convert/';
	$backtext = '点此兑换';
}
else
{
	$paied = $money.' 银币';
	$backurl = '/app/native/ibank/index/';
	$backtext = '点此查看';
}
$text = '已成功充值'.$paied.'到您的银行帐户';
info_page($text , $backurl , $backtext );
?>
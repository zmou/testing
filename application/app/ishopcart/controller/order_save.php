<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$uid = format_uid();

$carry = intval( v('carry') );
$toname = z( trim(v('username')) );


if( $toname != '' && intval( $toname ) != '0' )
{
	$suid = $toname;
	$toname = lazy_get_var("SELECT `name` FROM `app_shopuser` WHERE `id` = '".$toname."'");
}
else
{
	$toname = z(trim(v('custom_name')));
}

$totell = z( trim(v('usertell')) );
$tocode = intval( v('usercode') );
$tohome = z( trim(v('userhome')) );
$pack = intval( v('pack') );
$need = z( v('need') );

if( $tocode == '0' || empty($tocode) )
{
	info_page('请输入正确的邮编!');
}

$shop = lazy_get_data("SELECT * FROM `app_shopcart` WHERE `uid` = '".intval( $uid )."'");

if( !$shop )
{
	info_page('您还没有选购商品','/app/native/'.$GLOBALS['app'],'热卖物品!');
}

$money = 0;
foreach( $shop as $v )
{
	$ware[$v['id']] = $v;
	$money = $money+$v['money']*$v['num'];
}

$ware = serialize($ware); //购买的物品


if( empty($toname) || empty($totell) || empty($tohome) )
{
	info_page('收货信息不能为空!');
}

$money_end = $money * ($agio/100); //应付
$stint = $money - $money_end; //节省

$data = array();
$data['uid'] = intval( $uid );
$data['toname'] = $toname;
$data['totell'] = $totell;
$data['tocode'] = intval($tocode);
$data['tohome'] = $tohome;
$data['need'] = $need;
$data['carry_type'] = intval( $carry );
$data['pack_type'] = intval( $pack );
$data['ware'] = $ware;
$data['money'] = $money_end;
$data['stint'] = $stint;
$data['time'] = time();
$data['enter'] = 0;



global $CI;
$CI->load->database();
$CI->db->insert( 'app_shoporder' , $data );
$data['id'] = $id = $CI->db->insert_id();

$name_num = lazy_get_var("SELECT COUNT(*) FROM `app_shopuser` WHERE `uid` = '".intval( $uid )."' AND `name` = ".s( $toname )." ");
if( $name_num == '0' )
{
	$sql  = "INSERT INTO `app_shopuser` (`uid`, `name`, `tell`, `code`, `home`)";
	$sql .= "VALUES ('".intval( $uid )."' , ".s( $toname ).", ".s($totell).", '".intval($tocode)."', ".s($tohome).")";
	lazy_run_sql( $sql );
}
else
{
	lazy_run_sql("UPDATE `app_shopuser` SET `tell` = ".s($totell)." , `code` = '".intval($tocode)."', `home` = ".s($tohome)." WHERE `id` = '".intval($suid)."'");
}

lazy_run_sql("DELETE FROM `app_shopcart` WHERE `uid` = '".intval( $uid )."'");

layout( $data , 'default' , 'app' );
?>
<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}


$id = intval( v('oid') );
$uid = format_uid();
$nums = v('num'); //物品数
$carry = intval( v('carry') );
$toname = z(trim(v('username')));

if( $toname != '0' )
{
	$suid = $toname;
	$toname = lazy_get_var("SELECT `name` FROM `app_shopuser` WHERE `id` = '".$toname."'");
}
else
{
	$toname = z(trim(v('custom_name')));
}

$totell = z( v('usertell') );
$tocode = intval( v('usercode') );
$tohome = z( v('userhome') );
$pack = intval( v('pack') );
$need = z( v('need') );

if( empty($tocode) || $tocode == '0' )
{
	info_page('请填写正确的邮编!');
}

if( empty($toname) || empty($totell)  || empty($tohome) )
{
	info_page('收货信息不能为空!');
}

$line = lazy_get_line("SELECT * FROM `app_shoporder` WHERE `id` = '".intval( $id )."' AND `enter` = '0' LIMIT 1");

if( !$line )
{
	info_page('您没有此条订单');
}

if( $uid != $line['uid'] )
{
	info_page('您没有权限进行此次操作!');
}

$ware = unserialize($line['ware']);

$i=0;
$money_sum = 0;
foreach( $ware as $k => $v )
{
	$ware[$k]['num'] = $nums[$i];
	$money_sum = $money_sum + $nums[$i]*$v['money'];
	$i++;
}

$ware = serialize($ware); 

$money_end = $money_sum * ($agio/100); //应付
$stint = $money_sum - $money_end; //节省

$data = array();
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


global $CI;
$CI->load->database();
$CI->db->where('id',$id);
$CI->db->update( 'app_shoporder' , $data );

$name_num = lazy_get_var("SELECT COUNT(*) FROM `app_shopuser` WHERE `uid` = '".intval( $uid )."' AND `name` = ".s( $toname )." ");
if( $name_num == '0' )
{
	$sql  = "INSERT INTO `app_shopuser` (`uid`, `name`, `tell`, `code`, `home`) VALUES";
	$sql .= "('".intval( $uid )."' , ".s( $toname ).", ".s($totell).", '".intval($tocode)."', ".s($tohome).")";
	lazy_run_sql( $sql );
}
else
{
	lazy_run_sql("UPDATE `app_shopuser` SET `tell` = ".s($totell)." , `code` = '".intval($tocode)."', `home` = ".s($tohome)." WHERE `id` = '".intval($suid)."'");
}

header('Location: /app/native/'.$GLOBALS['app'].'/order_modify/'.$id);
?>

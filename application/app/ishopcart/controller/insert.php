<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$uid = format_uid();
$folder = z(array_shift( $args ));
$cid = intval(array_shift( $args ));
$mid = app_config('mid' , $folder );
$bind = app_config('bind' , $folder );

if( !isset($mid) )
{
	info_page('没有此表!');
}

if( $bind['price'] == '' || $bind['price'] <= '0' )
{
	info_page('价格错误!');
}

foreach( $bind as $k => $v )
{
	$selected[] = " `$v` as $k ";
}

$com = lazy_get_line("SELECT  ".join(',',$selected)." FROM `app_content_{$mid}` WHERE `id` = '".intval( $cid )."'");
$cnum = get_count();

if( $cnum == '0' )
{
	info_page('没有此件物品!');
}

$num = lazy_get_var("SELECT COUNT(*) FROM `app_shopcart` WHERE `cid` = '".intval( $cid )."' AND `uid` = '".intval( $uid )."'");
if( $num != '0')
{
	lazy_run_sql( "UPDATE `app_shopcart` SET `num` = `num`+1 WHERE `cid` = '".intval( $cid )."' AND `uid` = '".intval( $uid )."'" );
}
else
{
	lazy_run_sql("INSERT INTO `app_shopcart` ( `uid`, `cid`, `name`, `desp`, `num`, `money`, `date`, `folder` ) VALUES ( '".intval( $uid )."' , '".intval( $cid )."' , ".s( $com['title'] )." , ".s( $com['desp'] )." , '1' , ".s( $com['price'] )." , '".date('Y-m-d H:i:s')."' , ".s($folder)." )");
}

header('Location: /app/native/'.$GLOBALS['app']);
?>
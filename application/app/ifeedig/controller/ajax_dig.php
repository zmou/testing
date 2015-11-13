<?php
//include_once( dirname( __FILE__ ) . '/function.php'   );
header("Content-Type:text/xml;charset=utf-8");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");


if( !is_login() )
{
	info_page('请登录后查看');
}

$iid = intval(array_shift( $args ));

$uid = format_uid();

$CookieTime = 360*( 24*3600 );
set_cookie( 'money_dig_'.$iid.'_'.$uid , '1' , $CookieTime );

$inum= lazy_get_var("SELECT COUNT(*) FROM `app_feed_dig` WHERE `iid` = '".intval($iid)."' AND `uid` = '".$uid."'");
if( $inum > 0 )
{
	echo '0';
	die();
}

lazy_run_sql("INSERT INTO `app_feed_dig` (`uid`, `iid`, `time`) VALUES ('".$uid."' , '".intval($iid)."' , '".date('Y-m-d H:i:s')."')");

lazy_run_sql("UPDATE `app_feed_item` SET `dig` = `dig`+1 WHERE `id` = '".intval($iid)."' LIMIT 1");


$dig = lazy_get_var("SELECT `dig` FROM `app_feed_item` WHERE `id` = '".intval($iid)."' LIMIT 1");
echo $dig;
die();
?>
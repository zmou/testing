<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$tid = intval(array_shift( $args ));
$text = trim(urldecode( array_shift( $args )));
if( empty( $tid ) || empty( $text ) )
{
	info_page('参数错误');
}
$uid = format_uid();

lazy_run_sql("DELETE FROM `u2_vote` WHERE `mid` = '".intval($mid)."' AND `uid` = '".intval($uid)."' AND `tid` = '".intval($tid)."' ");

lazy_run_sql("INSERT INTO `u2_vote` (`mid`, `uid`, `tid`, `text`) VALUES ('".intval($mid)."', '".intval($uid)."', '".intval($tid)."', ".s($text)." )");

header( 'Location: /app/native/'.$GLOBALS['app'].'/display/'.$tid.'' );
die();
?>

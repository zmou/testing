<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$cid = intval(v('cid'));
$desp = x(v('con'));
$uid = format_uid();
$com = lazy_get_line( "SELECT * FROM `u2_comment` WHERE `id` = '".intval($cid)."' limit 1 " );
if( !$com )
{
	info_page('没有此条记录!');
}
$tid = $com['tid'];
if( empty($desp) )
{
	info_page('回复内容不能为空!');
}

lazy_run_sql( "INSERT INTO `u2_comment_reply` ( `mid` ,`cid`, `tid`, `uid`, `content`, `time`) VALUES ( '".intval($mid)."','".intval($cid)."' , '".intval($tid)."' , '".intval($uid)."' , ".s($desp)." , '".date('Y-m-d H:i:s')."')" );

lazy_run_sql( "UPDATE `u2_comment` SET `rcount` = `rcount`+1 WHERE `id` = '".intval($cid)."'" );

header( 'Location: /app/native/'.$GLOBALS['app'].'/com_display/'.$cid.'' );

?>
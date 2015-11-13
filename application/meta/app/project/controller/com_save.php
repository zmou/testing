<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$id = intval(v('id'));
$title = z(trim(v('title')));
$step = intval(v('step'));
$desp = x(v('desp'));
$uid = format_uid();
if( empty($title) || empty($desp) )
{
	info_page('标题或内容不能为空!');
}

if( $id < 1 )
{
	info_page('参数错误!');
}
lazy_run_sql("update `u2_comment` set `step` = '$step' where `tid` = '$id' and  `uid` = '$uid' and `mid` = '$mid' ");
$sql = "INSERT INTO `u2_comment` (`tid`, `mid`, `uid`, `title`, `content`, `time`, `step`) VALUES ('".intval($id)."' , '".intval($mid)."' , '".intval($uid)."' , ".s($title)." , ".s($desp)." , '".date('Y-m-d H:i:s')."' , '".intval($step)."')";

lazy_run_sql( $sql );

lazy_run_sql( "UPDATE `app_content_{$mid}` SET `comnum` = `comnum`+1 WHERE `id` = '".intval($id)."'" );

lazy_run_sql( "replace into `u2_rate` (`uid` , `mid` , `cid` , `rate` , `time` )values('$uid','$mid','$id','$step' , '".date("Y-m-d H:i:s")."')" );


header( 'Location: /app/native/'.$GLOBALS['app'].'/display/'.$id.'' );
die();
?>
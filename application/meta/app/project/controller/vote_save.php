<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$id = array_shift( $args );
$uid = format_uid();
$com = lazy_get_line( "SELECT sql_calc_found_rows * FROM `u2_comment` WHERE `id` = '".intval($id)."'" );
$tid = $com['tid'];
$sql = get_count();
if( $sql == '0' )
{
	info_page('没有此条记录!');
}

$num = lazy_get_var( "SELECT COUNT(*) FROM `u2_comment_vote` WHERE `cid` = '".intval( $id )."' AND `uid` = '".intval( $uid )."' " );
if( $num == '0' )
{
	lazy_run_sql( "INSERT INTO `u2_comment_vote` ( `mid` ,`tid`, `cid`, `uid`) VALUES ( '".intval($mid)."','".intval( $tid )."' , '".intval( $id )."' , '".intval( $uid )."');" );
	lazy_run_sql( "UPDATE `u2_comment` SET `dig` = `dig`+1 WHERE `id` = '".intval( $id )."'" );
}
else
{
	info_page('你已经推荐过了!');
}

header( 'Location: /app/native/'.$GLOBALS['app'].'/com_display/'.$id.'' );

?>
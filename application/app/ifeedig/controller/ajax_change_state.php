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

if( !is_admin() )
{
	info_page('你没有权限进行此次操作!');
}

$id = intval(array_shift( $args ));
$sid = intval(array_shift( $args ));

lazy_run_sql("UPDATE `app_feed_item` SET `state` = '".intval( $sid )."' WHERE `id` = '".intval( $id )."'");

//$uid = format_uid();
if( $sid == '1' )
{
	echo "<span style='float:right'>
		  <span id='state_".$id."'><img src='/static/images/tick.gif'></span>
		  <INPUT TYPE='checkbox' onclick='Change_state( ".$id." , 2)'>通过
		  </span>";
}
else
{
	echo "<span style='float:right'>
		  <span id='state_".$id."'><img src='/static/images/tick.gif'></span>
		  <INPUT TYPE='checkbox' onclick='Change_state( ".$id." , 1)' checked>通过
		  </span>";
}


die();
?>
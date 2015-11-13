<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$id = intval(array_shift( $args ));
$page = intval(array_shift( $args ));
$return_page= intval(array_shift( $args ));
$return_key = intval(array_shift( $args ));
$line = lazy_get_line("SELECT * FROM `app_iforum_posts` WHERE id = '$id' AND `is_active` = 1 LIMIT 1");

if( !$line || $line['uid'] != format_uid() )
{
	info_page('你没有权限进行此操作');
}
if( $line['parent_id'] != '0' )
{
	$desp = v('desp');
	if(!$desp)
	{
		info_page('内容不能为空@');
	}
	lazy_run_sql("update `app_iforum_posts` set `desp` = ".s($desp)." WHERE id = '$id' LIMIT 1 ");
}
else
{
	$title = htmlspecialchars( z(v('title')) );
	$type = intval(v('type'));
	$desp = v('desp');
	if( !$title || !$desp )
	{
		info_page('标题和内容不能为空');
	}

	lazy_run_sql("update `app_iforum_posts` set `title` = '$title' ,`desp`=".s($desp).", `type` = '$type' WHERE id = '$id' LIMIT 1 ");
}
$dis_id =  $line['parent_id'] == '0'?$id:$line['parent_id'];
header("Location: /app/native/iforum/display/".$dis_id.'/'.$page.'/'.$return_page.'/'.$return_key );
?>

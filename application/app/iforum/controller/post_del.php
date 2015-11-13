<?php

if( !is_login() )
{
	info_page('请登录后查看');
}
$id = intval(array_shift( $args ));
$return_page = intval(array_shift( $args ));
$return_key = intval(array_shift( $args ));
$item = lazy_get_line("SELECT * FROM `app_iforum_posts` WHERE id = '".$id."' AND `parent_id` = '0' AND `is_active` = 1 LIMIT 1");
if(!$item )
{
	info_page('错误的参数');
}
$uid = format_uid();
if( !is_admin() && $uid != $item['uid'] )
{
	info_page('你没有权限进行此操作');
}
$sql = "update `app_iforum_posts` set `is_active` = '0'  where `id` = '".$id."' and `parent_id` = '0' ";
lazy_run_sql($sql);
header('Location: /app/native/'.$GLOBALS['app'].'/index/'.$item['fid'].'/'.$return_page .'/'.$return_key);
?>
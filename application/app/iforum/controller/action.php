<?php
if( !is_admin() )
{
	die('你没有权限进行此操作');
}
$ids = v('ids');
$action = v('action');
if( !$ids || !is_array($ids) )
{
	die('请选择文章');
}
if($action == 'top')
{
	$set = " `top_level` = '1' ";
}
elseif($action == 'untop')
{
	$set = " `top_level` = '0' ";
}
elseif($action == 'sel')
{
	$set = " `is_selected` = '1' ";
}
elseif($action == 'unsel')
{
	$set = " `is_selected` = '0' ";
}
elseif($action == 'del')
{
	$set = " `is_active` = '0' ";
}
else
{
	die('错误的参数');
}
$sql = "update `app_iforum_posts` set $set where `id` in(".join(',',$ids).") and `parent_id` = '0' ";

lazy_run_sql($sql);
?>
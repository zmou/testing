<?php
if( !is_login() )
{
	die('请登陆后操作');
}
include_once( dirname( __FILE__ ) . '/function.php'   );
$id = intval(array_shift( $args ));
$floor = intval(array_shift( $args ));
$line = lazy_get_line("SELECT * FROM `app_iforum_posts` WHERE id = '$id' AND `is_active` = 1 LIMIT 1");
if(!$line)
{
	die('错误的参数');
}

$post_uid = $line['parent_id']?lazy_get_var("SELECT `uid` FROM `app_iforum_posts` WHERE id = '{$line['parent_id']}' AND `parent_id` = '0' AND `is_active` = 1 LIMIT 1"):$line['uid'];
$uid = format_uid();

if( !is_admin() && $uid != $line['uid'] && $uid != $post_uid  )
{
	die('你没有权限进行此操作');
}
$del_uid = $uid;
lazy_run_sql("update `app_iforum_posts` set `del_uid` = '$uid' WHERE id = '$id' LIMIT 1 ");

echo '成功删除文章';
$name = $uid == $post_uid?'楼主':($uid == $line['uid']?'发布者':'管理员');
$js_data[] = '$("display_img_'.$id.'").setHTML("")';
$js_data[] = '$("display_info_'.$id.'").setHTML(\'<span class="r">'.show_floor( $floor).'</span><del>该楼已被'.$name.'删除</del>\')';
echo '<script>'.join(';',$js_data).';</script>';
?> 
<?php
if( !is_login() )
{
	info_page('请登录后查看');
}
include_once( dirname( __FILE__ ) . '/function.php'   );
$id = intval(array_shift( $args ));
$return_page = intval(array_shift( $args ));
$return_key = intval(array_shift( $args ));
$desp = v('desp');
if(!$desp)
{
	info_page('回复内容不能为空');
}
$check = lazy_get_line("SELECT * FROM `app_iforum_posts` WHERE id = '$id' AND `is_active` = 1 AND `parent_id` = '0' LIMIT 1");
if( !$check )
{
	info_page('错误的参数');
}
$forum = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '{$check['fid']}' and `is_active` = '1' limit 1");
if(!$forum)
{
	info_page('错误的论坛id');
}
$now = date("Y-m-d H:i:s");
lazy_run_sql("update `app_iforum_posts` set `reply` = `reply` + 1 , `last_post_time` = '$now' ,`last_uid` = '".format_uid()."' WHERE id = '$id' AND `is_active` = 1 AND `parent_id` = '0' LIMIT 1");
global $CI;
$CI->load->database();
$data['fid'] = $check['fid'];
$data['parent_id'] = $id;
$data['title'] = '';
$data['desp'] = r($desp);
$data['floor'] = intval(v('floor'));
$data['last_uid'] = $data['uid'] = format_uid();
$data['last_post_time'] = $data['time'] = $now;
$CI->db->insert( 'app_iforum_posts' , $data );
$count = lazy_get_var("select count(*) from `app_iforum_posts`  WHERE fid = '{$data['fid']}' AND `parent_id` = '$id' AND `is_active` = 1 AND `parent_id` != '0' ");
$page_all = ceil($count / $limit);
$rid = intval(v('rid'));
if( !$rid  )
{
	$nuid = $check['uid'];
}
else
{
	$temp = lazy_get_var("SELECT `uid` FROM `app_iforum_posts` WHERE id = '$rid' AND `is_active` = 1 AND `parent_id` = '$id' LIMIT 1");
	$nuid = $temp?$temp:$check['uid'];
}
if( $nuid != format_uid() )
{
	$title = '<a href="/user/space/' .  format_uid() . '" target="_blank">' .  _sess('u2_nickname') . '</a>回复了主题<a href="/app/native/iforum/display/'.$id.'/'.$page_all.'/" target="_blank">'.strip_tags($check['title']).'</a>';
	send_to_notice( $nuid , 'iforum' , $title );
}
$title = '<a href="/user/space/' . format_uid() . '" target="_blank">' . _sess('u2_nickname') . '</a>回复了主题<a href="/app/native/iforum/display/'.$id.'/" target="_blank">'.strip_tags($check['title']).'</a>';
send_to_feed( format_uid() , 'iforum' , $title );
header("Location: /app/native/iforum/display/".$id."/".$page_all."/".$return_page.'/'.$return_key);
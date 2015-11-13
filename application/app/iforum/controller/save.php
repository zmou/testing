<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$fid = intval(array_shift( $args ));
$fid = $fid < 1?1:$fid;
$forum = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '$fid' and `is_active` = '1' limit 1");
if(!$forum)
{
	info_page('错误的讨论区id');
}
$title = htmlspecialchars( z(v('title')) );
$desp = r(v('desp'));
if( !$title || !$desp )
{
	info_page('标题和内容不能为空');
}
$data['fid'] = $fid;
$data['parent_id'] = '0';
$data['title'] = $title;
$data['desp'] = $desp;
$data['time'] = $data['last_post_time'] = date("Y-m-d H:i:s");
$data['uid'] = $data['last_uid'] = format_uid(); 
$data['type'] = intval(v('type'));
global $CI;
$CI->load->database();
$CI->db->insert( 'app_iforum_posts' , $data );
$tid = $CI->db->insert_id();
$title = '<a href="/user/space/' . format_uid() . '" target="_blank">' . _sess('u2_nickname') . '</a>发起了一个新主题<a href="/app/native/iforum/display/'.$tid.'/" target="_blank">'.strip_tags( $title ).'</a>';
send_to_feed( format_uid() , 'iforum' , $title );
info_page('主题发布成功','/app/native/'.$GLOBALS['app'].'/index/'.$fid ,'返回讨论区');
?>

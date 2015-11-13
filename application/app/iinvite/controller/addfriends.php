<?php
if( !is_login() )
{
	info_page('请登录后查看');
}
$uids = v('uids');
if( !$uids )
{
	info_page('请提交正确的联系人');
}
$user = lazy_get_data('select id, u2_nickname from u2_user where id in('.join(',',$uids ).')' );
if( !$user )
{
	info_page('请提交正确的联系人');
}
global $CI;
$CI->load->model('User_model', 'user',true );
foreach( $user as $v )
{
	$CI->user->friend_doaction('add' , $v['id'] );
}
header('Location: /app/native/iinvite/showmail');
?>
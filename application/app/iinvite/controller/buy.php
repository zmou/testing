<?php 
if( !is_login() )
{
	die('请登陆后操作');
}
if( !c('invite_active') )
{
	die('目前网站没有开发邀请注册');
}
$number = intval( array_shift($args) );
if( $number < 1 )
{
	die('请输入正确的数字');
}
$CI = &get_instance();
$CI->load->model('Invite_model', 'invite', TRUE);

$limit = intval( $CI->invite->get_invite_limit() );
if( $number > $limit  )
{
	die('你购买的邀请码超过限制');
}
if( $CI->invite->buy( $number ))
{
	die('购买邀请码成功');
}
else
{
	die('你身上没有足够的金钱购买');
}
?>

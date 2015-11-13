<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '邀请好友';

$tab_type = 'index';

$data['user'] = NULL;
$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;
$uid = format_uid();
$emails = lazy_get_data("select * from app_iinvite_emails where uid = '".$uid."'");
if( !$emails )
{
	info_page('没有找到联系人');
}
foreach( $emails as $v )
{
	$contacts[strtolower( $v['email'] )] = strtolower( $v['email'] );
}
$users = lazy_get_data("select id, u2_email , u2_nickname from u2_user where LCASE(u2_email)in('".join("','",$contacts )."')");
if($users)
{
	$fids = get_friends_by_uid();
	foreach( $users as $v )
	{
		if( $uid != $v['id'] )
		{
			$is_friend = in_array( $v['id'] ,$fids )?1:0;
			$v['is_friend'] = $is_friend;
			$data['user'][$v['id']] = $v;
		}
		unset( $contacts[$v['u2_email']] );
	}
}
if( $contacts )
{
	lazy_run_sql("update app_iinvite_emails set no_in_site = '1' where LCASE(email)in('".join("','",$contacts )."') ");
}
if( !$data['user'] )
{
	header('Location: /app/native/iinvite/showmail');
	die();
}
layout( $data , 'default' , 'app' );



?>
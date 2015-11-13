<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '邀请好友';

$tab_type = 'index';

$data['type'] = array_shift($args);
$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;

if(  $data['type'] == 'link' )
{
	if( c('register_is_open') )
	{
		$show = '<input id="show_link" name="show_link" value="http://'.$_SERVER['HTTP_HOST'].'/user/register/invuid/'.format_uid().'" class="text" style="width:300px;">&nbsp;&nbsp;<input type="button" value="复制" class="button" onclick="app_copy_value(\'show_link\')">';
	}
	elseif( c('invite_active') )
	{
		$invite = lazy_get_line("select * from u2_invite where u2_is_use = '0' and u2_uid = '" . format_uid() . "' and  u2_is_copied = '0' limit 1 ");
		if( !$invite )
		{
			$CI = &get_instance();
			$CI->load->model('Invite_model', 'invite', TRUE);
			$CI->invite->buy( 1 );
			$invite = lazy_get_line("select id , u2_invite_code  from u2_invite where  u2_uid = '" . format_uid() . "' and u2_is_use = '0' and  u2_is_copied = '0' LIMIT 1 ");
		}
		
		$icode = $invite['u2_invite_code'];
		$show = '<input id="show_link" name="show_link" value="http://'.$_SERVER['HTTP_HOST'].'/gate/index/'.$icode.'" class="text" style="width:300px;">&nbsp;&nbsp;<input type="button" value="复制" class="button" onclick="app_copy_icode_link(\''.$invite['id'].'\')">';
	}
	else
	{
		$show = '对不起,目前本网站禁止注册';
	}
	$data['show'] = $show;
}

layout( $data , 'default' , 'app' );
?>


<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	die('请登录后查看');
}

$emails = is_array( v('emails') )? v('emails'):array();
$extra = v('extar_mail');
$extra_mail = $extra ? explode("\n",$extra) : array();
$emails = array_merge($emails ,$extra_mail );

foreach( $emails as $k => $v )
{
	if( !app_checkemail($v) )
		unset( $emails[$k] );
}
if( !$emails )
{
	die('请填写正确的邮箱');
}
if( c('register_is_open') )
{
	$title = _sess('u2_nickname').'(' . _sess('u2_email') . ')邀请您加入- '.c('site_name');
	$message = '
		<TABLE>
		<TR>
			<TD valign="top" style="padding:10px;"><img src="http://'.$_SERVER['HTTP_HOST'].show_user_icon('big').'" style="border:1px solid #c8c8c8;padding:1px;"></TD>
			<TD valign="top" style="padding:10px;">
			
				<p><strong>亲爱的朋友,我是'. _sess('u2_nickname') .'～</strong></p>
				
				最近我加入了 '. c('site_name') .' ，每天在这里种仙豆养宠物，感觉还不错哦．你也来和我一起玩，顺便帮我挣点银币吧，呵呵．<br/>

				<br/>点下边的链接就可以了，我会仔细的给你做向导的哦～<br/><br/>
				<a href="http://'.$_SERVER['HTTP_HOST'].'/user/register/invuid/'.format_uid().'">http://'.$_SERVER['HTTP_HOST'].'/user/register/invuid/'.format_uid().'</a>
				<p>如果点击无效,你可以复制上边的链接,粘贴到地址栏,再按回车就行了</p><br/>
				<p>邀请函我好不容易才弄到的呢，如果你没兴趣的话，就转发给你的朋友吧～</p>
			</TD>
		</TR>
		</TABLE>
		';
	/* $message = '<TABLE><TR><TD valign="top"><img src="http://'.$_SERVER['HTTP_HOST'].show_user_icon('big').'" style="border:1px solid #c8c8c8;padding:1px;"></TD><TD valign="top" style="padding:10px;"><b>你好我是'._sess('u2_nickname').'我在'.c('site_name').'上建立了个人主页，请你也加入并成为我的好友。</b><br/><br/>请点击以下链接，接受好友邀请：<br/><a href="http://'.$_SERVER['HTTP_HOST'].'/user/register/invuid/'.format_uid().'">http://'.$_SERVER['HTTP_HOST'].'/user/register/invuid/'.format_uid().'</a></TD></TR></TABLE>';*/

	if( sendmail( $emails , $title , $message ,  _sess('u2_email') ))
	{
		$sql = "delete from app_iinvite_emails where uid = '".format_uid()."' ";
		lazy_run_sql($sql);
		die('发送邮件成功.');
	}
	else
	{
		die('邮件系统不可用,请稍候在试');
	}
}
elseif( c('invite_active') )
{
	$invites = lazy_get_data("select id , u2_invite_code  from u2_invite where u2_uid = '" . format_uid() . "' and u2_is_use = '0' and  u2_is_copied = '0' ");
	if( count($invites) < count($emails) )
	{
		$limit = count($emails) - count($invites) ;
		if( c('invite_price') == '0' )
		{
			$CI = &get_instance();
			$CI->load->model('Invite_model', 'invite', TRUE);
			$CI->invite->buy( $limit );
			$invites = lazy_get_data("select id , u2_invite_code  from u2_invite where  u2_uid = '" . format_uid() . "' and u2_is_use = '0' and  u2_is_copied = '0' ");
		}
		else
		{
			die('你还差'.$limit.'个邀请码来进行此操作,请购买邀请码后操作');
		}
	}
	
	$title = _sess('u2_nickname').'(' . _sess('u2_email') . ')邀请您加入 - '.c( 'site_name' );
	$i = 0;
	foreach( $emails as $v )
	{
		$icode = $invites[$i]['u2_invite_code'];
		$copyid[] = $invites[$i]['id'];
		
		$message = '<TABLE>
		<TR>
			<TD valign="top" style="padding:10px;"><img src="http://'.$_SERVER['HTTP_HOST'].show_user_icon('big').'" style="border:1px solid #c8c8c8;padding:1px;"></TD>
			<TD valign="top" style="padding:10px;">
			
				<p><strong>亲爱的朋友,我是'. _sess('u2_nickname') .'～</strong></p>
				
				最近我加入了 '. c('site_name') .' ，每天在这里种仙豆养宠物，感觉还不错哦．你也来和我一起玩，顺便帮我挣点银币吧，呵呵．<br/>

				<br/>点下边的链接就可以了，我会仔细的给你做向导的哦～<br/><br/>
				<a href="http://'.$_SERVER['HTTP_HOST'].'/gate/index/'.$icode.'">http://'.$_SERVER['HTTP_HOST'].'/gate/index/'.$icode.'</a>
				<p>如果点击无效,你可以复制上边的链接,粘贴到地址栏,再按回车就行了</p><br/>
				<p>邀请函我好不容易才弄到的呢，如果你没兴趣的话，就转发给你的朋友吧～</p>
			</TD>
		</TR>
		</TABLE>';
		
		/*
		$message = '<TABLE><TR><TD valign="top"><img src="http://'.$_SERVER['HTTP_HOST'].show_user_icon('big').'" style="border:1px solid #c8c8c8;padding:1px;"></TD><TD valign="top" style="padding:10px;"><b>你好我是'._sess('u2_nickname').'我在'.c('site_name').'上建立了个人主页，请你也加入并成为我的好友。</b><br/><br/>请点击以下链接，接受好友邀请：<br/><a href="http://'.$_SERVER['HTTP_HOST'].'/gate/index/'.$icode.'">http://'.$_SERVER['HTTP_HOST'].'/gate/index/'.$icode.'</a></TD></TR></TABLE>';*/
		if( !sendmail( $v , $title , $message ,  _sess('u2_email') )  )
		{
			die('邮件系统不可用,请稍候在试');
		}
		$i++;
	}
	$sql = "update u2_invite set u2_is_copied = '1' where id in (".join(',',$copyid ).")";
	lazy_run_sql($sql);
	$sql = "delete from app_iinvite_emails where uid = '".format_uid()."' ";
	lazy_run_sql($sql);
	die('发送邮件成功.');
}
else
{
	die('网站目前不允许注册');
}

?> 
<?php
//print_r($_FILES);
include_once( dirname( __FILE__ ) . '/function.php'   );
if( !is_login() )
{
	info_page('请登录后查看');
}
if( !$_FILES ||  $_FILES['cardfile']['size'] <= 0 )
{
	info_page('请正确选择提交的文件');
}
$content = file_get_contents($_FILES['cardfile']['tmp_name']);
preg_match_all("/[a-z0-9_\.\-]+@[a-z0-9\-]+\.[a-z]{2,6}/i", $content, $matches);
$upload_mails = array();
if(($emails = array_unique($matches[0])) !=false) 
{
	foreach( $emails as $v )
	{
		$v = trim($v);
		if( app_checkemail($v) )
		{
			$upload_mails[] = $v;
		}
	}
} 
if( !$upload_mails )
{
	info_page('没有找到邮件信息');
}
app_save_mails($upload_mails);

header('Location: /app/native/iinvite/showuser/');
?>
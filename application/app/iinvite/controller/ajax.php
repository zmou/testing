<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	die('error');
}
$type = array_shift($args);

$email = v('email').v('domain');
$psw = v('psw');

if( !$type || !$email || !$psw )
{
	die('请正确填写信息.');
}
$mails = app_get_emails( $email , $psw , $type );
//print_r($mails);
if( $mails )
{
	app_save_mails($mails);
}
else
{
	die('错误的账号,或者账号中没有联系人');	
}
?>
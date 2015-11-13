<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$type = array_shift($args);

if( $type != 'get' )
{
	$type = 'put';
}

$money = intval( v('money') );
$user = lazy_get_line( "SELECT * FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
$account = lazy_get_line( "SELECT * FROM `app_ibank_account` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );

if( $money <= 0 )
{
	info_page('错误的金额');
	exit;
}

if( $type == 'put' )
{
	if( $money < 50 )
	{
		info_page('对不起,本行不受理50以下的小额业务');
		exit;	
	}
	
	if( $money > intval( $user['g'] ) )
	{
		info_page('对不起,您身上的金额小于您要存的值');
		exit;
	}
	
	// 存款操作
	// 开户
	if( lazy_get_var( "SELECT COUNT(*) FROM `app_ibank_account` WHERE `uid` = '" . format_uid() . "'" ) < 1 )
	{
		lazy_run_sql( "INSERT INTO `app_ibank_account` ( `uid` , `g_count`  ) VALUES ( '" . format_uid() . "' , '" . intval($money*0.9) . "'  )" );
	}
	else
	{
		lazy_run_sql("UPDATE `app_ibank_account` SET `g_count` = `g_count` + " . intval( $money*0.9 ) . " WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
	}
	
	lazy_run_sql("UPDATE `app_ihome_user` SET `g` = `g` - " . intval( $money ) . " WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
	
}

if( $type == 'get' )
{
	if( $money > intval( $account['g_count'] ) )
	{
		info_page('对不起,您的存款不足');
		exit;
	}
	lazy_run_sql("UPDATE `app_ibank_account` SET `g_count` = `g_count` - " . intval( $money ) . " WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
	$check = lazy_get_var( "SELECT COUNT(*) FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "'" );
	if( $check < 1 )
	{
		lazy_run_sql("INSERT INTO `app_ihome_user`( `uid`,`g`,`gold`,`hp`,`hp_max`  )values('".format_uid()."','$money','0','0','0')" );
	}
	else
	{
		lazy_run_sql("UPDATE `app_ihome_user` SET `g` = `g` + " . intval( $money ) . " WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
	}
	
}

header('Location: /app/native/' . $GLOBALS['app'] . '/index');
info_page( '进行的操作完成了' );

?>
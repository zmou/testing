<?php
if( !is_login() )
{
	die('请登陆后操作!');
}
$action = array_shift( $args );
$number = intval(array_shift( $args ));
if( $action =='save' )
{
	$check =  lazy_get_var("SELECT g FROM `app_iduoduo_duoduo` WHERE `uid` = '" . format_uid() . "' LIMIT 1 ");
	$sql1 = "update `app_iduoduo_duoduo` set `g` = `g` - '$number' WHERE `uid` = '" . format_uid() . "' ";
	$sql2 = "update `app_ihome_user` set `g` = `g` + '$number' WHERE `uid` = '" . format_uid() . "' ";
	$js_data[] = '$("duoduo_money").innerHTML = parseInt($("duoduo_money").innerHTML) - '.$number;
	$js_data[] = '$("user_money").innerHTML = parseInt($("user_money").innerHTML) + '.$number;
	$action_name = '存入';
}
else
{
	$check =  lazy_get_var("SELECT g FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1 ");
	$sql1 = "update `app_ihome_user` set `g` = `g` - '$number' WHERE `uid` = '" . format_uid() . "' ";
	$sql2 = "update `app_iduoduo_duoduo` set `g` = `g` + '$number' WHERE `uid` = '" . format_uid() . "' ";
	$js_data[] = '$("user_money").innerHTML = parseInt($("user_money").innerHTML) - '.$number;
	$js_data[] = '$("duoduo_money").innerHTML = parseInt($("duoduo_money").innerHTML) + '.$number;
	$action_name = '取出';
}
if($check < $number )
{
	die('您没足够的银币.');
}
lazy_run_sql( $sql1 );
lazy_run_sql( $sql2 );
echo '您'.$action_name.'了'.$number.'银币';
if($js_data)
{
	$js_code = '<script>'.join(';',$js_data).';</script>';
	echo $js_code;
}
?>
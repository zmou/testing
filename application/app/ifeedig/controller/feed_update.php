<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = 'Feed修改';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$title = z( v('title') );
$feed = z(v('feed'));
$fid = intval( v('fid') );
$type = intval( v('type') );
$img = z(trim(v('img_add')));
$status = v('state');


if( !isset( $fid ) || $fid < 1 )
{
	info_page("ID错误!");
}

$num = lazy_get_var("SELECT COUNT(*) FROM `app_feed` WHERE `id` = '".intval( $fid )."'");
if( $num == '0' )
{
	info_page('没有此条Feed!');
}

//分类
$tids = app_config('titles');
if( $tids )
{
	foreach( $tids as $k => $v )
	{
		$ids[$k] = $k;
	}

	if( !in_array( $type, $ids ) )
	{
		info_page( "分类ID错误!" );
	}
}


if( $img === 'http://' )
{
	$img = null;
}



//状态
$states = $state;
if( $states )
{
	foreach( $states as $k => $v )
	{
		$statu[$k] = $k;
	}

	if( !in_array( $status, $statu ))
	{
		info_page('状态错误!');
	}
}

if( trim($title) == '' )
{
	info_page('标题不能为空');
}


$update_feed  = "UPDATE `app_feed` SET `tid` = '".intval($type)."' , `feed` = '".$feed."' , ";
$update_feed .= "`title` = ".s($title).", `state` = '".$status."', `img` = '".$img."'";
$update_feed .= "WHERE `id` = '".intval( $fid )."'";

//echo $update_feed;
lazy_run_sql( $update_feed );

info_page('修改成功!' , '/app/native/'.$GLOBALS['app'].'/feed/' , '点击返回管理!');
?> 


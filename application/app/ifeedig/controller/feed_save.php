<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$feed = trim(v('faddress'));
$title = trim(v('ftitle'));
$tid = intval(v('ftid'));
$img = z(trim(v('img_add')));
$status = v('state');
if( empty( $feed ) || empty( $title ) )
{
	info_page( "Feed标题或地址不能为空!" );
}

//type
$tids = $titles;
if( $tids )
{
	foreach( $tids as $k => $v )
	{
		$ids[$k] = $k;
	}

	if( !in_array( $tid, $ids ) )
	{
		info_page( "分类ID错误!" );
	}
}

//
if( $img === 'http://' )
{
	$img = null;
}

//状态$status
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

if( strpos( $feed , 'http://') === false )
{
	$feed = 'http://'.$feed;
}

$fnum = lazy_get_var("SELECT COUNT(*) FROM `app_feed` WHERE `feed` = ".s($feed)."");
if( $fnum != '0' )
{
	info_page( "此博客已经存在!" );
}

//
$uid = format_uid();
$insert_feed  = "INSERT INTO `app_feed` (`feed`, `tid`, `uid`, `title`, `time`, `img`, `state`) VALUES";
$insert_feed .= "(".s($feed).", '".intval($tid)."', '".intval($uid)."', ".s($title).", '".date('Y-m-d H:i:s')."', '".$img."' , '".$status."')";
lazy_run_sql( $insert_feed );
$fid = lazy_last_id();


//add feed item
$CI =&get_instance();
$CI->load->library('simplepie');
//MakeDir(ROOT.'static/data/cache');
//$CI->simplepie->set_cache_location(ROOT.'static/data/cache'); 
$CI->simplepie->set_feed_url( $feed ); 
$CI->simplepie->init();

$items = $CI->simplepie->get_items();
$values = array();
foreach( $items as $k => $v )
{
	$title = $v->get_title(); // 标题
	$desp = $v->get_content();// 内容
	$link = $v->get_link();	//连接
	$date = date('Y-m-d H:i:s' , strtotime($v->get_date()) );
	$unistring = md5($link).$date;
	$values[] = "('".intval($tid)."', '".intval($fid)."', ".s($title).", ".s($desp).", '".$date."' , '".$link."', '".$status."', ".s($unistring).", '".intval($uid)."')";

}

$sql_insert_item = "INSERT INTO `app_feed_item` (`tid`, `fid`, `title`, `desp`, `time`, `link`, `state`, `unistring`, `admin_uid`) VALUES ".join(',',$values)."";

//echo $sql_insert_item;
lazy_run_sql( $sql_insert_item );

info_page( "Feed成功保存!", "/app/native/".$GLOBALS['app']."/feed/", "返回管理" );
?> 
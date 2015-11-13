<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '悦读推荐';
if( !is_login() )
{
	info_page('请登录后查看');
}


$uid = format_uid();
$feed = trim(v('fblog'));

if( $feed == 'http://' )
{
	info_page('博客地址错误!');
}

if( strpos( $feed , 'http://' ) === false )
{
	$feed_link = 'http://'.$feeds;
}
else
{
	$feed_link = $feed;
}


$feeds = lazy_get_var("SELECT COUNT(*) FROM `app_feed_recommend` WHERE `feed` = ".s($feed_link)."");
if( $feeds > 0 )
{
	info_page("此BLOG地址已经存在!");
}

$sql  = "INSERT INTO `app_feed_recommend` (`uid`, `feed`, `timeline`) VALUES";
$sql .= "('".$uid."', ".s($feed_link).", '".date('Y-m-d H:i:s')."')";
lazy_run_sql( $sql );

info_page("提交成功,已加入验证列队!");
?> 
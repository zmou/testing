<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

$data['ci_top_title'] = '悦读';

if( !is_login() )
{
	info_page('请登录后查看');
}

$data['uid'] = format_uid();
$titleid = intval(array_shift($args));
$day = intval(array_shift($args));
$hit = array_shift($args);
$page = array_shift( $args );

if( isset($hit) && $hit == 'hit' )
{
	$hi = "ORDER BY `dig` DESC";
}
else
{	$hit = 'new';
	$hi = "ORDER BY `time` DESC";
}
$data['hit'] = $hit;

// 翻页
$page = intval($page) < 1 ?1:intval($page);
$data['page'] = $page;
$limit = 5;
$start = ($page-1)*$limit;

//分类
$titleid = !isset($titleid) || empty($titleid) || $titleid < '1' ? '1' : $titleid ;
$data['titles'] = $titles = app_config('titles');
if( $titleid )
{
	foreach( $titles as $k => $v )
	{
		if( $titleid == $k )
		{
			$where = "AND `tid` = ".$k."";
		}

		if( $titleid == '1' )
		{
			$where = '';
		}
	}
}
else
{
	$where = '';
	$titleid = '1';
}
$data['titleid'] = $titleid;

//时间分类
$data['days'] = app_config('days');
$date = date( 'Y-m-d H:i:s' );
if( $day )
{
	if( $day == '7' )
	{
		$time = "AND `time` > '".date( 'Y-m-d H:i:s' , strtotime("$date -7 day"))."'";	
	}
	elseif( $day == '365' )
	{
		$time = "AND `time` > '".date( 'Y-m-d H:i:s' , strtotime("$date -365 day"))."'";
	}
	else
	{
		$time = "AND `time` > '".date( 'Y-m-d H:i:s' , strtotime("$date -1 day"))."'";
	}
}
else
{
	$time = "AND `time` > '".date( 'Y-m-d H:i:s' , strtotime("$date -1 day"))."'";
	$day = '1';
}
$data['day'] = $day;

$data['inum'] = lazy_get_var("SELECT COUNT(*) FROM `app_feed_item` WHERE 1 AND `state` = '2'");

$item = lazy_get_data("SELECT sql_calc_found_rows * FROM `app_feed_item` WHERE 1 AND `state` = '2'  {$where} {$time} {$hi} LIMIT $start , $limit ");
$data['all'] = $all = get_count();
$snap = array();
if( file_exists( dirname( __FILE__ ) . '/snap.info.txt' ) )
{
	$snap = unserialize( file_get_contents( dirname( __FILE__ ) . '/snap.info.txt' ) );
}

foreach( $item as $ik => $iv )
{
	$snap_content = isset($snap[$iv['fid']] )?true:false;
	$item[$ik]['desp'] = format_contents( $iv['desp'] , $snap_content );	
}

$data['item'] = $item;

$times = array();
$diged = array();
if( $item )
{
	foreach( $item as $k => $v )
	{
		$times[$v['id']] = time2Units($v['time']);
		$iids[] = $v['id'];
	}
	$diged = lazy_get_vars("select `iid` from `app_feed_dig` where `uid` = '".format_uid()."' and `iid` IN(".join(',' , $iids ).") ");
}
$data['diged'] = $diged;

$data['times'] = $times;

//分页

$base = '/app/native/ifeedig/index/'.$titleid.'/'.$day.'/'.$hit;
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager( $page , $page_all , $base );


$feed = lazy_get_data("SELECT * FROM `app_feed`");
if( $feed )
{
	foreach( $feed as $k => $v )
	{
		$by[$v['id']] = $v['title'];
	}
	$data['by'] = $by;
}

$fdig = lazy_get_data("SELECT * FROM `app_feed_dig`");
if( $fdig )
{
	foreach( $fdig as $k => $v )
	{
		$udig[$v['uid']][] = $v['id'];
	}
}
layout( $data , 'default' , 'app' );
?>

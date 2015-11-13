<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '悦读文章管理';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}
$data['uid'] = format_uid();
$titleid = intval(array_shift($args));
$item_s = array_shift($args);
$page = array_shift( $args );


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

if( isset($item_s) && $item_s == 'all' )
{
	$item_state = "";
}
else
{
	$item_s = 'parts';
	$item_state = "AND `state` = '1'";
}
$data['item_s'] = $item_s;

$item = lazy_get_data("SELECT sql_calc_found_rows * FROM `app_feed_item` WHERE 1 {$item_state} {$where} ORDER BY `time` DESC LIMIT $start , $limit ");
$all = get_count();

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

$base = '/app/native/ifeedig/item/'.$titleid.'/'.$item_s;
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

//选项分类
$ts = app_config('titles');
unset( $ts['1'] );
$data['ts'] = $ts;

//验证
$data['state'] = app_config('state');

$data['num'] = lazy_get_var("SELECT COUNT(*) FROM `app_feed_item` WHERE `state` = '1'");


layout( $data , 'default' , 'app' );
?> 
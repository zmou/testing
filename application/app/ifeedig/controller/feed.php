<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '悦读管理';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

//$uid = format_uid();
//分类列表
foreach( app_config('titles') as $k => $v )
{
	$tits[$k] = $k; 
	$titles[$k] = $v;
}
$data['tits'] = $tits;
unset( $titles[1] );
$data['fselect'] = $titles;

$view = array_shift($args);
if( $view == 'add' )
{
	$data['view'] = 'add';
}

	//分类ID
$tnum = intval($view);
if( $tnum != '0' )
{
	if( !in_array( $tnum,$tits ) )
	{
		info_page('错误的分类');
	}
	$where = "AND `tid` = '".intval($tnum)."'";
	$data['tnum'] = $tnum;
}

//待处理
$waiting = lazy_get_data("SELECT * FROM `app_feed_recommend` ORDER BY `timeline` DESC LIMIT 30 ");
if( $waiting )
{
	$data['content'] = $content = $waiting;
	foreach( $content as $k => $v )
	{	
		$uids[] = $v['uid'];
		$times[$v['id']] = time2Units( $v['timeline'] );
	}
	$data['unames'] = get_name_by_uids( $uids );
	$data['waiting'] = $waiting;
	$data['times'] = $times;
}

//列表
$data['list']= lazy_get_data("SELECT * FROM `app_feed` WHERE 1 {$where} ORDER BY `time` DESC");

$files =  @file_get_contents( dirname(__FILE__).'/snap.info.txt' );

$data['snap'] = unserialize( $files );

layout( $data , 'default' , 'app' );
?>
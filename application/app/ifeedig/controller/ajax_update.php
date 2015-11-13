
<?php
//include_once( dirname( __FILE__ ) . '/function.php'   );
header("Content-Type:text/xml;charset=utf-8");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");


if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行此次操作!');
}

$id = intval(array_shift( $args ));
if( $id < 1 )
{
	echo '<a href="javascript:history.back(1)">ID不能为空!</a>';
	die();
}

foreach( app_config('titles') as $k => $v )
{
	$tits[$k] = $k; 
}

$info = lazy_get_line("SELECT * FROM `app_feed` WHERE `id` = '".intval( $id )."' LIMIT 1");
$feed = $info['feed'];
$tid = $info['tid'];

if( !empty($feed) )
{
	$CI =&get_instance();
	$CI->load->library('simplepie');
	//MakeDir(ROOT.'static/data/cache');
	//$CI->simplepie->set_cache_location(ROOT.'static/data/cache'); 
	$CI->simplepie->set_feed_url( $feed ); 
	$CI->simplepie->init();
	$items = $CI->simplepie->get_items();

	foreach( $items as $item )
	{
		$title = $item->get_title(); //
		$desp = $item->get_content();//
		$link = $item->get_link();
		$date = date('Y-m-d H:i:s' , strtotime($item->get_date()) );
		$unistring = md5( $link ).$date;
		$itid = lazy_get_var("SELECT `tid` FROM `app_feed_item` WHERE `unistring` = '".$unistring."'");
		if( $itid > 0 )
		{
			if( !in_array( $itid , $tits ) )
			{
				$itid = $tid;
			}
			//update
			$sql  = "UPDATE `app_feed_item` SET `tid` = '".intval($itid)."',";
			$sql .= " `fid` = '".intval($id)."', `title` = ".s($title).",";
			$sql .= "`desp` = ".s($desp).",`time` = ".s($date).", `link` = ".s($link)."";
			$sql .= "WHERE `unistring` = '".$unistring."' LIMIT 1 ";

			lazy_run_sql( $sql );
		}
		else
		{
			//insert
			$sql  = "INSERT INTO `app_feed_item` (`tid`, `fid`, `title`,";
			$sql .= " `desp`, `time`, `link`, `state`, `unistring`, `admin_uid`)";
			$sql .= "VALUES ('".intval($tid)."', '".intval($id)."', ".s($title).",";
			$sql .= " ".s($desp).", ".s($date)." , ".s($link).",";
			$sql .= "'".$info['state']."', ".s($unistring).", '".intval($info['uid'])."')";

			lazy_run_sql( $sql );
		}
		
		lazy_run_sql("UPDATE `app_feed` SET `time` = '".date('Y-m-d H:i:s')."' WHERE `id` = '".$id."'");
	}
}

echo '<img src="/static/images/tick.gif">';
die();
?>
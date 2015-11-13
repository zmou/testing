<?php
// check the type of url

if( !is_login() )
{
	info_page('请登录后查看');
}

include_once( dirname( __FILE__ ) . '/function.php'   );

$data = array();
$data['ci_top_title'] = '藏图箱子';
 
/*
$url = v('url');

if( empty( $url ) )
{
	info_page('链接地址不能为空');
}

$url = 'http://' . ltrim( $url , 'http://' );

//echo $url;



$ext = end( explode( '.' , $url ) ); 

if( $ext == 'mp3' )
{
	$data['type'] = MUSIC;
	$data['music_url'] = $data['link'] = $url;
}
else
{
	if( $p = get_player_by_url( $url ) )
	{
		$data['type'] = VIDEO;
		$data['link'] = $url;
		$data['video_player'] = $p;
	}
	else
	{
		$data['type'] = WEBPAGE;
		$data['link'] = $url;
	}
	
}
*/
layout( $data , 'default' , 'app' );






?>
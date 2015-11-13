<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
// clear data

$type = intval(v('type')); 
$url = z(v('url'));
$desp = n(v('desp'));

if( $type < 1 || strlen( $url ) < 1 )
{
	info_page( '分享格式不正确' );
}

$data = array();


$data['type'] = $type;
$data['link'] = $url;
$data['time'] = date("Y-m-d H:i:s");
$data['desp'] = $desp;

switch( $type )
{
	case VIDEO:
		
		$info = parse_url( $url );
		$data['video_domain'] = $info['host'];
		$cnname = '视频';
		break;
	
	case MUSIC:
		$data['music_url'] = $url;
		$cnname = '音乐';
		break;
			
	case WEBPAGE:
		$cnname = '网页';
		break;
}

$data['uid'] = format_uid();

global $CI;
$CI->load->database();
$CI->db->insert( 'app_fav' , $data );
$aid = $CI->db->insert_id();
if( $aid )
{
	$aname = 'ishare';
	$appname = get_app_name_with_aid( $aname );
	$title = '<a href="/user/space/' . format_uid() . '" target="_blank">' . _sess('u2_nickname') . '</a>'.$appname.'了一个<a href="/app/native/'.$aname.'/show/'.$aid.'/" target="_blank">'.$cnname.'</a>';
	send_to_feed( format_uid() , 'ishare' , $title , mb_substr( $data['desp'],0,20,'utf-8' )  );
}

header('Location: /app/native/' . $GLOBALS['app'] . '/index');
?>
<?php

define( 'WEBPAGE' , 1 );
define( 'MUSIC' , 2 );
define( 'VIDEO' , 3 );

function type_text( $type )
{
	switch( $type )
	{
		case VIDEO: return '视频';
		case MUSIC: return '音乐';
		default: return '网页';
	}
	
}

function get_friends_by_uid( $uid = NULL )
{
	$uid = format_uid( $uid );

	
	$where = "(`u2_uid1` = '".$uid."'  AND `is_active` = '1' )OR( `u2_uid2` = '".$uid."' AND `is_active` = '1')";
	$sql = "SELECT * FROM `u2_fans` where $where LIMIT 500 ";
	
	$fans = lazy_get_data( $sql ) ;
	
	$fid = NULL;
	
	if( isset($fans[0]) && is_array($fans[0]))
	{
		foreach( $fans as $f )
		{
			if($f['u2_uid1'] == $uid)
			{
				$fid[] = $f['u2_uid2'];
			}
			else
			{
				$fid[] = $f['u2_uid1'];
			}
		}
		if( is_array( $fid ) ) return $fid;
	}
	
	return false;	
}

function get_player_by_url( $url )
{
	$info = parse_url( $url );
	$domain = $info['host'];

	$key = rtrim(end(explode( '/' , trim( $info['path'] , '/' ))) , '.html' );

	$player = NULL;
	
	switch( $domain )
	{
		case 'v.youku.com':
			$key = preg_replace(  '/id_[a-z]{2}[0-9]{2}/is' , '' ,  $key  );
			$player .='<embed src="http://player.youku.com/player.php/sid/' . $key . '/v.swf" quality="high" width="240" height="200" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed>';
			break;

		case 'www.tudou.com':

			if( preg_match('/playindex.do\?lid=([0-9]+)/is' , $url , $out ) )
			{
				$player = '<embed src="http://www.tudou.com/player/playlist.swf?lid=' . $out[1] . '" type="application/x-shockwave-flash" width="244" height="211"></embed>';
			}
			else
			{
				$player = '<embed src="http://www.tudou.com/v/' . $key . '" type="application/x-shockwave-flash" width="240" height="200" allowFullScreen="true" wmode="transparent" allowScriptAccess="always"></embed>';
				//$player .= '<center><div style="border:1px solid #d8d8d8;padding:5px;margin:10px"><a target="_blank" href="' . get_flv_by_url( rtrim( $url , '/' ) ) . '">点击这里下载FLV文件</a></p></div><center>';
			}
			break;

		case 'v.ku6.com':
			$player = '<embed src="http://player.ku6.com/refer/' . $key . '/v.swf" type="application/x-shockwave-flash" width="240" height="200" allowFullScreen="true" wmode="transparent" allowScriptAccess="always"></embed>';
			break;

		case 'you.video.sina.com.cn':
			$keys = explode( '-' , $key );
			$player = '<embed src="http://vhead.blog.sina.com.cn/player/outer_player.swf?auto=1&vid=' . $keys[0] . '&uid=' . $keys[1] . '" type="application/x-shockwave-flash" width="240" height="200" allowFullScreen="true" wmode="transparent" allowScriptAccess="always"></embed>';
			break;

		case 'tv.mofile.com':
			$player = '<embed src="http://tv.mofile.com/cn/xplayer.swf" FlashVars="v='. $key .'&p=http://acgtv.cn/all.jpg&autoplay=1&nowSkin=0_0" width="240" height="200" allowScriptAccess="sameDomain" wmode="transparent" type="application/x-shockwave-flash"/>';
			break;

		case 'www.biku.com':
			$player = '<embed src="http://www.biku.com/opus/player.swf?VideoID=' . $key . '&embed=true&autoStart=false" type="application/x-shockwave-flash" width="225" height="184"></embed>';
			break;


		default:
			$player = false;
	}
	
	return $player;
}


?>
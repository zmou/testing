<?php

function f_desp( $content , $num , $feed_id )
{
	$content = strip_tags( $content ,'<br>' );
	$leng = mb_strlen( $content , 'UTF-8' );
	if( $leng > $num )
	{
		$content = mb_substr( $content , 0 , $num , 'UTF-8' );
		$content = $content."...(<a href='javascript:void(0)' onclick='show_desp_all(".$feed_id.");'>全文</a>)";
	}
	$result = $content;
	return $result;
}

function time2Units ($data)
{
   $past = strtotime($data);
   $now = time();
   $time = $now - $past;

   $year   = floor($time / 60 / 60 / 24 / 365);
   $time  -= $year * 60 * 60 * 24 * 365;
   $month  = floor($time / 60 / 60 / 24 / 30);
   $time  -= $month * 60 * 60 * 24 * 30;
   $week   = floor($time / 60 / 60 / 24 / 7);
   $time  -= $week * 60 * 60 * 24 * 7;
   $day    = floor($time / 60 / 60 / 24);
   $time  -= $day * 60 * 60 * 24;
   $hour   = floor($time / 60 / 60);
   $time  -= $hour * 60 * 60;
   $minute = floor($time / 60);
   $time  -= $minute * 60;
   $second = $time;
   $elapse = '';

   $unitArr = array('年前'  =>'year', '个月前'=>'month',  '周前'=>'week', '天前'=>'day',
                    '小时前'=>'hour', '分钟前'=>'minute', '秒前'=>'second'
                    );

   foreach ( $unitArr as $cn => $u )
   {
       if ( $$u > 0 )
       {
           $elapse = $$u . $cn;
           break;
       }
   }

   return $elapse;
}

function format_contents( $contents , $snap = true )
{
	$contents= strtolower($contents);
	$reg = '/<img.*?src=(.+?)\s/is';
	preg_match_all( $reg , $contents , $out );
	
	if($out[1])
	{
		foreach( $out[1] as $k => $v )
		{
			$url = trim($v,"'\"");
			if( $snap )
			{
				if(  substr( $url , 0 ,7 ) == 'http://' )
				{
					$old[$k] = $out[0][$k];
					$new[$k] = '<img src="/upload/antilink/'.$GLOBALS['app'].'/'.base64_encode($url).'" lock="400"'  ;
				}
				else
				{
					$old[$k] = $out[0][$k];
					$new[$k] = '<img src="'.$url.'" lock="400"'  ;
				}
			}
			else
			{
				$old[$k] = $out[0][$k];
				$new[$k] = '<img src="'.$url.'" lock="400"'  ;
			}
		}
		if($new)
		{
			$contents = str_replace($old,$new,$contents);
		}
	}
	return $contents;
}
?>
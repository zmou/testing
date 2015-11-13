<?php
$field_lables = app_config('field_lable');
$bind = app_config('bind');
$mid = app_config('mid');
function get_user_names( $uids ) 
{
	if( !is_array( $uids ) )
	{
		$uids = array( intval( $uids ) );
	}
	return	lazy_get_data("SELECT `id` , `u2_nickname` FROM `u2_user` WHERE `id` IN (".join(',',$uids).") " , 'id');
}

function short_content( $contents , $length = 200 , $extra = NULL  ) 
{
	$contents = strip_tags($contents);
	$contents = nl2br($contents);
	if( mb_strlen( $contents , 'utf-8' ) >  $length  )
	{
		$contents = mb_substr( $contents ,0,$length,'utf-8');
		$contents .= '...';
		$contents .= $extra;
	}
	return $contents;
}
function get_starts_by_id( $id , $mid )
{
	$data = lazy_get_data( "select `rate` , count(*) as c from `u2_rate` where  `cid` = '$id' and `mid` = '$mid' group by `rate` " , 'rate' );
	$starts[1] = isset( $data[1] )?$data[1]['c']:0;
	$starts[2] = isset( $data[2] )?$data[2]['c']:0;
	$starts[3] = isset( $data[3] )?$data[3]['c']:0;
	$starts[4] = isset( $data[4] )?$data[4]['c']:0;
	$starts[5] = isset( $data[5] )?$data[5]['c']:0;
	return $starts;
}
function get_state_html_by_id( $id )
{
	$mid = intval( app_config('mid') );
	$html = NULL;
	$votes = array();
	$state = app_config('model_state');
	$keys = array();
	if( $state )
	{
		$app = explode( '|' , $state );
		$i = 0;
		foreach( $app as $v )
		{
			$v = trim( $v );
			if( $v )
			{
				$keys[] = " `text` = '$v' ";
			}
		}
	}
	if( $keys )
	{
		$first = trim($app[0]);
		$data = lazy_get_data( "select `text` , count(*) as c from `u2_vote` where(".join( 'or' ,$keys ).") and `tid` = '$id' and `mid` = '$mid' group by `text` " );
		if( $data )
		{
			$html .= '<h5 class="w2">谁'.$first.'这'.app_config('model_name').'</h5><br/>';
			foreach( $data as $v )
			{
				if( $v['text'] == $first )
				{
					$uids =  lazy_get_vars( "select `uid` from `u2_vote` where `text` = '$first' and `tid` = '$id' and `mid` = '$mid' limit 3 " );
					$names = get_name_by_uids( $uids );
					foreach( $uids as $v )
					{
						$html .= '<div class="app_user_div"><center><a href="/user/space/'.$v.'" target="_blank"><img src="'.show_user_icon('normal',$v).'" class="icon" /><br/>'.$names[$v]['u2_nickname'].'</a></center></div>';
					}
					$html .= '<br clear="all"/>';
					if( $v['c'] > 3 )
					{
						$html .= '<a href="/app/native/'.$GLOBALS['app'].'/votepeople/'.$id.'/'.urlencode($first).'"> > 还有'. ($v['c'] - 3).'人'.$first.'</a><br/>';
					}
				}
				else
				{
					$html .= '<a href="/app/native/'.$GLOBALS['app'].'/votepeople/'.$id.'/'.urlencode($v['text']).'"> > '. $v['c'].'人'.$v['text'].'</a><br/>';
				}
			}
		}

	}
	return $html;
	
}
function get_view_uids( $mid , $cid , $limit = 5 )
{
	
	$uids = lazy_get_vars("select `uid` from `u2_app_view` where `mid` = '$mid' and `cid` = '$cid' order by `id` desc limit $limit");
	if( is_login() )
	{
		$sql = "replace into `u2_app_view` (`mid`,`cid`,`uid`,`time`)values('$mid','$cid','".format_uid()."','".date("Y-m-d H:i:s")."')";
		lazy_run_sql( $sql );
	}
	return $uids;
}
function get_state_button( $id )
{
	$mid = intval( app_config('mid') );
	$state = app_config('model_state');
	$buttons = array();
	if( $state )
	{
		$text = lazy_get_var( "select `text` from `u2_vote` where `uid` = '".format_uid()."' and `tid` = '$id' and `mid` = '$mid' limit 1 " );
		$app = explode( '|' , $state );
		foreach( $app as $v )
		{
			$v = trim( $v );
			if( $v == $text )
			{
				$buttons[] = $v;
			}
			else
			{
				$buttons[] = '<input type="button" class="button" onclick="location=\'/app/native/'.$GLOBALS['app'].'/module_vote/'.$id.'/\'+ encodeURIComponent(\''.$v.'\')"  value="'.$v.'" />';
			}
		}
	}
	if( $buttons )
	{
		return join( '&nbsp;&nbsp;&nbsp;&nbsp;' , $buttons );
	}
	else
	{
		return NULL;
	}
}
?>
<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	die('请登录后查看');
}

// 
$fid = array_shift( $args );



$user = lazy_get_line( "SELECT * FROM `u2_user` WHERE `id` = '" . format_uid() . "' LIMIT 1" );
$user['sex'] = $user['u2_sex'];

$on = lazy_get_data( "SELECT * FROM `app_ihome_cloth_on` WHERE `uid` = '" . format_uid() . "' ORDER BY `cate` ASC LIMIT 20 " );
if( is_array( $on ) )
{
	foreach( $on as $item )
	{
		$strs[$item['cate']] = $item['fid'];  
		$fids[] = $item['fid'];
	}
}

if( $fid > 0 )
{
	// 首先判断是要


	$item = lazy_get_line("SELECT * FROM `app_ishop_items` WHERE `fid` = '" . intval( $fid ) . "' LIMIT 1");

	if( $item['fid'] < 1 )
	{
		die('错误的商品id');
	}
	
	// 如果已经穿了套装 又选择了 有单件的 裙子 裤子 或者 上衣
	if( $strs['5'] && ( $item['cate'] == 4 || $item['cate'] == 3 || $item['cate'] ==2  || $item['cate'] == 10) )
	{
		lazy_run_sql( "DELETE FROM `app_ihome_cloth_on` WHERE `uid` = '" . format_uid() . "' AND `cate` = 5  " );
		unset( $strs['5'] );
	}

	// 如果已经穿了套装 又选择了 有单件的 裙子 裤子 或者 上衣 或者套装
	if( $strs['10'] && ( $item['cate'] == 4 || $item['cate'] == 3 || $item['cate'] ==2 || $item['cate'] == 5 || $item['cate'] == 1 ) )
	{
		lazy_run_sql( "DELETE FROM `app_ihome_cloth_on` WHERE `uid` = '" . format_uid() . "' AND `cate` = 10  " );
		unset( $strs['10'] );
	}
	
	
	// 裙子
	if( $item['cate'] == 4 || $item['cate'] == 3 || $item['cate'] == 5 || $item['cate'] == 10 )
	{
		lazy_run_sql( "DELETE FROM `app_ihome_cloth_on` WHERE `uid` = '" . format_uid() . "' AND `cate` = 4 OR `cate` = 3 OR `cate` = 5 OR `cate` = 10  " );
		unset( $strs['3'] );unset( $strs['4'] );unset( $strs['5'] );unset( $strs['10'] );
	}
	else
	{
		lazy_run_sql( "DELETE FROM `app_ihome_cloth_on` WHERE `uid` = '" . format_uid() . "' AND `cate` = '" . $item['cate'] . "'  " );
	}
	
	// 如果已经穿上则卸下
	if( in_array( $fid , $fids ) )
	{
		unset( 	$strs[$item['cate']] );
	}
	else
	{
		lazy_run_sql( "INSERT INTO `app_ihome_cloth_on` ( `uid` , `fid` , `cate` ) VALUES ( '" . format_uid() . "' , '" . $item['fid'] . "' , '" . intval( $item['cate'] )   . "' ) " );
		$strs[$item['cate']] = $item['fid'];
	}
}
//print_r($strs);

$screen = Array(); // 



// 背景
if( $strs['6'] > 0 ) $screen[] = $strs['6'];

// 头发_背景层
if( $strs['1'] > 0 && !$strs['10'] )
{
	$screen[] = $strs['1'] .'_b';	
}
elseif( !$strs['10'] )
{
	if( $user['sex'] == 2 )
		$screen[] = '32_b';
	else
		$screen[] = '7_b';
}

// 套装
if( $strs['5'] > 0 && !$strs['10'] )
{
	$screen[] = $strs['5'] ;	
}
else
{
	// 裙子
	if( $strs['4'] > 0 )
		$screen[] = $strs['4'] ;
	else
	{
		// 裤子
		if( $strs['3'] > 0)
			$screen[] = $strs['3'] ;
		elseif( !$strs['10'] )
		{
			if( $user['sex'] == 2 )
				$screen[] = '4' ;
			else
				$screen[] = '2' ;
		}
	}
	
	// 上衣
	if( $strs['2'] > 0 )
		$screen[] = $strs['2'] ;
	else
	{
		if( $user['sex'] == 2 )
			$screen[] = '3' ;
		else
			$screen[] = '1' ;	
	}
		
}

// 表情
		
if( $user['sex'] == 2 )
	$screen[] = '25' ;
else
	$screen[] = '14' ;


// 头发_前景
if( $strs['1'] > 0 && !$strs['10'] )
{
	$screen[] = $strs['1'] ;	
}
elseif( !$strs['10'] )
{
	if( $user['sex'] == 2 )
		$screen[] = '32';
	else
		$screen[] = '7';
}

// 饰品

if( $strs['9'] > 0 )
{
	$screen[] = $strs['9'] ;	
}

// 其他

if( $strs['8'] > 0 )
{
	$screen[] = $strs['8'] ;	
}

// 相框
if( $strs['7'] > 0 )
{
	$screen[] = $strs['7'] ;	
}


if($strs['10'] > 0)
{
	$screen[] = $strs['10'] ;
}

//print_r($screen);
?>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
	   codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="120" height="163">
	   <param name="movie" value="/static/flash/as.swf?vapart=<?php echo join( '-' , $screen ); ?>" />
	   <param name="quality" value="high" />
	   <param name="wmode" value="transparent">
	   <embed wmode="transparent" src="/static/flash/as.swf?vapart=<?php echo join( '-' , $screen ); ?>" quality="high" type="application/x-shockwave-flash"
	   pluginspage="http://www.macromedia.com/go/getflashplayer" width="120" height="163"></embed>
	   </object>
				</span>		
				</div>


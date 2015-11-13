<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '修改'.app_config('model_name');
if( !is_login() )
{
	info_page('请登录后查看');
}

$id = intval(array_shift( $args ));

$item = lazy_get_line("SELECT * FROM `app_content_{$mid}` WHERE `id` = '".intval( $id )."'");
if( !$item )
{
	info_page('错误的参数');
}
if( $item['uid'] != format_uid() )
{
	info_page('您没有权限进行此操作');
}
$data['item'] = $item;

$jsnls = array();
$sets = $item;
unset( $sets['id'] );
unset( $sets['hit'] );
unset( $sets['uid'] );
unset( $sets['comnum'] );
unset( $sets['is_active'] );
([foreach from=$field item=item ])
([if $item.type == 'checkbox' ])
$sets['field_([$item.id])'] = $sets['field_([$item.id])']?unserialize($sets['field_([$item.id])']):NULL;
([elseif $item.type == 'multi-pic' ])
$jsnls_pic[] = 'field_([$item.id])';
([elseif $item.type == 'multi-line' ])
$jsnls[] = 'field_([$item.id])';
([/if])
([/foreach])
$set = NULL;
foreach( $sets as $k => $v )
{
	if( is_array( $v ) )
	{
		foreach( $v as $value )
		{
			$set .= "set( '".$k."[]' , '".add_slashes_on_quote($value)."' );"; 
		}
	}
	elseif( in_array( $k ,$jsnls_pic  )  )
	{
		$set .= "set( '$k' , '".jsnl2br(add_slashes_on_quote($v))."' );"; 
		$set .= "show_pic_muti_preview( '$k' , '{$k}_pic' );";
	}
	elseif( in_array( $k ,$jsnls  )  )
	{
		$set .= "set( '$k' , '".jsnl2br(add_slashes_on_quote($v))."' );"; 
	}
	else
	{
		$set .= "set( '$k' , '".add_slashes_on_quote($v)."' );"; 
	}
}
$width = intval(app_config('pic_width'))?'width:'.intval(app_config('pic_width')).'px;':'90px';
$height = intval(app_config('pic_height'))?'height:'.intval(app_config('pic_height')).'px;':'120px';
$data['pic_style'] = ' style="'.$width.$height.'" ';
$data['set'] = $set;
layout( $data , 'default' , 'app' );
?>
<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
if( !level_check( app_config('add_level') ) )
{
	info_page('您没有权限进行此操作');
}

([foreach from=$field item=item ])
$data['field_([$item.id])'] = ([if $item.type != 'multi-line' && $item.type != 'multi-pic' && $item.type != 'checkbox' ])z( v('field_([$item.id])') );([elseif $item.type == 'checkbox' ])serialize(v('field_([$item.id])'));([else])x( v('field_([$item.id])') );([/if])

([/foreach])
$unique = array();
([foreach from=$field item=item ])
([if $item.is_unique ])
$unique['field_([$item.id])'] = '([if $item.label ])([$item.label])([else])默认标题([/if])';
([/if])
([/foreach])
([foreach from=$field item=item ])
([if $item.is_required ])
if( $data['field_([$item.id])'] == '' )
	info_page( '([if $item.label ])([$item.label])([else])默认标题([/if])不能为空' );
([/if])
([/foreach])
global $CI;
$CI->load->database();
if( $unique )
{
	$CI->db->select('*')->from('app_content_([$mid])')->where(0);
	foreach( $unique as $k => $v )
	{
		$CI->db->orwhere( $k , $data[$k] );
	}
	$CI->db->limit(1);
	$line = lazy_get_line();
	if( $line )
	{
		foreach( $line as $k => $v )
		{
			if( isset( $unique[$k] ) &&  $v = $data[$k]  )
			{
				info_page($unique[$k].'字段已被占用');
			}
		}
	}
}
$level = intval(app_config('nocheck_level'));
$data['is_active'] = intval(level_check($level));
$data['uid'] = format_uid();
$CI->db->insert( 'app_content_([$mid])' , $data );
$id = $CI->db->insert_id();
$title = $data[$bind['title']]?$data[$bind['title']]:'无标题';
$desc = '<a href=/app/native/'.$GLOBALS['app'].'/display/'.$id .'/ target="_blank">'.$title.'</a>';
add_to_manager( 'app_content_'.$mid , $id ,$desc , app_config('model_name') , $data['is_active'] ,'/static/icon/'.$GLOBALS['app'].'.gif' ,'is_active' );
if($data['is_active'] == 0 )
{
	info_page('添加([$mname])成功,请等待管理员审核' , '/app/native/'.$GLOBALS['app'].'/');
}
else
{
	info_page('添加([$mname])成功' , '/app/native/'.$GLOBALS['app'].'/display/'.$id );
}

?>
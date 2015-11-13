<?php
//include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$id = intval(array_shift( $args ));
$item = lazy_get_line("SELECT * FROM `app_content_([$mid])` WHERE `id` = '".intval( $id )."'");
if( !$item )
{
	info_page('错误的参数');
}
if( $item['uid'] != format_uid() )
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
if( $unique )
{
	foreach( $unique as $k => $v )
	{
		$check_keys[] = " `$k` = '{$data[$k]}' ";
	}
	$sql = "select * from `app_content_([$mid])` where ( ".join(',' , $check_keys )." ) and `id` != '$id' limit 1";
	$line = lazy_get_line($sql);
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
global $CI;
$CI->load->database();
$CI->db->where( 'id' , $id );
$CI->db->update( 'app_content_([$mid])' , $data );
info_page('修改([$mname])成功' , '/app/native/'.$GLOBALS['app'].'/display/'.$id );

?>
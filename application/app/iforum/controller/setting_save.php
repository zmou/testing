<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$fid = intval(array_shift( $args ));
$type = array_shift( $args );
$fid = $fid < 1?1:$fid;
$data['forum'] = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '$fid' and `is_active` = '1' limit 1");
if(!$data['forum'])
{
	info_page('错误的讨论区id');
}
if($type == 'basic')
{
	$name = htmlspecialchars( z(v('name')));
	$desp = v('desp');
	if( !$name || !$desp )
	{
		info_page('标题和描述不能问空');
	}
	lazy_run_sql( "update `app_iforum_status` set `name` = '$name' , `desp` = '$desp' where `id` = '$fid' limit 1");
}
elseif($type == 'cates')
{
	for($i =1 ; $i<7 ; $i++)
	{
		iforum_save_cates($fid,$i,v('cate_'.$i) );
	}
}
info_page('保存信息成功','/app/native/iforum/setting/'.$fid.'/'.$type);
header();
?>
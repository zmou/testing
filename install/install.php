<?php
define( 'ROOT' , dirname( __FILE__ ).'/../' );
function sys_info( $info )
{
	$data['info'] = $info;
	$data['title'] = $data['top_title'] = '系统消息';
	@extract( $data );
	require( ROOT. 'install/sys.tpl.html' );
	exit;
}
if( file_exists( ROOT.'install.lock' ) )
{
	sys_info('EasySNS已安装,如需重新安装请登录FTP删除根目录下的install.lock文件.<a href="http://techblog.easysns.com/?p=35" target="_blank">查看教程</a>');
}
require( ROOT. 'install/install.tpl.html' );

?>
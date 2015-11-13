<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '悦读推荐';
if( !is_login() )
{
	info_page('请登录后查看');
}


//$uid = format_uid();

layout( $data , 'default' , 'app' );
?> 
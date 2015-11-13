<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
if( !is_login() )
{
	info_page('请登录后查看');
}
$data = array();
$data['ci_top_title'] = '邀请好友';

$tab_type = 'index';

$data['user'] = NULL;
$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;

$data['emails'] = lazy_get_data("select * from app_iinvite_emails where uid = '".format_uid()."' and no_in_site = '1' ");
layout( $data , 'default' , 'app' );
?>
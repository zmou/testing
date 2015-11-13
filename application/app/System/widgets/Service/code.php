<?php
function get_system_service_data($para = NULL)
{
	//print_r( $GLOBALS['widget_id']);
	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : '在线咨询' ;
	$other = isset($para['other']) && trim(strip_tags($para['other'])) != '' ? strip_tags($para['other']) : '' ;
	$qq = isset($para['qq']) && trim(strip_tags($para['qq'])) != '' ? strip_tags($para['qq']) : '' ;
	$wangwang = isset($para['wangwang']) && trim(strip_tags($para['wangwang'])) != '' ? strip_tags($para['wangwang']) : '' ;
	
	if( (!isset( $qq ) || $qq == '' ) && (!isset( $wangwang ) || $wangwang == '') && (!isset( $other ) || $other == '') )
	{
		$data['set_please'] = '0';
	}
	
	if( isset($qq) && $qq != '' )
	{
		$data['qq'] = explode( "\n" , $qq );
	}
	
	if( isset($wangwang) && $wangwang != '' )
	{
		$data['wangwang'] = explode( "\n" , $wangwang );
	}
	
	if( isset($other) && $other != '' )
	{
		$data['other'] = explode( "\n" , $other );
	}
	
	$data['http_host'] = $_SERVER['HTTP_HOST']; 

	return $data;
}

?>		

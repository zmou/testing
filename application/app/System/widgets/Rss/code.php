<?php
function get_system_rss_data($para = NULL)
{
	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : 'Rss' ;
	$data['limit'] = isset($para['limit']) && $para['limit'] != '' ? intval($para['limit']) : '10' ;
	$data['show'] = isset($para['show']) && $para['show'] != '' ? intval($para['show']) : '1' ;
	$data['blog'] = isset($para['blog']) && strip_tags($para['blog']) != '' ? strip_tags($para['blog']) : '';
	$data['feed'] = isset($para['feed']) && strip_tags($para['feed']) != '' ? strip_tags($para['feed']) : '';
	if( !empty($data['feed']) ) 
	{
		$CI =&get_instance();
		$CI->load->library('simplepie');
		MakeDir(ROOT.'static/data/cache');
		$CI->simplepie->set_cache_location(ROOT.'static/data/cache'); 
		$CI->simplepie->set_feed_url( $data['feed'] ); 
		$CI->simplepie->init(); 

		$data['get_items'] = $CI->simplepie->get_items();
		$i=0;
		foreach ( $data['get_items'] as $line )
		{
			$i++;
			if( $i <= $data['limit'] )
			{
				$data['con'][$i]['feed_title'] = $line->get_title();
				$data['con'][$i]['feed_con'] = $line->get_content();
				$data['con'][$i]['feed_url'] = $line->get_permalink();
			}
			else
			{
				break;
			}
		}
	}
	return $data;
}

?>
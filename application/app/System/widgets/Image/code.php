<?php
function get_system_image_data($para = NULL)
{
	$para = unserialize($para);
	$data['width'] = (isset($para['width']) && intval( $para['width'] ) > 0 ) ?intval( $para['width'] ):200;
	$data['height'] = (isset($para['height']) && intval( $para['height'] ) > 0 ) ?intval( $para['height'] ):100;
	$links = array();
	$images = array();
	if( isset( $para['img_url'] ) && $para['img_url'] )
	{
		$image_para = explode('|',$para['img_url'] );
		foreach($image_para as $v)
		{
			$images[] = urlencode($v);
		}
	}
	else
	{
		$images = array('http://tbn3.google.cn/images?q=tbn:cLz3tbZyPvAOiM:','http://tbn2.google.cn/images?q=tbn:qSJmFoi5Tn8I8M:');
	}
	if( isset( $para['link_url'] ) && $para['link_url'] )
	{
		$link_para = explode('|',$para['link_url'] );
		foreach($image_para as $v)
		{
			$links[] = urlencode($v);
		}
	}

	$data['imgs'] = join('|',$images);
	$data['links'] = join('|',$links);
	$data['title'] = (isset($para['title']) && strip_tags($para['title']))?strip_tags($para['title']):'Change Image';
	return $data;
}
?>
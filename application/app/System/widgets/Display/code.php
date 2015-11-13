<?php
function get_system_display_data($para = NULL)
{
	$id = _Page('pid');

	$data = NULL;
	
	if($id)
	{
		$CI =&get_instance();

		$CI->load->model('Pro_model', 'pro', TRUE);
		
		$data['pro'] = $CI->pro->load_item( intval($id) , false );

		if(!$data['pro'])
		{
			return;
		}
		if( !is_admin() && $data['pro']['u2_is_active'] != 1  )
		{
			return;
		}
		$data['added'] = null;
		$meta_field = $CI->pro->load_meta_field($data['pro']['u2_cate']);

		if($meta_field )
		{
			foreach ($meta_field as $m)
			{
				if( isset($data['pro'][$m['u2_en_name']]) && $data['pro'][$m['u2_en_name']])
				$data['added'][] = '<p></p>'.$m['u2_cn_name'].':<br/>'.$data['pro'][$m['u2_en_name']].'</p>';
			}
		
		}
		$CI->pro->hit($id);
	}

	 return $data;
}
?>
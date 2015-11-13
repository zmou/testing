<?php

class box extends Controller {

	function box()
	{
		parent::Controller();
	}
	function index()
	{
		set_time_limit(0);
		$limit = 1000;
		
		$step = 11;


		
			$start = ($step-1)*$limit;

			$sql = "SELECT DISTINCT cid FROM `app_icase_pictures` limit $start,$limit ";

			$temp = lazy_get_data($sql);

			$this->load->database();

			if( $temp )
			{
				foreach( $temp as $v )
				{
					$data['id'] = $v['cid'];
					$this->db->insert( 'temp' , $data );
				}
			}
			$step++ ;
			echo $step;
		die();
		
	}
	
}
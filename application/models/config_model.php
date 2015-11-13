<?php
class Config_model extends Model 
{

    function Config_model()
    {
        parent::Model();
    }

	function add( $keywords , $modules , $sid )
	{
		// 检查是否存在

		$data = array();
		$data['sid'] = $sid;
		$data['keywords'] = $keywords;
		$data['modules'] = $modules;
		$data['created'] = date("Y-m-d H:i:s");


		$this->db->select('count(*) as c')->from('s_conf')->where('sid' , $sid );
		
		if( lazy_get_var() > 0 )
		{
			$this->db->where( 'sid' , $sid );
			return $this->db->update('s_conf', $data);
		}
		else
		{
			return $this->db->insert('s_conf', $data);
		}
	}
}

?>
<?php
class Riki_model extends Model 
{

    function Riki_model()
    {
        parent::Model();
    }

	function load_page($id)
	{
		if($id == NULL)
		{
			$this->db->where('u2_tag','首页');
		}
		else
		{
			$this->db->where('id',intval($id) );
		}
		$this->db->select('*')->from('u2_page')->orderby('id','DESC')->limit(1);
		
		return  lazy_get_line();
	}
	function add_page($page_info)
	{
		$this->db->select('COUNT(*)')->from('u2_page')->where('u2_tag',$page_info['page_title']);

		$data['u2_link'] = NULL;
		if( lazy_get_var() )
		{
			return false;
		}

		$data = $this->get_page_data($page_info);

		$this->db->select('MAX(u2_order)')->from('u2_page');
		
		$data['u2_order'] =  lazy_get_var() + 1 ;
		$data['u2_is_system'] = '0';
		$data['u2_in_tab'] = '1';
		$this->db->insert('u2_page',$data);

		return $this->db->insert_id();
	
	}
	function modify_page($page_info ,$id )
	{
	
		$page_old = $this->load_page($id);

		if(!$page_old)
		{
			return false;
		}
		$u2_data = unserialize($page_old['u2_data'] );

		$o_widgets = $u2_data['widgets'];

		$data = $this->get_page_data($page_info);

		$data['u2_data'] = unserialize($data['u2_data']);

		$layout = $page_info['page_layout'];

		foreach( $o_widgets as $k => $v)
		{
			if( ($k+1) > $layout[0] )
			{
				if( !isset( $o_widgets[( intval($layout[0]) - 1 )] ) )
				{
					$o_widgets[( intval($layout[0]) - 1 )] = array();
				}
				$o_widgets[( intval($layout[0]) - 1 )] = array_merge($o_widgets[( intval($layout[0]) - 1 )] , $v);
				unset ($o_widgets[$k]);
			}
		}

		$data['u2_data']['widgets'] = $o_widgets;

		$data['u2_data'] = serialize($data['u2_data']);
		
		$this->db->where('id',$id);
		
		$this->db->update('u2_page',$data);

	}
	function get_page_data($page_info )
	{
		if($page_info['page_type'] == 2)
		{
			$data['u2_data']['cateid'] = $page_info['cateid'];
		}
		if($page_info['page_type'] == 3)
		{
			$this->db->select('*')->from('u2_content')->where('id', intval($page_info['pro_id']) )->limit('1');

			$pro = lazy_get_line();

			if( !$pro )
			{
				$data['u2_data']['cateid'] = 0;
				$data['u2_data']['pro_id'] = intval($page_info['pro_id']);
			}
			else
			{
				$data['u2_data']['cateid'] = $pro['u2_cate'];
				$data['u2_data']['pro_id'] = intval($page_info['pro_id']);
			}
		}
		$data['u2_link'] = NULL;
		if($page_info['page_type'] == 4)
		{
			if( strpos(  $page_info['page_link'] , 'http://' ) === false )
				$page_info['page_link'] = 'http://'.$page_info['page_link'];
				
			if(isset( $page_info['new_window'] ) )
				$u2_link['new_window'] = $page_info['new_window'];

			$u2_link['link'] = $page_info['page_link'];

			$data['u2_link'] = serialize($u2_link);
		}
		$data['u2_data']['type'] = $page_info['page_type'];
		$data['u2_tag'] = $page_info['page_title'];
		$data['u2_data']['layout'] = $page_info['page_layout'];
		$data['u2_data']['widgets'] = array();
		$data['u2_data'] = serialize($data['u2_data']);

		return $data;
	
	}
	function del_page($id)
	{
		$page = $this->load_page($id);
		if($page)
		{
			if($page['u2_is_system'])
			{
				return;
			}
			$u2_data = unserialize($page['u2_data'] );
			if($u2_data['widgets'])
			{
				foreach($u2_data['widgets'] as $row)
				{
					foreach($row as $iid)
					{
						$this->db->where('id',$iid);
						$this->db->delete('u2_widget_instance');
					}
				}
			}
			$this->db->where('id',$id)->where('u2_is_system','0');
			$this->db->delete('u2_page');
			return true;
		}
		return false;
	}

}
?>
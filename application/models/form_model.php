<?php
class Form_model extends Model 
{

    function Form_model()
    {
        parent::Model();
    }

	function get_form_info_by_id( $fid )
	{
		$this->db->select('*')->from('w2_form')->where( 'id' , $fid )->limit(1);
		return lazy_get_line();
	}
	function del_form_by_id( $id )
	{
		$this->db->where('id' , $id )->limit(1);
		$this->db->delete( 'w2_form' );
	}
	function get_forms( $start , $limit )
	{
		$this->db->select('sql_calc_found_rows *')->from('w2_form')->orderby('id', 'DESC')->limit($limit ,  $start  );
		return lazy_get_data();
	}

	function save()
	{
		$data = array();
		$data['name'] = z(v('name'));
		$data['title'] = z(v('title'));
		$data['subtitle'] = z(v('subtitle'));
		$data['state'] = z(v('state'));
		$data['uid'] = format_uid();
		$data['timeline'] = date("Y-m-d H:i:s");
		
		$this->db->insert( 'w2_form' , $data );
		$fid = $this->db->insert_id();
		
		// add title to
		
		header('Location: /design/form/' . $fid );
		info_page( '<a href="/design/form/' . $fid . '">下一步</a>' );
		//echo 'ooo';
	}
	
	function update()
	{
		$fid = intval(v('fid'));
		
		if( $fid < 1 ) info_page( '错误的form参数' );
		$uid = format_uid();
		$data = array();
		$data['name'] = z(v('name'));
		$data['title'] = z(v('title'));
		$data['subtitle'] = z(v('subtitle'));
		$data['item_name'] = z(v('item_name'));
		$data['is_main_app'] = z(v('is_main_app'));

		$this->db->where( 'id' , $fid );
		$this->db->where( 'uid' , $uid );

		$this->db->update( 'w2_form' , $data );
		
		header( 'Location: /design/build/' . $fid );
	}
	function modify( $id )
	{
		$data = array();
		$data['title'] = z(v('title'));
		$data['subtitle'] = z(v('subtitle'));
		$data['state'] = z(v('state'));

		$this->db->where('id' ,  $id )->limit(1);
		$this->db->update( 'w2_form' , $data );	
	}
}

?>
<?php
class Item_model extends Model 
{

    function Item_model()
    {
        parent::Model();
    }

	function add( $fid , $type = 'line' )
	{
		$data = array();
		$data['fid'] = intval( $fid );
		$data['uid'] = format_uid();
		$data['type'] = $type;
		$data['timeline'] = date("Y-m-d H:i:s");
		
				
		$this->db->insert( 'w2_item' , $data );
		
		return $this->db->insert_id();
	}
	
	function update()
	{
		$iid = intval(v('iid'));
		if( $iid < 1 ) return false;
		
		$data = array();
		
		$data['label'] = z(v('label'));
		$data['type'] = z(v('type'));
		$data['size'] = z(v('size'));
		$data['is_required'] = intval(v('is_required'));
		$data['is_unique'] = intval(v('is_unique'));
		$data['is_searchable'] = z(v('is_searchable'));
		$data['view_level'] = z(v('view_level'));
		
		$type_values = v('type_values');
		$new_values = array();
		if(  $type_values && is_array( $type_values ) )
		{
			$i = 1;
			foreach( $type_values['name']  as $key => $value )
			{
				if( $value || $type_values['value'][$key] )
				{
					$new_values['name'][$i] = $value;
					$new_values['value'][$i] = $type_values['value'][$key];
					$i++;
				}
			}
		}
		$data['type_values'] = serialize( $new_values );
		
		$is_adv = intval( v('is_adv') );
		
		if( $is_adv > 0  ) 
		{
			$data['default_value'] = z(v('default_value'));
			$data['instruction'] = z(v('instruction'));
			$data['custom_css'] = z(v('custom_css'));
		}
		
		$this->db->where( 'id' , $iid );
		
		$this->db->update( 'w2_item' , $data );
		
		echo 'done';
		
	}
	
	function get_item_info_by_id( $iid )
	{
		$this->db->select('*')->from('w2_item')->where('id' , $iid)->limit( 1 );
		return lazy_get_line();
	}
	
	function get_items_by_fid( $fid )
	{
		$this->db->select('*')->from('w2_item')->where('fid' , $fid)->orderby('display_order' , 'DESC')->orderby('id' , 'ASC')->limit( 100 );
		return lazy_get_data();
	}
	
	function remove( $iid )
	{
		$this->db->where( 'id' , $iid )->limit(1);
		$this->db->delete( 'w2_item' );
	}
	
	function update_order( $id , $order )
	{
		$this->db->where( 'id' , $id )->limit(1);
		$this->db->update( 'w2_item' , array( 'display_order'  => $order ) );
	}
	function del_item_by_fid( $id )
	{
		$this->db->where( 'fid' , $id );
		$this->db->delete('w2_item');
	}
	
}

?>
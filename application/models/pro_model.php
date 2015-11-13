<?php
class Pro_model extends Model 
{
    function Pro_model()
    {
		parent::Model();
	}
	function load_meta_field($cid)
	{
		$this->db->select('*')->from('u2_meta_field')->where('u2_cate_id',$cid)->orwhere('u2_cate_id', '0');

		return lazy_get_data();
	}
	function save( $info ,$extra = NULL)
	{
		if(is_array ($extra) )
			$data = $extra;

		$data['u2_title'] = $info['u2_title'];	
		$data['u2_desp'] = $info['u2_desp'];
		$data['u2_uid'] = format_uid();
		$data['u2_nickname'] = _sess('u2_nickname');
		$data['u2_addtime'] = date("Y-m-d H:i:s");
		$data['u2_hit'] = 0;
		$data['u2_pic'] = $info['u2_pic'];
		if( check_active() )
		{
			$data['u2_is_active'] = 1;
		}
		else
		{
			$data['u2_is_active'] = 0;
		}
		
		$data['u2_cate'] = $info['cate'];

		$this->db->insert('u2_content', $data);	
		$id = $this->db->insert_id();
		$type = _text('system_pro');
		$desc = '<a href="/riki/display/'.$id.'" target="_blank">'.$info['u2_title'].'</a>';
		add_to_manager('u2_content' ,$id ,$desc ,$type , $data['u2_is_active'] );
		
	}
	function plist($cid ,$start,$limit)
	{
		if( $cid > 0 )
			$this->db->select('*')->from('u2_content')->where('u2_cate',$cid)->where('u2_is_active',1)->orderby('id','DESC')->limit($limit,$start);
		else
			$this->db->select('*')->from('u2_content')->where('u2_is_active',1)->orderby('id','DESC')->limit($limit,$start);
		
		return lazy_get_data();
	}
	function load_item($id , $actived = 1)
	{
		if( $actived )
		{
			$this->db->where('u2_is_active',1);
		}
		$this->db->select('*')->from('u2_content')->where('id',$id)->limit(1);

		return lazy_get_line();
	}
	function hit($id)
	{
		$sql = " UPDATE u2_content SET u2_hit = u2_hit + 1  WHERE id = $id ";
		$this->db->query($sql);
	}
	function update( $id , $info ,$extra )
	{	
		$all = is_array ($extra) ? array_merge( $info, $extra ) :$info ; 
		foreach( $all as $k => $v )
		{
			if( $v == NULL )
				continue;
			$data[$k] = $v;
		}
		$this->db->where('id' , $id);
		$this->db->update('u2_content',$data);
	}
	function del( $id )
	{
		$this->db->where('id' , $id);
		$this->db->delete('u2_content');
		$this->db->where('u2_tid' , $id)->where('u2_table' , 'u2_content');
		$this->db->delete('u2_manager');
	}

}
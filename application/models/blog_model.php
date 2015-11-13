<?php
class Blog_model extends Model 
{

    function Blog_model()
    {
        parent::Model();
    }

	function add( $title , $content , $link , $keyword ,  $sid )
	{
		$data = array();
		$data['sid'] = $sid;
		$data['title'] = $title;
		$data['content'] = $content;
		$data['link'] = $link;
		$data['keyword'] = $keyword;
		$data['created'] = date("Y-m-d H:i:s");

		return $this->db->insert('s_blog', $data);
	}

	function clean( $sid )
	{
		$this->db->where( 'sid' , $sid );
		$this->db->delete('s_blog');
	}
}

?>
<?php

class extra extends Controller {

	function extra()
	{
		parent::Controller();

	}
	function index()
	{
		check_login();
		if( strpos( _sess('u2_email') , '@' ) !== false )
		{
			info_page('你使用是正确的email');
		}
		$data['ci_top_title'] = '修改email';
		
		$data['page_name'] = 'extra';

		layout($data);
	}
	function save()
	{
		check_login();
		$mail = z( v('email') );
		if( strpos( _sess('u2_email') , '@' ) !== false )
		{
			info_page('你使用是正确的email');
		}
		if( strpos( $mail , '@' ) === false )
		{
			info_page('请填写正确的email');
		}
		$sql = "select count(*) from u2_user where LCASE(u2_email) = '".strtolower($mail)."' ";
		if( lazy_get_var($sql) )
		{
			info_page('该email已经有人使用了,请重新填写');
		}
		$this->load->database();
		$data['u2_email'] = $mail;
		$uid = format_uid();
		$this->db->where('id' , $uid );
		$this->db->update( 'u2_user' , $data );
		$this->load->library('session');
		$this->session->set_userdata('u2_email' ,$mail );
		info_page('补充E-mail信息成功,以后请用email登陆.','/','返回首页');

	}
	
}
?>
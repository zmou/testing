<?php

class invite extends Controller {

	function invite()
	{
		parent::Controller();
		$this->load->model('Invite_model', 'invite', TRUE);
	}
	function index()
	{
		header('Location: myinvite/');
	}
	function myinvite($page = NULL )
	{
		check_login();

		$page = intval($page) < 1 ?1:intval($page);
		$limit = $this->config->item('per_page');
		$start = ($page-1)*$limit;
		$page_all = ceil( $this->invite->get_user_invite_num() /$limit);
		$base = '/invite/myinvite';
		$data['list'] =  $this->invite->get_user_invite($start,$limit);
		
		$data['pager'] = get_pager(  $page , $page_all , $base );

		$this->view('myinvite',$data);
	}
	function buy()
	{
		$data = NULL;
		$data['limit'] = intval( $this->invite->get_invite_limit() );
		$data['price'] = '&nbsp;'.intval( c('invite_price') ).'&nbsp;'.( c('invite_use_gold') ?  _text('system_gold_money') : _text('system_silver_money')) ;
		$this->view('buy',$data);
	}
	function view($page,$data)
	{
		$data['ci_top_title'] = _text('invite_'.$page.'_title');
		
		$data['page_name'] = $page;

		layout($data);
	}
	function add()
	{
		$number = intval(v('number'));
		if( $number < 1 )
		{
			info_page( _text('system_input_right_no') );
		}
		if( intval( $this->invite->get_invite_limit() ) && ( intval( $this->invite->get_invite_limit() ) < $number) )
		{
			info_page( _text('invite_buy_over_limit') );
		}
		if( $this->invite->buy( $number ))
		{
			info_page( _text('invite_buy_success'),'/invite/myinvite' );
		}
		else
		{
			info_page( _text('invite_buy_money_limit') );
		}
	}

}
?>
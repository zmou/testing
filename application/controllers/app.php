<?php

class app extends Controller {

	function app()
	{
		parent::Controller();
	}
	
	function index()
	{
		//
		echo 'index';
	}
	
	/*
	 * Native interface for applications
	 */
	function native()
	{
		// 
		$args = func_get_args();
		
		$folder = array_shift( $args );

		$GLOBALS['app'] = $folder;

		$action = array_shift( $args );
		
		if( $action == '' ) $action = 'index';
		$GLOBALS['action'] = $action;
		
		$code_file = APPPATH.'app/'.basename( $folder ) . '/controller/' .  basename( $action ). '.php';
		
		if( file_exists( $code_file ) )
		{
			//check_app( $folder );
			@include_once( $code_file );
		}
		else
		{
			info_page( '您访问的应用尚不存在,请联系管理员' );
		}
		
		//print_r( $args );
		//if()
	}
}
<?php

class js extends Controller {

	function js()
	{
		parent::Controller();
	}
	function index($name)
	{
		$this->js_header();
		if( file_exists( $f = ROOT . 'static/scripts/' . $name ) )
		{
			require( $f );
		}
	}
	function fck( $name )
	{
		$this->js_header();
		if( file_exists( $f = ROOT . 'static/fck/' . $name ) )
		{
			require( $f );
		}
	}
	private function js_header()
	{
		//header("Expires: ".gmdate("D, d M Y H:i:s", time()+315360000)." GMT");
		//header("Cache-Control: max-age=315360000");	
	}
}
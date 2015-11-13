<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Builder 
{
    function Builder()
    {
		//
		$this->CI = &get_instance();
		$this->CI->load->model('Form_model', 'form', TRUE);	
		$this->CI->load->model('Item_model', 'item', TRUE);
    }

	function init( $fid )
	{
		$this->form = $this->CI->form->get_form_info_by_id( $fid );
		$this->items = $this->CI->item->get_items_by_fid( $fid );
	}
	
	
	function make( $code_path = NULL , $view_path = NULL )
	{
		define( 'META_TEMPLATE' , APPPATH . 'meta' );
		define( 'META_CTEMPLATE' ,   APPPATH . 'meta/smarty_template' );
		define( 'META_CACHE' ,   APPPATH . 'meta/smarty_cache' );
		
		
		if( $code_path == NULL ) $code_path = APPPATH . 'meta/code/';
		if( $view_path == NULL ) $view_path = APPPATH . 'meta/view/';
		
		// create main app folder
		define( 'MAIN_APP' , APPPATH . 'app/main/' );
		define( 'MAIN_APP_CODE' , MAIN_APP . 'controller/' );
		
		define( 'MAIN_APP_VIEW' , MAIN_APP . 'views/' );
		define( 'MAIN_APP_WIGETS' , MAIN_APP . 'widgets/' );
		
		define( 'META_LAYOUT' , 'default' );
		
		
		
		@mkdir( MAIN_APP );
		@mkdir( MAIN_APP_CODE );
		@mkdir( MAIN_APP_VIEW );
		@mkdir( MAIN_APP_VIEW . 'default' ) ;
		@mkdir( MAIN_APP_VIEW . 'default/main' );
		@mkdir( MAIN_APP_VIEW . 'default/side' );
		@mkdir( MAIN_APP_WIGETS );
		
		
		
		// load the parse to deal with meta php code
		$this->CI->load->library('Smarty');
		
		
		
		// copy some files
		copy( $code_path . 'function.php' ,  MAIN_APP_CODE . 'function.php' );
		copy( $view_path . 'core.js' ,  MAIN_APP_VIEW . 'function.php' );
		copy( $view_path . 'style.css' ,  MAIN_APP_VIEW . 'style.css' );
		
		// TODO: what about the database?
		
		
		// copy config file
		$this->CI->smarty->assign( 'desc' , $this->form['subtitle'] );
		file_put_contents( MAIN_APP_CODE . 'config.php' , $this->CI->smarty->fetch( 'code/config.php' )  );
		
		$this->CI->smarty->assign( 'form' , $this->form );
		
		// 
		$this->CI->load->library('Uitem');
		
		$this->CI->smarty->assign( 'items' , $this->CI->uitem->decode( $this->items ) );
		
		
		$actiones = array( 'add' );
		
		foreach( $actiones as $action )
		{
			$this->make_action( $code_path , $view_path , $action );
			echo '<p>' . $action . ' page finished!</p>';
		}
		
		
	}
	
	function make_action( $code_path , $view_path , $action )
	{
		file_put_contents( MAIN_APP_CODE . $action . '.php' , $this->CI->smarty->fetch( 'code/' . $action . '.php' )  );
		
		file_put_contents( MAIN_APP_VIEW . META_LAYOUT . '/main/' . $action . '.tpl.html' ,  $this->CI->smarty->fetch( 'view/main/' . $action . '.tpl.html' ) );
		if( file_exists( $view_path . 'side/' . $action . '.tpl.html' ) )
		{
			file_put_contents( MAIN_APP_VIEW . META_LAYOUT . '/side/' . $action . '.tpl.html' ,  $this->CI->smarty->fetch( 'view/side/' . $action . '.tpl.html' ) );
		}
	}
	
}

?>
<?php

class riki extends Controller {

	function riki()
	{
		parent::Controller();
		$this->load->model('Riki_model', 'riki', TRUE);
	}
	function display($id)
	{
		gobal_page_item('pid',$id);
		$this->index(2);
	}
	function modify($id)
	{
		gobal_page_item('pid',$id);
		$this->index(4);
	}
	function plist($cid)
	{
		gobal_page_item('cid',$cid);
		$this->index(3);
	}
	function index($id = NULL )
	{
		//time start
		if($id == NULL)
			$id = 1 ;

		$time_start = microtime_float();

		//init infomation

		//load and check ip infomation 

		//load page information
		
		$page_information = $this->riki->load_page($id);

		//load hook
		
		//run hook pre

		//load module

		$this->load_module($page_information);

		//run hook finish

		//time finish

		$time_end = microtime_float();
		$time = $time_end - $time_start;

		echo "<!-- runding $time seconds\n -->";
		
	}
	function load_module($page_information)
	{
		//check user style
		$style = get_style();

		//set ajax load widget by page information para
		if( isset($page_information['u2_data']) )
		{
			$page_para = unserialize($page_information['u2_data']);
		}
		if( isset( $page_para ) && $page_para != '' )
		{
			//print_r($page_para);
			global_page_info( $page_para );
			$data['pid'] = $page_information['id'];
			$data['tag'] = $page_information['u2_tag'];
			$data['widgets'] = $page_para['widgets'];
			$data['is_system'] = $page_information['u2_is_system'];
			$data['page_location_info'] =serialize($page_para['widgets']);
			$data['ci_top_title'] = $data['tag'];

			$data['num_widgets'] = is_admin() ? count( $data['widgets'] ,COUNT_RECURSIVE) -count($data['widgets']): 999;
			//load module by page information and style
		}
		else
		{
			$data = array();
		}
		layout( $data ,'riki');	
		
	}
	function page_action($id)
	{
		$this-> check_admin();
		
		if (v('page_action') == 'add' )
		{
			$id = $this->riki->add_page($_POST);
			if( !$id )
			{	
				info_page( _text('riki_add_error_title_same') );
				die();
			}
			
		}
		elseif (v('page_action') == 'modify' )
		{
			$this->riki->modify_page($_POST,$id );
		}
		if( v('page_link') )
		header('Location: /');
		else
		header('Location: /riki/index/'.$id );
		return;
	
	}
	function check_admin()
	{
		if( !is_admin() )
		{
			info_page( _text('system_limit_rights') );
			die();
		}
	}
	function delpage($id)
	{
		$this-> check_admin();
		
		if(intval($id) == 1)
		{
			info_page( _text('riki_del_page_index_error')  );
			die();
		}
		if( $this->riki->del_page(intval($id)))
		{
			header('Location: /');
		}
		else
		{
			info_page( _text('system_error_id')  );
			die();
		}
	
	}
	
}
?>
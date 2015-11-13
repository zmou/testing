<?php

class pro extends Controller {

	function pro()
	{
		parent::Controller();
		$this->load->model('Pro_model', 'pro', TRUE);
	}
	function index()
	{
		header('Location: /pro/add/');
	}
	function add( )
	{
		check_admin();
		//$this->login_check();
		$data['cates'] = load_cates();
		$this->view('add',$data);
	}
	function display($id = NULL)
	{
		
		$data['pro'] = $this->pro->load_item( intval($id) );

		if(!$data['pro'])
		{
			info_page( _text('system_error_id') );
			die();
		}
		$meta_field = $this->pro->load_meta_field($data['pro']['u2_cate']);

		if($meta_field )
		{
			foreach ($meta_field as $m)
			{
				if( isset($data['pro'][$m['u2_en_name']]) && $data['pro'][$m['u2_en_name']])
				$data['added'][] = '<p></p>'.$m['u2_cn_name'].':<br/>'.$data['pro'][$m['u2_en_name']].'</p>';
			}
		
		}
		$this->pro->hit($id);
		$this->view('display',$data);
	}
	function plist($cid = 1 , $page = NULL )
	{
		$page = intval($page) < 1 ?1:intval($page);
		$limit = $this->config->item('per_page');
		$start = ($page-1)*$limit;

		$data['pros'] = $this->pro->plist( intval( $cid ) ,$start,$limit  );

		$this->view('list',$data);
	}
	function save()
	{
		check_admin();
		//$this->check_admin();
		//$this->login_check();
		$cid = v('cate');
		if(!$cid)
		{
			info_page( _text('pro_add_no_cate') );
			die();
		}
		if(!($info['u2_title'] = v('u2_title')) || !($info['u2_desp'] = v('u2_desp')) || !($info['cate'] = v('cate'))  )
		{
			info_page( _text('pro_add_not_null') );
			die();
		}
		if( isset( $_FILES['picfile']['size'] ) && ($_FILES['picfile']['size'] > 0) )
		{
			make_pro_icon_dir();
			$time = time();
			$source_image = ROOT . get_pro_icon_path().$time.'.gif';
			move_uploaded_file( $_FILES['picfile']['tmp_name'] , $source_image  );
			$info['u2_pic'] = '/'.get_pro_icon_path().$time.'.gif';
		}
		else
		{
			$info['u2_pic'] = NULL;
		}
		$extra = $this->extra_save($cid);

		$this->pro->save($info , $extra);

		info_page( _text('pro_add_success') ,'/riki/plist/'.$cid );
	}
	function extra_save($cid)
	{
		check_admin();

		$extra = NULL;

		$meta_field = $this->pro->load_meta_field($cid);

		if($meta_field )
		{
			foreach ($meta_field as $m)
			{
				if( $m['u2_not_null'] && !$_POST[$m['u2_en_name']] )
				{
					info_page( _text('pro_add_extra_not_null',$m['u2_cn_name']) );
					die();
				}
				elseif( isset($_POST[$m['u2_en_name']]) && $_POST[$m['u2_en_name']]  )
				{
					$extra[$m['u2_en_name']] = $_POST[$m['u2_en_name']];
				}
				
			}
			return $extra;
		}

	}
	function view($page,$data)
	{
		if( _text('pro_'.$page.'_title') )
		{
			$data['ci_top_title'] = _text('pro_'.$page.'_title');
		}
		$data['page_name'] = 'pro_'.$page;

		layout($data);
	}
	function modify($id = NULL )
	{
		check_admin();
	
		$data = NULL;
		
		if($id)
		{
		
			$pro = $this->pro->load_item( intval($id) );

			if(! $pro )
			{
				info_page(_text('system_error_id'));
	
			}
			if( ( $pro['u2_uid'] != _sess('u2_uid') ) && !is_admin() )
			{
		
				info_page(_text('system_limit_rights')) ;

			}
			$data['pro'] = $pro;
			$data['added'] = null;
			$data['cates'] = load_cates();
			$meta_field = $this->pro->load_meta_field($data['pro']['u2_cate']);

			if($meta_field )
			{

				foreach ($meta_field as $m)
				{
					$temp['key'] = $m['u2_en_name'];
					$temp['value'] = $data['pro'][$m['u2_en_name']];
					$temp['name'] = $m['u2_cn_name'];
					$data['added'][] = $temp;
				}
			}
		
		}
		else
		{
			info_page ( _text('system_error_id') );
		}

		$this->view('modify',$data);
	}
	function update()
	{
		check_admin();
		$id = intval(v('id'));
		$pro = $this->pro->load_item( $id );

		if(!$pro )
		{
			info_page( _text('system_error_id') );
			die();
		}
		if(!($info['u2_title'] = v('u2_title')) || !($info['u2_desp'] = v('u2_desp')) || !($info['u2_cate'] = v('cate'))  )
		{
			info_page( _text('pro_add_not_null') );
			die();
		}
		if( isset( $_FILES['picfile']['size'] ) && ($_FILES['picfile']['size'] > 0) )
		{
			make_pro_icon_dir();
			$time = time();
			$source_image = ROOT . get_pro_icon_path().$time.'.gif';
			move_uploaded_file( $_FILES['picfile']['tmp_name'] , $source_image  );
			$info['u2_pic'] = '/'.get_pro_icon_path().$time.'.gif';
			if( $pro['u2_pic'] )
			{
				@unlink( $pro['u2_pic'] );
			}
		}
		else
		{
			$info['u2_pic'] = NULL;
		}
		$extra = $this->extra_save( $info['u2_cate'] );
		$this->pro->update($id , $info , $extra);
		info_page( _text('pro_update_success') ,'/riki/plist/'.$info['u2_cate'] );
	}
	function del($id = NULL )
	{
		check_admin();
		$pro = $this->pro->load_item( $id );

		if(!$pro )
		{
			info_page( _text('system_error_id') );
			die();
		}
		$this->pro->del($id);
		if( $pro['u2_pic'] )
		{
			@unlink( $pro['u2_pic'] );
		}
		info_page( _text('pro_del_success') ,'/riki/plist/'.$pro['u2_cate'] );
	}

}
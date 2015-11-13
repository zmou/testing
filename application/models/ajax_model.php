<?php
class Ajax_model extends Model 
{

    function Ajax_model()
    {
        parent::Model();
    }
	function get_apps()
	{
		$this->db->select('*')->from('u2_app')->where(' `u2_has_widgets` > 0 ')->orderby('id','asc');

		return lazy_get_data();
	}
	function get_widget_by_aid($aid)
	{
		$this->db->select('*')->from('u2_widget')->where('u2_aid',$aid);

		return lazy_get_data();
	}
	function load_widget_by_id($id)
	{
		$this->db->select('*')->from('u2_widget')->where('id',$id)->limit('1');

		return lazy_get_line();
	}
	function load_page($tag)
	{
		$this->db->select('*')->from('u2_page')->where('u2_tag',$tag)->orderby('id','DESC')->limit(1);
		
		return  lazy_get_line();
	}
	function insert_wedget_by_id($id)
	{
		$widget = $this->load_widget_by_id( intval($id) );
		if($widget)
		{
			$data['u2_wid'] = $id;
			$data['u2_aid'] = $widget['u2_aid'];
			$data['u2_folder'] = $widget['u2_folder'];
			$data['u2_data'] = '';
	
			$this->db->insert('u2_widget_instance',$data);
		
			$newid = $this->db->insert_id();

			return $newid;
		}
	}
	function add_widget_to_page($tag ,$id)
	{
		$widget = $this->load_widget_by_id( intval($id) );
		$page =  $this->load_page($tag);
		if($page)
		{
			$newid = $this->insert_wedget_by_id($id);
		
			if(!$newid)
			{
				return;
			}
			$u2_data = unserialize($page['u2_data']);

			if( isset($u2_data['widgets'][0]) && $u2_data['widgets'][0] )
				array_unshift( $u2_data['widgets'][0] , $newid );
			else
				$u2_data['widgets'][0][] = $newid;
			
			$this->update_page_data( $u2_data ,$tag);

			return $newid;
		}
		return false;
	}
	function update_page_data( $u2_data ,$tag)
	{
		$data['u2_data'] = serialize($u2_data);
			
		$this->db->where('u2_tag',$tag);

		$this->db->update('u2_page',$data);
	}
	function load_page_data($tag)
	{
		$this->db->select('u2_data')->from('u2_page')->where('u2_tag',$tag)->orderby('id','DESC')->limit(1);

		return unserialize( lazy_get_var() );
	}
	function delete_instance_by_id($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('u2_widget_instance');
	}
	function load_cates()
	{
		$this->db->select('*')->from('u2_cate')->orderby('u2_cate_num');
		return lazy_get_data() ;
	}	
	function load_widget_instance($id)
	{
		$this->db->select('*')->from('u2_widget_instance')->where('id',$id);

		return  lazy_get_line();
	}
	function save_widget_extra($info,$id)
	{
		$data['u2_data'] = serialize($info);
		$this->db->where('id',$id);
		$this->db->update('u2_widget_instance',$data);
	}
	function save_page_order($orders)
	{
		if( is_array($orders) )
		{
			foreach($orders as $u2_order => $id)
			{
				$this->db->where('id',$id);
				$data['u2_order'] = $u2_order;
				$this->db->update('u2_page',$data);
			}
		}
	}
	function del_data_extra($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('u2_meta_field');
	}
	function update_page_link($id ,$link)
	{
		$this->db->select('*')->from('u2_page')->where('id',$id)->limit('1');

		$page = lazy_get_line();

		if($page)
		{
			$u2_link = unserialize($page['u2_link']);
			if( strpos($link,'http://') === false )
			{
				$link = 'http://'.$link;
			}
			$u2_link['link'] = $link;
			$data['u2_link'] = serialize($u2_link);
			$this->db->where('id',$id);
			$this->db->update('u2_page',$data);		
		}

	}
	function initialize_page($id)
	{
		$this->db->select('*')->from('u2_page')->where('id',$id)->limit(1);
		$page = lazy_get_line();
		if($page)
		{
			$u2_data = unserialize($page['u2_data']);
			if($u2_data['widgets'])
			{
				foreach($u2_data['widgets'] as $v)
				{
					if($v)
					{
						foreach($v as $iid)
						{
							$this->delete_instance_by_id($iid);
						}
					}
				}
			}
			if($id == 2)
			{
				$u2_data['type'] = 3;
				$u2_data['widgets'] = array();
				$newid=$this->insert_system_wedget_by_folder('Display');
				$u2_data['widgets'][1][] = $newid;
			}	
			elseif($id == 3)
			{
				$u2_data['type'] = 2;
				$u2_data['widgets'] = array();
				$newid=$this->insert_system_wedget_by_folder('ProList');
				$u2_data['widgets'][1][] = $newid;
			}
			$u2_data['layout'] = '2';
			$data['u2_data'] = serialize($u2_data);
			$this->db->where('id',$id)->where('u2_is_system','1');
			$this->db->update('u2_page',$data);
		}
	}
	function insert_system_wedget_by_folder(  $folder  )
	{
		$u2_folder = strtolower('System/widgets/'.$folder );
		$widget = lazy_get_line("select * from `u2_widget` where `u2_aid` = 'system' and LCASE(`u2_folder`) = '$u2_folder' ");
		if($widget)
		{
			$data['u2_wid'] = $widget['id'];
			$data['u2_aid'] = $widget['u2_aid'];
			$data['u2_folder'] = $widget['u2_folder'];
			$data['u2_data'] = '';
	
			$this->db->insert('u2_widget_instance',$data);
		
			$newid = $this->db->insert_id();

			return $newid;
		}
	}
	function chang_page_display($pid , $display )
	{
		$data['u2_in_tab'] = intval(!$display);
		$this->db->where('id',$pid);
		$this->db->update('u2_page',$data);
	}
	function save_app_order($orders)
	{
		if( is_array($orders) )
		{
			foreach($orders as $u2_order => $id)
			{
				$this->db->where('id',$id);
				$data['u2_order'] = $u2_order;
				$this->db->update('u2_app',$data);
			}
		}
	}
	function res_save( $aname , $aid , $title , $desp , $uid = NULL )
	{
		if( $aname && $aid && $desp )
		{
			$data['u2_uid'] = format_uid( $uid );
			$data['u2_app_name'] = $aname;
			$data['u2_app_id'] = intval($aid);
			$data['u2_title'] = htmlspecialchars( $title );
			$data['u2_desp'] = htmlspecialchars( $desp );
			$data['u2_time'] = date("Y-m-d H:i:s");

			$this->db->insert('u2_restore', $data);

			return $this->db->insert_id();
		}
	}
	function get_res_with_aids( $aname , $array , $key = NULL )
	{
		$data = NULL;
		if( $key )
		{
			foreach( $array as $v )
			{
				$aids[] = $v[$key];
			}
		}
		else
		{
			$aids = is_array($array)?$array:array( $array );
		}
		if( $aids )
		{
			$this->db->select('*')->from('u2_restore')->where('u2_app_name',$aname)->where(' u2_app_id IN ('.join(',',$aids).')' )->orderby('id','desc');
			$res = lazy_get_data();
		}
		if( $res )
		{
			foreach( $res as $v )
			{
				$uids[$v['u2_uid']] = $v['u2_uid'];
			}
			
			$this->db->select('id,u2_nickname')->from('u2_user')->where(' id IN ('.join(',',$uids).')' );
			$names = lazy_get_data();
			if( $names )
			{
				foreach( $names as $v )
				{
					$nick[$v['id']] = $v['u2_nickname'];
				}
				foreach( $res as $v )
				{
					$v['nickname'] = $nick[$v['u2_uid']];
					$data[$v['u2_app_id']][] = $v;
				}
			}
		}
		if(is_array($array) )
		{
			return $data;
		}
		else
		{
			return isset($data[$array])?$data[$array]:NULL;
		}
	}
	function get_ticket_by_id($id)
	{
		$this->db->select('*')->from('app_iticket_items')->where('id', $id )->limit('1');
		return lazy_get_line();
	}
	function update_ticket_by_id($id,$data)
	{
		$this->db->where('id', $id );
		$this->db->update('app_iticket_items', $data );
	}
	function get_unique_pics( $limit  , $page )
	{
		$start = ($page-1)*$limit;
		$this->db->select('sql_calc_found_rows *')->from('app_icase_pictures_unique')->orderby('id', 'desc')->limit($limit, $start );
		return lazy_get_data();
	}
	function get_unique_pic_url( $id )
	{
		$this->db->select('url')->from('app_icase_pictures_unique')->where('id',$id)->limit('1');
		return lazy_get_var();
	}
	function contents_manage($ids,$action)
	{
		$this->db->select('*')->from('u2_manager')->where("`id` IN(".join(',',$ids).") ");

		$manage_data = lazy_get_data();

		if(!$manage_data)
		{
			return false;
		}
		$do_id = lazy_get_var("SELECT MAX(`u2_doid`) FROM `u2_manager`") + 1;

		$active = $action=='accept'?1:0;

		foreach($manage_data as $v)
		{
			$this->db->where('id',$v['u2_tid']);

			$data[$v['u2_key']] = $active ;

			$this->db->update( $v['u2_table'] , $data );
			
			unset($data);

		}
		$sql = "update `u2_manager` set `u2_done` = concat(`u2_done`,';',`u2_doid`,',',`u2_state`)  where `id` IN(".join(',',$ids).") and `u2_done` IS NOT NULL ";

		lazy_run_sql( $sql );

		$sql = "update `u2_manager` set  `u2_done` =concat(`u2_doid`,',',`u2_state`) where `id` IN(".join(',',$ids).") and `u2_done` IS NULL ";

		lazy_run_sql( $sql );
		
		$sql = "update `u2_manager` set  `u2_doid` = '$do_id' , `u2_state` = '$action' where `id` IN(".join(',',$ids).")";
		
		lazy_run_sql( $sql );
	}
	function u_do_manager()
	{
		$do_id = lazy_get_var("SELECT MAX(`u2_doid`) FROM `u2_manager`");
		if( $do_id )
		{
			$this->db->select('*')->from('u2_manager')->where('u2_doid' , $do_id );

			$manage_data = lazy_get_data();

			foreach( $manage_data as $v )
			{
				$done = explode( ';' , $v['u2_done'] );
				$last = array_pop( $done );
				$queue = explode( ',' , $last );
				$last_action = $queue[1];
				$last_doid = intval( $queue[0] );
				$active = $last_action =='accept'?1:0;

				$this->db->where('id',$v['u2_tid']);
				$data[$v['u2_key']] = $active ;
				$this->db->update( $v['u2_table'] , $data );
				
				$manage_data = array();
				if( $done )
				{
					$manage_data['u2_done'] = join(';', $done );
					$manage_data['u2_doid'] = $last_doid;
				}
				else
				{
					$manage_data['u2_done'] = NULL;
					$manage_data['u2_doid'] = 0;
				}
				$manage_data['u2_state'] = $last_action;

				$this->db->where('id',$v['id']);
				$this->db->update( 'u2_manager' , $manage_data );

			}

		}
	}
	function update_widgets( $old , $new )
	{
		$old = array_change_key_case( $old );
		$new = array_change_key_case( $new );
		$count = count($old);
		$update = 0 ;
		foreach( $old as $k => $v )
		{
			if( isset( $new[$k] ) )
			{
				if( $v['u2_name'] != $new[$k]['u2_name'] || $v['u2_name'] != $new[$k]['u2_name'] )
				{
					$update++;
					$this->db->where('id',$v['id']);
					$this->db->update( 'u2_widget' , $new[$k] );
				}
				unset( $old[$k] );
				unset( $new[$k] );
			}
		}
		if( $old )
		{
			foreach( $old as $v )
			{
				$count--;
				$update++;
				$this->db->where('id',$v['id'])->limit(1);
				$this->db->delete( 'u2_widget');
			}
		}
		if( $new )
		{
			foreach( $new as $v )
			{
				$count++;
				$update++;
				$this->db->insert('u2_widget' , $v );
			}
		}
		$data = array();
		$data['count'] = $count;
		$data['update'] = $update;
		return $data;
	}
}
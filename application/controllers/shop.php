<?php

class shop extends Controller {

	function shop()
	{
		parent::Controller();
		$this->load->model('Shop_model', 'shop', TRUE);
	}
	function index()
	{
	
	}
	function cate( $id = NULL , $page = NULL )
	{
		$id = intval( $id );
		$cates = get_shop_cates();
		$parent = array();
		if( $cates )
		{
			foreach( $cates as $v )
			{
				$parent[$v['pid']][$v['id']] =  $v['orders'];
			}
		}
		if( !isset( $cates[$id] ) )
		{
			info_page('错误的分类');
		}
		$nav[] = $cates[$id]['cate_desc'];
		$nid = $id;
		while( $cates[$nid]['pid'] > 0 )
		{
			$nid = $cates[$nid]['pid'];
			$nav_link = '<a href="/shop/cate/'.$nid.'">'.$cates[$nid]['cate_desc'].'</a>';
			array_unshift($nav,  $nav_link );
		}
		$cids = get_child_cids( $id , $parent );
		$data['cates'] = $cates;
		$data['nav'] = $nav;
		$page = intval( $page ) > 0?intval( $page ):1;
		$limit = 10;
		$start = ($page-1)*$limit;
		$data['list'] = $this->shop->get_item_by_cids( $cids , $start , $limit );
		$all = get_count();
		$page_all = ceil( $all/$limit );
		$data['pager'] = get_pager( $page , $page_all , '/shop/cate/'.$id  );

		$keeps = array();
		$wishes = array();
		if( $data['list'] )
		{
			foreach( $data['list'] as $v )
			{
				$sids[$v['id']] = $v['id'];
			}
			$keeps = $this->shop->get_wishlist_with_ids( $sids , 1 );
			$wishes = $this->shop->get_wishlist_with_ids( $sids , 2 );
			
		}
		$data['keeps'] = $keeps;
		$data['wishes'] = $wishes;

		$data['brands'] = $this->shop->get_brands();
		$data['views'] = $this->get_viewed_item();
		$this->view('list', $data );
	}
	function brand( $id = NULL , $page = NULL )
	{
		$id = intval( $id );
		$data['brands'] = $this->shop->get_brands();
		if( !isset( $data['brands'][$id] ) )
		{
			info_page('错误的品牌');
		}
		$data['nav'] = array( $data['brands'][$id]['name'] );
		$page = intval( $page ) > 0?intval( $page ):1;
		$limit = 10;
		$start = ($page-1)*$limit;
		$data['list'] = $this->shop->get_item_by_brand( $id , $start , $limit );
		$all = get_count();
		$page_all = ceil( $all/$limit );

		$keeps = array();
		$wishes = array();
		if( $data['list'] )
		{
			foreach( $data['list'] as $v )
			{
				$sids[$v['id']] = $v['id'];
			}
			$keeps = $this->shop->get_wishlist_with_ids( $sids , 1 );
			$wishes = $this->shop->get_wishlist_with_ids( $sids , 2 );
			
		}
		$data['keeps'] = $keeps;
		$data['wishes'] = $wishes;

		$data['pager'] = get_pager( $page , $page_all , '/shop/brand/'.$id  );
		$data['cates'] = get_shop_cates();
		$data['views'] = $this->get_viewed_item();
		$this->view('list', $data );
	}
	function item( $id = NULL )
	{
		$id = intval( $id );
		$data['item'] = $this->shop->get_item( $id );
		if( !$data['item']  )
		{
			info_page('错误的商品ID');
		}
		$this->save_viewed_item( $id );
		$cates = get_shop_cates();
		$nav[] = $data['item']['name'];
		$nid = $data['item']['cate'];
		while( $nid > 0 )
		{
			$nav_link = '<a href="/shop/cate/'.$nid.'">'.$cates[$nid]['cate_desc'].'</a>';
			array_unshift($nav,  $nav_link );
			$nid = $cates[$nid]['pid'];
		}
		$data['nav'] = $nav;
		$extra_info = array();
		if( $data['item']['type'] >  0 )
		{
			$extra_info = $this->shop->get_extra_info( $data['item']['type'] );
		}
		$data['extra_info'] = $extra_info ;
		$data['cates'] = $cates;

		$sids = array();
		$sids[] = $id ;
		$keeps = $this->shop->get_wishlist_with_ids( $sids , 1 );
		$wishes = $this->shop->get_wishlist_with_ids( $sids , 2 );
		$data['keeps'] = $keeps;
		$data['wishes'] = $wishes;

		$data['brands'] = $this->shop->get_brands();
		$data['replys'] = $this->shop->get_replys();
		if( $data['replys'] )
		{
			foreach( $data['replys'] as $v )
			{
				$uids[$v['uid']] = $v['uid'];
				if( $v['ruid'] > 0 )
					$uids[$v['ruid']] = $v['ruid'];
			}
			$data['names'] = get_name_by_uids( $uids );
		}
		$data['views'] = $this->get_viewed_item();
	
		$data['shopcate'] = $this->shop->get_shopcate_folder();
		$this->view('item', $data );
	}
	function replysave( $id = NULL )
	{
		check_login();
		$id = intval( $id );
		$line = $this->shop->get_item( $id );
		if( !$line  )
		{
			info_page('错误的商品ID');
		}
		$info = trim(strip_tags( v('info') ));
		if( $info == '' )
		{
			info_page('请填写回复内容');
		}
		$this->shop->save_reply( $info , $line );
		header( 'Location: /shop/item/'.$id.'/#reply_title' );
	}
	function sencode()
	{
		$posts = NULL;
		if( $_POST )
		{
			$post = array();
			foreach( $_POST as $k => $v )
			{
				if( $v != NULL && $v != '0' )
				{
					$post[$k] = $v;
				}
			}
			if( $post )
			{
				$posts = base64_encode( serialize( $post ) );
			}	
		}
		header('Location: /shop/search/1/'.$posts );
	}
	function search( $page = NULL , $posts = NULL )
	{
		$keys = NULL;
		if( $posts )
		{
			$keys = @unserialize(base64_decode( $posts ));
			if( !is_array( $keys ) )
				info_page('错误的参数');
		}
		$keys['searchtxt'] = isset( $keys['searchtxt'] )&& $keys['searchtxt']?z( $keys['searchtxt'] ):NULL;
		$cates = get_shop_cates();
		if( isset( $keys['cate'] ) && intval( $keys['cate'] ) > 0 && isset( $cates[intval($keys['cate'])] ) )
		{
			if( $cates )
			{
				foreach( $cates as $v )
				{
					$parent[$v['pid']][$v['id']] =  $v['orders'];
				}
				$cids = get_child_cids( intval( $keys['cate'] ) , $parent );
				$keys['cates'] = $cids;
			}
			else
			{
				$keys['cates'] = NULL;
			}
		}
		else
		{
			$keys['cates'] = NULL;
		}
		$page = intval( $page ) > 0?intval( $page ):1;
		$limit = 10;
		$start = ($page-1)*$limit;
		$data['list'] = $this->shop->get_search_items( $start , $limit , $keys );
		$all = get_count();
		$page_all = ceil( $all/$limit );

		$keeps = array();
		$wishes = array();
		if( $data['list'] )
		{
			foreach( $data['list'] as $v )
			{
				$sids[$v['id']] = $v['id'];
			}
			$keeps = $this->shop->get_wishlist_with_ids( $sids , 1 );
			$wishes = $this->shop->get_wishlist_with_ids( $sids , 2 );
			
		}
		$data['keeps'] = $keeps;
		$data['wishes'] = $wishes;

		$data['pager'] = get_pager( $page , $page_all , '/shop/search' ,  $posts );
		$data['nav'] = array( '搜索结果列表' );
		$data['cates'] = $cates;
		$data['brands'] = $this->shop->get_brands();
		unset( $keys['cates'] );
		$set = NULL;
		if( $keys )
		{
			foreach( $keys as $k => $v )
			{
				$set .= "set( '".$k."' , '".add_slashes_on_quote($v)."' );"; 
			}
		}
		$data['set'] = $set;
		$data['views'] = $this->get_viewed_item();
		$this->view('search', $data );
		
	}
	private function get_viewed_item()
	{
		$shopinfo = _sess('shopinfo');
		$viewed = isset( $shopinfo['viewed'] )?$shopinfo['viewed']:NULL;
		$views = array();
		if( $viewed && is_array( $viewed ) )
		{
			$views = $this->shop->get_viewed_items( $viewed );
		}
		return $views;
	}
	private function save_viewed_item( $id )
	{
		$id = intval( $id );
		if( $id > 0 )
		{
			$shopinfo = _sess('shopinfo');
			$viewed = isset( $shopinfo['viewed'] )?$shopinfo['viewed']:array();
			if( !in_array( $id , $viewed ) )
			{
				while( count( $viewed )  >= 5)
				{
					array_shift($viewed);
				}
				$viewed[] = $id;
			}
			$shopinfo['viewed'] = $viewed;
			$data['shopinfo'] = $shopinfo;
			set_sess($data );
		}
		
	}
	private function view( $page , $data )
	{
		$data['ci_top_title'] = _text('shop_'.$page.'_title');
		
		$data['page_name'] = $page;

		layout($data);
	}
	
}
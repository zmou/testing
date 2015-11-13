<?php
class Shop_model extends Model 
{

    function Shop_model()
    {
        parent::Model();
    }
	function get_cates()
	{
		$cates = array();
		return lazy_get_data("select * from `u2_shop_cate` " , 'id');
	}
	function get_item_by_cids( $cids , $start = 0 ,$limit = 10 )
	{
		$this->db->select("sql_calc_found_rows *")->from('u2_shop_items')->where( "`cate` IN (".join(',',$cids).")" )->where('is_active', 1)->limit($limit,$start)->orderby('id','desc');
	
		return lazy_get_data();
	}
	function get_brands()
	{
		return  lazy_get_data("select * from `u2_shop_brands` order by `orders` ASC " , 'id');
	}
	function get_item_by_brand( $bid , $start = 0 ,$limit = 10 )
	{
		$this->db->select("sql_calc_found_rows *")->from('u2_shop_items')->where( 'brands' , $bid )->where('is_active', 1)->limit($limit,$start)->orderby('id','desc');
		return lazy_get_data();
	}
	function get_item( $id )
	{
		$this->db->select("*")->from('u2_shop_items')->where( 'id' , $id )->where('is_active', 1)->limit(1);
		$line = lazy_get_line();
		if( !$line )
		{
			return $line;
		}
		if( $line['type'] > 0 )
		{
			$extra = array();
			$check = lazy_get_var( "SHOW TABLES LIKE 'shop_extra_".$line['type']."' " );
			if( $check )
			{
				$extra = lazy_get_line("select * from `shop_extra_".$line['type']."` where `cid` = '{$line['id']}' limit 1 ");
			}
			$line['extra'] = $extra;
		}
		return $line;
	}
	function get_extra_info( $id )
	{
		$extra = array();
		$this->db->select('extra')->from('u2_shop_type')->where( 'id' , $id )->limit(1);
		$var = lazy_get_var();
		if( $var )
		{
			$var = unserialize( $var );
			$extra = $var['field'];
		}
		return $extra;		
	}
	function save_reply( $info , $line )
	{
		$data['cid'] = $line['id'];
		$data['uid'] = format_uid();
		//$data['type'] = $line['type'];
		$data['info'] = $info;
		$data['time'] = date("Y-m-d H:i:s");

		return $this->db->insert('u2_shop_replys',$data);
	}
	function get_replys( $limit = 10 )
	{
		$this->db->select('*')->from('u2_shop_replys')->orderby('id','desc')->limit( $limit );
		return lazy_get_data();
	}
	function get_search_items( $start , $limit , $keys )
	{
		$wheres = array();
		$extra = NULL;
		$is_pro = isset( $keys['is_pro'])&&intval($keys['is_pro'])== 1?1:0;
		if( $keys )
		{
			$array = array( 'brands' , 'new' );
			foreach( $array as $v )
			{
				if( isset( $keys[$v] ) && intval($keys[$v]) > 0 )
				{
					$wheres[] = " `$v` = '".intval($keys[$v])."' ";
				}
			}
			if( isset( $keys['cates'] ) && $keys['cates'] && is_array( $keys['cates'] ) )
			{
				$wheres[] = " `cate` IN (".join(',',$keys['cates'] ).")";
			}
			if( isset( $keys['searchtxt'] ) && $keys['searchtxt'] )
			{
				$name = trim(strip_tags( $keys['searchtxt'] ));
				if( $name )
					$wheres[] = " `name` Like '%".addslashes($name)."%' ";
			}
			if( $is_pro )
			{
				$wheres[] = " `is_pro` = '1' ";
				if( isset( $keys['price_start'] ) && intval($keys['price_start']) > 0 )
				{
					$wheres[] = " `pro_price` > '".intval($keys['price_start'])."' ";
				}
				if( isset( $keys['price_end'] ) && intval($keys['price_end']) > 0 )
				{
					$wheres[] = " `pro_price` < '".intval($keys['price_end'])."' ";
				}
			}
			else
			{
				if( isset( $keys['price_start'] ) && intval($keys['price_start']) > 0 )
				{
						$a[] = " `pro_price` > '".intval($keys['price_start'])."' ";
						$b[] = " `price` > '".intval($keys['price_start'])."' ";
				}
				if( isset( $keys['price_end'] ) && intval($keys['price_end']) > 0 )
				{
						$a[] = " `pro_price` < '".intval($keys['price_end'])."' ";
						$b[] = " `price` < '".intval($keys['price_end'])."' ";
				}
				if( isset($a) && $a )
				{
					$a[] = " `is_pro` = '1' ";
					$b[] = " `is_pro` = '0' ";
					$extra = " and( (".join(' and ', $a ).")OR(".join(' and ', $b ).") )";
				}
			}
		
			
		}
		$where = NULL;
		if( $wheres || $extra  )
		{
			$where = join( ' and ' , $wheres );
			$where = $where == NULL?' 1 '.$extra:$where.$extra;
			$this->db->where( $where );
		}
		$this->db->select("sql_calc_found_rows *")->from('u2_shop_items')->where('is_active', 1)->limit($limit,$start)->orderby('id','desc');

		return lazy_get_data();
	}
	function get_viewed_items( $ids )
	{
		$this->db->select("*")->from('u2_shop_items')->where( "`id` IN (".join(',',$ids).")" )->where('is_active', 1)->limit(5)->orderby('id','desc');
	
		return lazy_get_data();
	}
	function get_wishlist_with_ids( $sids , $type )
	{
		$data = array();
		if( !is_login() || !$sids || !is_array($sids) )
		{
			return $data;
		}
		$uid = format_uid();
		$type = intval( $type ) == 2 ?2:1;
		$data = lazy_get_vars("select `cid` from `u2_shop_wishlist` where `type` = '$type' and `uid` = '$uid' and `cid` IN(".join(',',$sids).") ");
		return $data;
	}
	function get_shopcate_folder() 
	{
		$this->db->select('u2_folder')->from('u2_app')->where('aid' , 'ishopcart')->limit(1);
		return lazy_get_var();
	}
}

?>
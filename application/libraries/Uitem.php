<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Uitem 
{
    function Uitem()
    {
		//
	}
	
	function decode( $items )
	{
		$this->result = array();
		
		foreach( $items as $item )
		{
			$this->result[] = $this->parse( $item );
		}
		
		return join( '' , $this->result ) . '<script>var tip0 = new Tips($$(".tips"));</script>';
	}
	
	function parse( $item )
	{
		$item['type_values'] = unserialize( $item['type_values'] );
		
		$type = $item['type'];
		if( $type == '' ) $type = 'line';
		
		$the_method = 'parse_' . str_replace( '-' , '_' , $type ) ;
		if( method_exists( $this , $the_method ) )
		{
			return  $this->$the_method( $item );
		}
	}
	
	function parse_line( $item )
	{
		if( !empty( $item['instruction'] ) ) 
			$instruction = '&nbsp;<img src="/static/images/comment_left.gif" title="Help::' . $item['instruction'] . '" class="tips" />';
		else 
			$instruction = '';
			
		if( !empty( $item['custom_css'] ) ) 
			$css = ' style="' . $item['custom_css'] . '"';
		else 
			$css = '';	
			
		if( !empty( $item['size'] ) ) 
			$size = ' class="item_size_' . $item['size'] . '"';
		else 
			$size = '';	
	
		
		return '<div class="uline">' 
		. '<strong>' . $item['label'] . '</strong>' 
		. $instruction 
		. '<br/><input name="field_' . $item['id'] . '" value="'. $item['default_value'] . '" ' . $css . $size . ' />'
		. '</div>';
	}
	
	function parse_multi_line( $item )
	{
		if( !empty( $item['instruction'] ) ) 
			$instruction = '&nbsp;<img src="/static/images/comment_left.gif" title="Help::' . $item['instruction'] . '" class="tips" />';
		else 
			$instruction = '';
			
		if( !empty( $item['custom_css'] ) ) 
			$css = ' style="' . $item['custom_css'] . '"';
		else 
			$css = '';	
			
		if( !empty( $item['size'] ) ) 
			$size = ' class="item_size_' . $item['size'] . '"';
		else 
			$size = '';	
	
		
		return '<div class="uline">' 
		. '<strong>' . $item['label'] . '</strong>' 
		. $instruction 
		. '<br/><textarea name="field_' . $item['id'] . '" ' . $css . $size . ' >' . htmlspecialchars($item['default_value']) . '</textarea>'
		. '</div>';
	}
	
	function parse_checkbox( $item )
	{
		if( !empty( $item['instruction'] ) ) 
			$instruction = '&nbsp;<img src="/static/images/comment_left.gif" title="Help::' . $item['instruction'] . '" class="tips" />';
		else 
			$instruction = '';

		if( !empty( $item['custom_css'] ) ) 
			$css = ' style="' . $item['custom_css'] . '"';
		else 
			$css = '';	

		if( !empty( $item['size'] ) ) 
			$size = ' class="item_size_' . $item['size'] . '"';
		else 
			$size = '';	
			
		$checkboxes = array();
		if( is_array( $item['type_values']['name'] ) )
		{
			foreach( $item['type_values']['name']  as $key => $value )
			{
				if( empty( $value ) ) continue;
				$checkboxes[] = '<input type="checkbox" name="field_' . $item['id'] . '" value="' . $item['type_values']['value'][$key] . '" ' . $css . '  />&nbsp;<label>'. $item['type_values']['name'][$key] .'</label>';
			}
			
			$checkbox = join( '&nbsp;' ,  $checkboxes );
		}
		
		


		return '<div class="uline">' 
		. '<strong>' . $item['label'] . '</strong>' 
		. $instruction 
		. '<br/>' . $checkbox
		. '</div>';
	}
	
	function parse_radio( $item )
	{
		if( !empty( $item['instruction'] ) ) 
			$instruction = '&nbsp;<img src="/static/images/comment_left.gif" title="Help::' . $item['instruction'] . '" class="tips" />';
		else 
			$instruction = '';

		if( !empty( $item['custom_css'] ) ) 
			$css = ' style="' . $item['custom_css'] . '"';
		else 
			$css = '';	

		if( !empty( $item['size'] ) ) 
			$size = ' class="item_size_' . $item['size'] . '"';
		else 
			$size = '';	
			
		$radios = array();
		if( is_array( $item['type_values']['name'] ) )
		{
			foreach( $item['type_values']['name']  as $key => $value )
			{
				if( empty( $value ) ) continue;
				$radios[] = '<input type="radio" name="field_' . $item['id'] . '" value="' . $item['type_values']['value'][$key] . '" ' . $css . '  />&nbsp;<label>'. $item['type_values']['name'][$key] .'</label>';
			}
			
			$radio = join( '&nbsp;' ,  $radios );
		}
		
		


		return '<div class="uline">' 
		. '<strong>' . $item['label'] . '</strong>' 
		. $instruction 
		. '<br/>' . $radio
		. '</div>';
	}
	
	function parse_dropdown( $item )
	{
		if( !empty( $item['instruction'] ) ) 
			$instruction = '&nbsp;<img src="/static/images/comment_left.gif" title="Help::' . $item['instruction'] . '" class="tips" />';
		else 
			$instruction = '';

		if( !empty( $item['custom_css'] ) ) 
			$css = ' style="' . $item['custom_css'] . '"';
		else 
			$css = '';	

		if( !empty( $item['size'] ) ) 
			$size = ' class="item_size_' . $item['size'] . '"';
		else 
			$size = '';	
			
		$options = array();
		if( is_array( $item['type_values']['name'] ) )
		{
			foreach( $item['type_values']['name']  as $key => $value )
			{
				if( empty( $value ) ) continue;
				$options[] = '<option  name="field_' . $item['id'] . '" value="' . $item['type_values']['value'][$key] . '" ' . ' >'. $item['type_values']['name'][$key] .'</option>';
			}
			
			$select =  '<select name="field_' . $item['id'] . '" ' . $css . $size . ' >' . join( '' ,  $options ) . '</select>';
		}
		
		


		return '<div class="uline">' 
		. '<strong>' . $item['label'] . '</strong>' 
		. $instruction 
		. '<br/>' . $select
		. '</div>';
		
	}
	
}	
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Service
{
	function Service()
	{
		$this->url_base = 'http://alpha.easysns.com/service/';
		$this->user = c('user');
		$this->password = md5(c('password'));
		$this->exp_date = c('exp_date');
		$this->secret_key = c('secret_key');
		$this->args = array();
	}
	function add( $key , $value )
	{
		$this->args[$key] = $value;
	}
	function send( $action )
	{
		$url = $this->url_base . $action;

		if( is_array($this->args ) )
		{
			foreach( $this->args as $key=>$value )
			{
				if( trim($key) != '' && trim($value) != '' )
				{
					$formvars[trim($key)] = trim($value);
				}
			}
		}
		$formvars['user'] = $this->user;
		$formvars['password'] = $this->password;
		$formvars['exp_date'] = $this->exp_date;
		$formvars['secret_key'] = $this->secret_key;
		$this->CI = &get_instance();
		$this->CI->load->library('snoopy');
		if( !$this->CI->snoopy->submit($url, $formvars) )
		{
			$this->result = false;
		}
		else
		{
			$this->result = $this->CI->snoopy->results;
		}
	}
	function result( $action )
	{
		$this->send( $action );
		if( $this->result )
		{
			return @unserialize( $this->result );
		}
		return $this->result;
	}

}
?>
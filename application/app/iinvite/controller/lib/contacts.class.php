<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

class Contacts
{

	public function Contacts( )
	{
		if ( !$this->_checkLicense( ) )
		{
			exit( "error" );
		}
	}

	public function checkLogin( $user, $password )
	{
		return 1;
	}

	public function getContacts( $user, $password, &$result )
	{
		return 1;
	}

	public function readCookies( $file, &$result )
	{
		$fp = fopen( $file, "r" );
		while ( !feof( $fp ) )
		{
			$buffer = fgets( $fp, 4096 );
			$tmp = split( "\t", $buffer );
			$result[trim( $tmp[5] )] = trim( $tmp[6] );
		}
		return 1;
	}

	public function _checkLicense( )
	{
		return true;
		/*
		$domains = explode( "|", CNT_ALLOW_DOMAIN );
		$pattern = array( );
		foreach ( $domains as $domain )
		{
			$pattern[] = preg_quote( $domain );
		}
		return preg_match( "/".implode( "|", $pattern )."\$/", $_SERVER['HTTP_HOST'] );
		*/
	}

}

define( "CNT_ALLOW_DOMAIN", "localhost|yue360.com" );
define( "MSINA", 0 );
define( "MTOM", 1 );
define( "MGOOGLE", 2 );
define( "M163", 3 );
define( "M126", 4 );
define( "MSOHU", 5 );
define( "MMSN", 6 );
define( "MYAHOO", 7 );
define( "MSOHU_VIP", 8 );
define( "USERAGENT", $_SERVER['HTTP_USER_AGENT'] );
define( "COOKIEJAR", tempnam( ini_get( "upload_tmp_dir" ), "cookie" ) );
define( "TIMEOUT", 10 );
?>

<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

include_once( "contacts.class.php" );
class ContactsYahoo extends Contacts
{

	public function checkLogin( $user, $password )
	{
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_URL, "http://mail.cn.yahoo.com" );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		$pattern = "/name=[\"|\\']*.challenge.[\"|\\']*\\s+value=[\"|\\']+(.*)[\"|\\']+>/";
		if ( !preg_match_all( $pattern, $contents, $result, PREG_PATTERN_ORDER ) )
		{
			return 0;
		}
		$challenge = trim( $result[1][0] );
		$request = "http://edit.bjs.yahoo.com/config/login";
		$postargs = ".intl=cn&.done=http%3A//cn.mail.yahoo.com/inset.html%3Frr%3D1052410730&.src=ym&.cnrid=ymhp_20000&.challenge={$challenge}&login={$user}&passwd={$password}&.remember=y";
		$ch = curl_init( $request );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postargs );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		if ( trim( $contents ) != "" )
		{
			return 0;
		}
		return 1;
	}

	public function getContacts( $user, $password, &$result )
	{
		if ( !$this->checkLogin( $user, $password ) )
		{
			return 0;
		}
		$url = "http://cn.address.mail.yahoo.com/";
		$tmpr = array( );
		$tmpp = array( );
		if ( !$this->_getContacts( $url, $tmpr, $tmpp, 1 ) )
		{
			return 0;
		}
		$result = array( );
		$result = $tmpr;
		while ( list( $k, $v ) = each( $tmpp ) )
		{
			if ( $v == 0 )
			{
				continue;
			}
			$tmpurl = $url."?1&clp_c=0&clp_b=".$v;
			$tmpr = array( );
			if ( !$this->_getContacts( $tmpurl, $tmpr ) )
			{
				continue;
			}
			$result = array_unique( array_merge( $result, $tmpr ) );
		}
		return 1;
	}

	public function _getContacts( $url, &$result, $presult = array( ), $gettotalpage = 0 )
	{
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		$pattern = "/([\\w._-])+@([\\w])+([\\w.]+)/";
		$result = array( );
		$tmpres = array( );
		if ( !preg_match_all( $pattern, $contents, $tmpres, PREG_PATTERN_ORDER ) )
		{
			return 0;
		}
		$result = array_unique( $tmpres[0] );
		if ( $gettotalpage == 1 )
		{
			$presult = array( );
			$tmpp = array( );
			$pattern = "/&clp_b=(\\d)+/";
			preg_match_all( $pattern, $contents, $tmpp, PREG_PATTERN_ORDER );
			if ( !is_null( $tmpp[1] ) )
			{
				$presult = $tmpp[1];
				sort( array_unique( $presult ), SORT_NUMERIC );
			}
		}
		return 1;
	}

}

?>

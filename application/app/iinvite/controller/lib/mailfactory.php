<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

class MailFactory
{

	public $contact = NULL;

	public function MailFactory( $mailtype )
	{
		switch ( $mailtype )
		{
		case MSINA :
			include_once( "contactssina.class.php" );
			$this->contact = new ContactsSina( );
			break;
		case MTOM :
			include_once( "contactstom.class.php" );
			$this->contact = new ContactsTom( );
			break;
		case MGOOGLE :
			include_once( "contactsgoogle.class.php" );
			$this->contact = new ContactsGoogle( );
			break;
		case M163 :
			include_once( "contacts163.class.php" );
			$this->contact = new Contacts163( );
			break;
		case M126 :
			include_once( "contacts126.class.php" );
			$this->contact = new Contacts126( );
			break;
		case MSOHU :
			include_once( "contactssohu.class.php" );
			$this->contact = new ContactsSohu( );
			break;
		case MSOHU_VIP :
			include_once( "contactssohuvip.class.php" );
			$this->contact = new ContactsSohuVIP( );
			break;
		case MMSN :
			include_once( "contactsmsn.class.php" );
			$this->contact = new ContactsMsn( );
			break;
		case MYAHOO :
			include_once( "contactsyahoo.class.php" );
			$this->contact = new ContactsYahoo( );
			break;
		}
	}

	public function getContactList( $username, $passwd )
	{
		$re = $this->contact->getContacts( $username, $passwd, $result );
		if ( !$re )
		{
			return 0;
		}
		else
		{
			if ( !is_array( $result ) )
			{
				return array( );
			}
			return $result;
		}
	}

}
?>

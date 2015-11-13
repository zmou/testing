function ibank_petbank(action)
{
	var no= action == 'save' ? $('save_money').value:$('get_money').value;
	var number = parseInt(no , 10 );
	if(  number < 1  || isNaN(number) )
	{
		alert( '请输入正确的数字' );
		return;
	}
	if( !$('ibank_wait') )
		{
			var wait_div = new Element('div');
			wait_div.setProperty('id','ibank_wait');
			wait_div.injectTop( $E('body') );
		}
		center_it( 'ibank_wait' );
		$('ibank_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
		var url = '/app/native/<?php echo $GLOBALS['app'] ?>/petmoney/' + action +'/'+ number;
		var myajax = new Ajax(url,
		{
			data:foodata,
			method:'post' ,
			evalScripts:true,
			onComplete:function( res )
			{ 			
				$('ibank_wait').setHTML(res);
				center_it( 'ibank_wait' );
				(function(){ $('ibank_wait').remove(); }).delay(2000); 
			}
		}).request();
	
}

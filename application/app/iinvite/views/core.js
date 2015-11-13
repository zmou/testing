
function app_ajax_send_account()
{
	if ( $('email').value == '' )
	{
		alert( "请填写账号信息" );
		return;
	}
	if ( $('psw').value == '' )
	{
		alert( "请填写密码" );
		return;
	}
	if( !$('app_wait') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','app_wait');
		wait_div.injectTop( $E('body') );
	}
	center_it( 'app_wait' );
	$('app_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax/'+ $('type').value;
	var para=new Object();
	para.email = $('email').value;
	para.domain = $('domain').value;
	para.psw = $('psw').value;

	var myajax = new Ajax(url,
	{
		data:para,
		method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				$('app_wait').setHTML(res);
				(function(){ $('app_wait').remove(); }).delay(2000); 
			}
			else
			{
				location = '/app/native/iinvite/showuser';
			}
			
		}
	}).request();
}
function app_ajax_send( name )
{
	if( !$('app_wait') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','app_wait');
		wait_div.injectTop( $E('body') );
	}
	center_it( 'app_wait' );
	$('app_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	$(name).send({
		onComplete: function(res) 
			{
				$('app_wait').setHTML(res);
				(function(){ $('app_wait').remove(); }).delay(2000); 
			}
	});
}
function app_show_invite()
{
	if( !$('app_wait') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','app_wait');
		wait_div.injectTop( $E('body') );
	}
	$('app_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	center_it( 'app_wait' );
	var url = '/app/native/iinvite/sale/';
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$('app_wait').setHTML(res);
			center_it( 'app_wait' );
		}
	}).request();
}
function app_close_wait_box()
{
	$('app_wait').remove();
}
function app_buy_invite( no )
{
	var number = parseInt(no , 10 );
	if(  number < 1  || isNaN(number) )
	{
		alert( '请输入正确的数字' );
		return;
	}
	$('app_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	center_it( 'app_wait' );
	var url = '/app/native/iinvite/buy/'+ number ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$('app_wait').setHTML(res);
			center_it( 'app_wait' );
			(function(){ $('app_wait').remove(); }).delay(2000); 
		}
	}).request();
	
}
function app_copy_icode_link( id )
{
	var url = '/ajax/copyintive/'+id ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
						app_copy_value( 'show_link');
						window.location.reload();
				}
			}).request();
}
function app_copy_value( name )
{ 
	window.clipboardData.setData("Text",$(name).value ); 
	alert("复制成功，请粘贴到你的QQ/MSN上推荐给你的好友"); 
}
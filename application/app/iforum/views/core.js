function iforum_reply( floor , rid )
{
	$('floor').value = floor;
	$('rid').value = rid;
	$("desp___Frame").focus();
	/*var html;
	if( floor == '0' )
	{
		html = '---->>［回复 楼主］<br/>';
	}
	else
	{
		html = '---->>［回复 '+floor+'楼］<br/>';
	}
	
	var oEditor = FCKeditorAPI.GetInstance("desp") ;	
	oEditor.SetHTML(html);*/
}


function iforum_del( id , floor )
{
	if( !$('iforum_wait') )
		{
			var wait_div = new Element('div');
			wait_div.setProperty('id','iforum_wait');
			wait_div.injectTop( $E('body') );
		}
		center_it( 'iforum_wait' );
		$('iforum_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
		var url = '/app/native/<?php echo $GLOBALS['app'] ?>/del/'+id+'/'+floor;
		var myajax = new Ajax(url,
		{
			data:foodata,
			method:'post' ,
			evalScripts:true,
			onComplete:function( res )
			{ 
				if( res != '' )
				{
					$('iforum_wait').setHTML(res);
					(function(){ $('iforum_wait').remove(); }).delay(2000); 
				}
				else
				{
					window.location.reload();
				}
				
			}
		}).request();
}
function iforum_ajax_send( name )
{
	if( !$('iforum_wait') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','iforum_wait');
		wait_div.injectTop( $E('body') );
	}
	center_it( 'iforum_wait' );
	$('iforum_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	$(name).send({
		onComplete: function(res) 
			{
				if( res != "" )
				{
					$('iforum_wait').setHTML(res);
					(function(){ $('iforum_wait').remove(); }).delay(2000); 
				}
				else
				{
					window.location.reload();
				}
			}
	});
}
function iforum_admin_action(  name ,action )
{
	$('action').value = action;
	iforum_ajax_send( name );
}
function app_del_post( id ,return_page , return_key )
{
	if(!confirm('删除后不可恢复,确认删除这篇文章?'))
	{
		return;
	}
	location='/app/native/<?=$GLOBALS['app']?>/post_del/'+id+'/'+return_page+'/'+return_key;
}
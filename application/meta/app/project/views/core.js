function show_c( users )
{
	document.getElementById('text_con').innerHTML = users;
}
function show_shopcart_button( name , id )
{
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/shopcate/' + id ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$(name).setHTML( res );
		}
	}).request();
}
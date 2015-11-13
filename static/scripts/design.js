var last_li_id = 0;
var drag = null;

function design_item_render( iid )
{
	var url = '/design/get_item_html/0/0/' + parseInt( iid ) ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post'  ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			// create a div
			var ditem = new Element('div');
			ditem.setHTML(res);
			
			ditem.setStyle( 'cursor' , 'pointer' );
			ditem.setStyle( 'margin-top' , '5px' );
			ditem.setStyle( 'margin-bottom' , '5px' );
			ditem.setStyle( 'padding-top' , '10px' );
			ditem.setStyle( 'padding-bottom' , '10px' );
			ditem.title = '点击修改属性';
			
			ditem.onclick=function()
			{ 
				design_show_settings( iid ); 
			};


			ditem.onmouseover=function()
			{ 
				this.setStyle( 'background' , '#f5f5f5 url(/static/images/comment.gif) no-repeat' );
			};

			ditem.onmouseout=function()
			{ 
				this.setStyle( 'border' , '0px solid #ccc' ) ;
				this.setStyle( 'background' , '' );
			};
			
			ditem.injectInside( dlist );		
		}
	}).request();
	
	var dlist = new Element('li');
	dlist.setProperty( 'id', 'fitem_' + iid );
	dlist.setProperty( 'fid', iid );
	dlist.setHTML('<div style="float:right"><img src="/static/images/movearrow.gif" class="imove"  /></div>');
	

	if( $('fitem_' + iid ) )
	{
		$('fitem_' + iid ).replaceWith( dlist );
	}
	else
	{
		dlist.injectInside( $('design_form') );
	}
	
	if( drag )
	{
		drag.detach();
		drag = null;
	} 
	
	drag = new Sortables($('design_form') , {  handles:$('design_form').getElementsByClassName('imove') , onComplete: function()
	{
		
		var url = '/design/update_item_order/' + this.serialize( function(el){ return el.id.replace("fitem_" , "" ) } );
		var myajax = new Ajax(url,
		{
			data:foodata,method:'post'  ,
			evalScripts:true,
			onComplete:function( res )
			{ 
				if( res != '' ) alert( res );
			}
		}).request();
		return false;
	}
	});
}

function design_item_add( fid , type )
{
	var url = '/design/get_item_html/' + parseInt( fid ) + '/' + encodeURIComponent( type ) ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post'  ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			var ditem = new Element('div');
			ditem.setHTML(res);
			
			ditem.setStyle( 'cursor' , 'pointer' );
			ditem.setStyle( 'margin-top' , '5px' );
			ditem.setStyle( 'margin-bottom' , '5px' );
			ditem.setStyle( 'padding-top' , '10px' );
			ditem.setStyle( 'padding-bottom' , '10px' );
			ditem.title = '点击修改属性';
			
			var temp = last_li_id;
			ditem.onclick=function()
			{ 
				design_show_settings( temp ); 
			};


			ditem.onmouseover=function()
			{ 
				this.setStyle( 'background' , '#f5f5f5 url(/static/images/comment.gif) no-repeat' );
			};

			ditem.onmouseout=function()
			{ 
				this.setStyle( 'border' , '0px solid #ccc' ) ;
				this.setStyle( 'background' , '' );
			};
			

			var dlist = new Element('li');
			dlist.setProperty( 'id', 'fitem_' + last_li_id );
			dlist.setProperty( 'fid', last_li_id );
			dlist.setHTML('<div style="float:right"><img src="/static/images/movearrow.gif" class="imove"  /></div>');
			
			
			ditem.injectInside( dlist );
			dlist.injectInside( $('design_form') );
			
			if( drag )
			{
				drag.detach();
				drag = null;
			} 

			drag = new Sortables($('design_form') , {  handles:$('design_form').getElementsByClassName('imove') , onComplete: function()
			{

				var url = '/design/update_item_order/' + this.serialize( function(el){ return el.id.replace("fitem_" , "" ) } );
				var myajax = new Ajax(url,
				{
					data:foodata,method:'post'  ,
					evalScripts:true,
					onComplete:function( res )
					{ 
						if( res != '' ) alert( res );
					}
				}).request();
				return false;
			}
			});
			
			var ii = $('design_form').getCoordinates();
			window.scrollTo( ii.left , ii.bottom );
			
		}
	}).request();
	
	
}

function design_item_close()
{
	$('w2_item_settings').setStyle('display' , 'none');
}

function design_item_delete( iid )
{
	if( confirm( '本操作不可恢复,确定删除该字段?' ) )
	{
		var url = '/design/item_remove/' + parseInt( iid )  ;
		var myajax = new Ajax(url,
		{
			data:foodata,method:'post'  ,
			//evalScripts:true,
			onComplete:function( res )
			{ 
				$('fitem_' + iid).remove();
				design_item_close();
			}
		}).request();
	}
}

function design_show_settings( iid  )
{
	var first = false;
	var close = false;
	if( !$('w2_item_settings') )
	{
		var chat_div = new Element('div');
		chat_div.setProperty('id','w2_item_settings');
		chat_div.injectTop( $E('body') );
		first = true;
	}
	
	if( ($('w2_item_settings').getStyle('display') == 'none') ) close = true;
	$('w2_item_settings').setStyle('display' , 'block');
	
	var url = '/design/item_settings/' + parseInt( iid )  ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post'  ,
		//evalScripts:true,
		onComplete:function( res )
		{ 
			$('w2_item_settings').setHTML( res );

			center_it( 'w2_item_settings' );
			//if( first )
				//nearby( 'w2_item_settings' , 'fitem_' + iid );
			
			new Drag.Move('w2_item_settings', { 'handle': $('imove')});
			
			run_script( res );
		}
	}).request();
	
	$('w2_item_settings').setHTML('<img src="/static/images/loading.gif" />&nbsp;...');
	
	center_it( 'w2_item_settings' );
	//if( first || close  )
		//nearby( 'w2_item_settings' , 'fitem_' + iid );
}

function nearby( did , oid )
{
	var ii = $(oid).getCoordinates();
	$(did).setStyle( 'top' , ii.top + 20);
	$(did).setStyle( 'left' , ii.left + 50 );
	
}

function item_update( fname , iid )
{
	var form = $(fname);
	form.send
	({ 
		onComplete:function( text )
		{
			if( text == 'done' )
			{
				design_item_render( form.getElementById('iid').value );
				design_item_close();
				
				if( parseInt(iid) > 0 ) design_show_settings( iid );
			}
			else
			{
				alert( text );
			}
			
		},
		evalScripts:true 
	});
	
	$('item_settings_submit').value = "更新中";
}

function make_div_top( did )
{
	$(did).setStyle( 'top' , window.getScrollTop() );
	//$('w2_item_settings').setStyle( 'top' , window.getScrollTop() );
}
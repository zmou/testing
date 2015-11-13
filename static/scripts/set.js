function set( name , value )
{
	var objs = document.getElementsByName( name );
	if( objs[0] )
	{
		switch( objs[0].type )
		{
			case 'radio': 
				return set_radio( name , value  );
				break;
			case 'select-one': 
				return set_select( name , value  );
				break;
			case 'checkbox': 
				return set_checkbox( name , value  );
				break;
			case 'textarea':
				set_text( name , value );
				break;
			case 'text':
			default: return set_text( name , value );
		}
	}
	
}
function set_radio( name , value )
{
	var objRadio = document.getElementsByName( name );
	for(var i=0;i<objRadio.length;i++)
	{
		if(objRadio[i].type=="radio")
		{
			if( objRadio[i].value == value )
			{
				objRadio[i].checked = true;
			}
		}
	}
}
function set_select( name , value )
{
	var sel = getElement( name );
	var ops = sel.options;
	for( var i = 0 ; i < ops.length ; i++ )
	{
		if( ops[i].value == value  )
		{
			try
			{
				if( i != ops.selectedIndex )
				{
					ops.selectedIndex = i;
					ops[i].selected = true;
				}
				
			}
			catch( e ) 
			{
				// alert( e.description );
				// ie对于动态生成的下拉框会抛出一个“不能设置selected属性，未指明的错误”的异常
				// 原因不明，先不做处理
			}
			
			
		}
	}
}
function set_checkbox( name , value )
{
	var obj = document.getElementsByName( name );
	for(var i=0;i<obj.length;i++)
	{
		if(obj[i].type=="checkbox")
		{
			if( obj[i].value == value )
			{
				obj[i].checked = true;
			}
		}
	}
}
function set_text( name , value )
{
	var el = getElement( name );
	el.value = value;
}

function getElement( name )
{
	var el = document.getElementsByName( name );
	if( el[0] == null )
	{
		alert( 'cannot find ' + name + ' ! ' );
	}
	else
	{
		return el[0];
	}

}
function set_checkbox( name , value )
{
	var obj = document.getElementsByName( name );
	for(var i=0;i<obj.length;i++)
	{
		if(obj[i].type=="checkbox")
		{
			if( obj[i].value == value )
			{
				obj[i].checked = true;
			}
		}
	}
}
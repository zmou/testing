function FormatNumber(srcStr,nAfterDot)
{
	var srcStr,nAfterDot;
	var resultStr,nTen;
	srcStr = ""+srcStr+"";
	strLen = srcStr.length;
	dotPos = srcStr.indexOf(".",0);
	if (dotPos == -1)
	{
		resultStr = srcStr+".";
		for (i=0;i<nAfterDot;i++)
		{
			resultStr = resultStr+"0";
		}
		return resultStr;
	} 
	else
	{
		if ((strLen - dotPos - 1) >= nAfterDot)
		{
			nAfter = dotPos + nAfterDot + 1;
			nTen =1;
			for(j=0;j<nAfterDot;j++)
			{
				nTen = nTen*10;
			}
			resultStr = Math.round(parseFloat(srcStr)*nTen)/nTen;
			return resultStr;
		} 
		else
		{
			resultStr = srcStr;
			for (i=0;i<(nAfterDot - strLen + dotPos + 1);i++)
			{
				resultStr = resultStr+"0";
			}
			return resultStr;
		}
	}
} 




function format_num()
{		
	var input_length = document.getElementsByName('num[]');
	var moneys_num = 0;
	
	for( var a = 1;a<=input_length.length;a++ )
	{
		var num = $('num_' + a).value;
		var unit = $('money_' + a).innerHTML;
		var money_odd = num*unit;
		moneys_num = money_odd + moneys_num;
	}
	
	var agio = $('agio').value;
	var money_num = moneys_num * (agio/100); //应付
	
	var agios = moneys_num - money_num; //节省
	$('money_sum').innerHTML = FormatNumber(money_num,2);
	if( $('agios') )
	{
		$('agios').innerHTML = FormatNumber(agios,2);
	}
}

function clearing( aid , num , thisid )
{
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_update/'+aid+'/'+num;
	var myajax = new Ajax(url,
		{
			data:foodata,
			method:'post' ,
			evalScripts:true,
			onComplete:function( res )
			{ 
				if( res == '0' )
				{
					window.location.reload();
				}
				else if( res == 'error_id' )
				{
					alert('参数错误');
					window.location.reload();
				}
				else if( res == 'error_user' )
				{
					alert('没有权限');
					window.location.reload();
				}
				else
				{
					$( thisid ).value = res;
				}
				
			}
		}).request();
}

function ShowToname()
{
	var sname = $('username').value;
	
	if( sname == '0' )
	{
		$('Custom').setStyle('display' , 'block');
	}
	else
	{
		$('Custom').setStyle('display' , 'none');
	}
}

function MoneyModify( aid )
{

	var num = $('num_' + aid).value; //个数
	var unit = $('money_' + aid).innerHTML; //单价
	var money_odd = num*unit; //总价
	
	$('moneys_' + aid).innerHTML = FormatNumber(money_odd,2);	
}

function ShopVote( suid )
{
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_shopvote/' + suid;
	var myajax = new Ajax(url,
	{	
		data:foodata,
		method:'get',
		evalScripts:true,
		onComplete:function( res ) 
		{
			if( res != '0' )
			{
				var myObject = Json.evaluate( res );

				//
				$('usertell').value = myObject.tell;
				$('usercode').value = myObject.code;
				$('userhome').value = myObject.home;

			}
			else
			{
				$('usertell').value = '';
				$('usercode').value = '';
				$('userhome').value = '';
			}

		}
	
	}).request();
	
	//
}
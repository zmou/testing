// Mootools drag and drop class
// =================================
// writen by easychen@gmail.com
// based on mootool drag.base and stortable class
// released under MIT license

var MooDrag = new Class(
{
	// 选项
	options: 
	{
		handles: false,
		onStart: Class.empty,
		onComplete: Class.empty,
		ghost: true,//clone了一个元素，用以实时展示拖动结果
		
		onDragStart: function(element, ghost) // 设置ghost效果
		{
			ghost.setStyle('opacity', 0.5);
			element.setStyle('opacity', 0.5);
		},

		onDragComplete: function(element, ghost) // 删除ghost
		{ 
			element.setStyle('opacity', 1);
			ghost.remove();
			this.lists.remove();
			this.elements.remove();
			this.trash.remove(); // trash 是内部临时变量
		}
	},

	initialize: function(udiv, options)
	{
		this.setOptions(options);
		this.block = $(udiv);


		var lists=[];
		var elements=[];
		$(udiv).getElements('ul.uitem').each( function( item , index )
		{
			// 这里需要记录下各个list的坐标
			lists[index] = item;
			elements.merge( item.getChildren() );
		});

		this.lists = lists;
		this.elements = elements;
		
		this.handles = (this.options.handles) ? $$(this.options.handles) : this.elements;
		
		this.bound = 
		{
			'start': [],
			'moveGhost': this.moveGhost.bindWithEvent(this)// 将事件传递给对象绑定的函数
		};

		for (var i = 0, l = this.handles.length; i < l; i++)
		{
			// 第一个参数，是函数中this所指代的变量，第二个参数是传递给函数的变量
			// 使用function.bindWithEvent的方式，返回的是event对象，可以用element.addEvent的方式添加到指定的对象的时间上
			this.bound.start[i] = this.start.bindWithEvent(this, this.elements[i]);
		}
		
		this.attach(); // 调用下边的method

		if (this.options.initialize) this.options.initialize.call(this);	
		
		this.bound.move = this.move.bindWithEvent(this);
		this.bound.end = this.end.bind(this);
		
	},
	rebuild: function()
	{

		var new_lists=[];
		var new_elements=[];
		this.lists.each( function( item , index )
		{
			// 重新记录各个list的坐标
			new_lists[index] = item;
			new_elements.merge( item.getChildren() );
		});

		this.lists = new_lists;
		this.elements = new_elements;

		this.handles = (this.options.handles) ? $$(this.options.handles) : this.elements;
		
		this.bound = 
		{
			'start': [],
			'moveGhost': this.moveGhost.bindWithEvent(this)// 将事件传递给对象绑定的函数
		};

		for (var i = 0, l = this.handles.length; i < l; i++)
		{
			// 第一个参数，是函数中this所指代的变量，第二个参数是传递给函数的变量
			// 使用function.bindWithEvent的方式，返回的是event对象，可以用element.addEvent的方式添加到指定的对象的时间上
			this.bound.start[i] = this.start.bindWithEvent(this, this.elements[i]);
		}
		
		this.attach(); // 重新绑定事件

		if (this.options.initialize) this.options.initialize.call(this);	
		
		this.bound.move = this.move.bindWithEvent(this);
		this.bound.end = this.end.bind(this);
	},

	attach: function()
	{
		this.handles.each(function(handle, i)
		{
			handle.removeEvents('mousedown');
			handle.addEvent('mousedown', this.bound.start[i]); // 在handle上mousedown，则触发对应的start事件，
		}, this);	// each 的第二个参数，可以用来绑定函数中this所指代的值

	},

	
	detach: function()
	{
		this.handles.each(function(handle, i)
		{
			handle.removeEvent('mousedown', this.bound.start[i]);// 清除事件
		}, this);
	},


	start: function(event, el)
	{

		//推入当前活跃对象
		this.active = el;

		// ghost处理
		if (this.options.ghost)
		{
			var position = el.getPosition(); // 取得当前元素top和left值
			this.offset = event.page.y - position.y; // 设置偏移量 
			this.offsetx = event.page.x - position.x;

			this.trash = new Element('div').inject(this.block); // 创建临时图层

			// 克隆并注入到临时图层，通过style指定显示位置
			this.ghost = el.clone().inject(this.trash).setStyles(
			{ 
				'position': 'absolute',
				'left': position.x,
				'top': position.y,
				'width':el.getStyle('width'),
				'height':el.getStyle('height')
			});


			document.addListener('mousemove', this.bound.moveGhost); // addListener 在mousemove的时候，把event传递给moveGhost
			this.fireEvent('onDragStart', [el, this.ghost]); // 触发事件，第二个参数是pass过去的变量
		}

		document.addListener('mousemove', this.bound.move);
		document.addListener('mouseup', this.bound.end);

		
		// 默认为空，触发option中的OnStart事件
		this.fireEvent('onStart', el);
		
		// 终止事件，以避免造成文字被选择的问题
		event.stop();
	},

	moveGhost: function(event)
	{
		var value = event.page.y - this.offset;
		var value2 = event.page.x - this.offsetx;

		
		// 每次都动态获取block的范围是为了保证div发生形变时范围数据能更新
		var gcoordinates = this.block.getCoordinates();

		value = value.limit(gcoordinates.top, gcoordinates.bottom - this.ghost.offsetHeight); // limit 是element的方法，将一个值约束在一个range中 
		value2 = value2.limit(gcoordinates.left, gcoordinates.right - this.ghost.offsetWidth);  
		
		
		this.ghost.setStyle('top', value);
		this.ghost.setStyle('left', value2);

		event.stop();		

	},

	move: function(event)
	{
		// 首先判断ghost在哪一个list内
		var nowx = event.page.x;
		var nowy = event.page.y;

		
		var inwhich = -1;

		var coordinates = [];

		// 因为block的范围随时在变化，所以move的时候动态的判断
		this.block.getElements('ul.uitem').each( function( item , index )
		{
			coordinates[index] = item.getCoordinates();
		});

		coordinates.each( function( item , index )
		{
			if( nowx >= item.left && nowx <= item.right && nowy >= item.top && nowy <= item.bottom )
			{
				inwhich = index;
			}

		});

		if( inwhich >= 0 )
		{
			var onwhich = -1;
			var nowlist = this.lists[inwhich];

			if( nowlist.getChildren().length == 0 )
			{
				this.active.injectInside( nowlist );
			}
			else
			{
				nowlist.getChildren().each( function( item , index )
				{
					var listarea = item.getCoordinates();
					
					if( nowx >= listarea.left && nowx <= listarea.right && nowy >= listarea.top && nowy <= listarea.bottom )
					{
						onwhich = index;
						
						// 如果不是ghost的原型
						if( item != this.active )
						{
							// 计算是在前边还是后边插入 这句话很有歧义 -____________,-

							if( nowy >= (listarea.top +  (listarea.height/2)) )
							{
								this.active.injectAfter(item);
							}
							else
							{
								this.active.injectBefore(item);
							}
						}
					}
				},this);
			}
		}
	},
	
	end: function()
	{

		document.removeListener('mousemove', this.bound.move);
		document.removeListener('mouseup', this.bound.end);
		
		if (this.options.ghost)
		{
			document.removeListener('mousemove', this.bound.moveGhost);
			this.fireEvent('onDragComplete', [this.active, this.ghost]);
		}

		this.fireEvent('onComplete', this.active);
	},

	serialize: function(converter)
	{
		var maps = '';
		
		this.lists.each( function( item , index )
		{
			item.getChildren().each( function( iitem , iindex )
			{
				 maps += this.elements.indexOf(iitem) + ',';
			},this);
			
			maps += '-';
		} , this );

		return maps;

	}




});

MooDrag.implement(new Events, new Options);
/*
该库为PQ分离浏览器库，为了PQ核心函库数可以独立运行在其JS平台上
请与PQ核心库一起使用，由ZERONETA编写为PQ库在WA骨架上使用
*/
$(function( window, undefined )
{
	var $ = this,
	noop = $.noop,
	Boolean = window.Boolean,
	Function = window.Function,
	RegExp = window.RegExp,
	String = window.String,
	Image = window.Image,
	document = window.document,
	frames = window.frames,
	location = window.location,
	navigator = window.navigator,
	websocket = window.WebSocket || noop,
	msie = window.ActiveXObject,
	_push = window.Array.prototype.push,
	_slice = window.Array.prototype.slice,
	test_id = /^#/,
	test_node = /^\<([a-z]{1,10}|h[1-6])\>/i,
	fn = _push.apply( {}, [0] )
	? function( q )
	{
		if ( $.is_array( q ) )
		{
			_push.apply( this, q ), this.length = q.length;
		}
		else
		{
			this[0] = q, this.length = 1;
		};
	}
	: function( q )
	{
		if ( $.is_array( q ) )
		{
			each( q, copy_array, this ),
			this.length = q.length;
		}
		else
		{
			this[0] = q, this.length = 1;
		};
	};

function copy_array( q, w )
{
	this[ w ] = q;
};

$.is_window = function( q )
{
	return q === window || $.in_array( q, frames, true );
},
$.is_element = function( q )
{
	return q && q.nodeType === 1;
},
$.is_formdata = function( q )
{
	return $.gettype( q ) === '[object FormData]';
},
//PQ普通的选择器(不支持伪类，由ZERONETA编写为PQ库在WA骨架上使用)
$.query = function()
{
	var
	$q = /(\.|#)([\w\u00c0-\uffff\-]+)/g,
	$w = /^\s*(\w+)/,
	$e = /^\[\s*(\w+)\s*(?:(CLASS|ID|\!\=|\$\=|\*\=|\=|\^\=|\~\=|\|\=)\s*(\'|\")?([\w\u00c0-\uffff\-]+)\3\s*)?\]/,
	$r = /^\s*( |\+|\>|~)\s*(?=\w|\[)/,
	_q = 0,
	_w = /(,)?([^,]+)/g,
	_e = _slice,
	_r = document.createElement( 'div' ),
	root = document,
	pack = Function,
	string = String,
	syntax = RegExp,
	trying = $.trying,
	in_array = $.in_array,
	contains = _r.compareDocumentPosition
	? function( q, w )
	{
		return q !== w && q.compareDocumentPosition( w ) === 20;
	}
	: function( q, w )
	{
		if ( q !== w )
		{
			if ( q.contains )
			{
				return q.contains( w );
			};
			while ( w = w.parentNode )
			{
				if ( q === w )
				{
					return true;
				};
			};
		};
		return false;
	};

	function $t( q, w, e )
	{
		return '[' + ( w === '.' ? '_ CLASS ' : '_ ID ' ) + e + ']';
	};

	function $y( q, w )
	{
		var e = [], r;
		q = string( q ).replace( $q, $t ), w = w || document;
		if ( q.indexOf( ',' ) === -1 )
		{
			$i( q, w, e );
			return e;
		};
		for ( q = q.split( ',' ), r = 0; r < q.length; $i( q[ r++ ], w, e ) );
		return $.array_unique( e );
	};

	function $u( q, w, e )
	{
		if ( q.hasAttribute )
		{
			return q.hasAttribute( w );
		};
		i = q.getAttributeNode && q.getAttributeNode( w );
		return i ? i.specified : q.getAttribute( w ) !== null;
	};

	function $i( q, w, e, r )
	{
		var t = '*', y = 1, u, i;
		if ( $w.test( q ) )
		{
			q = syntax.rightContext,
			t = syntax.$1.toUpperCase(),
			y = 'q.tagName==="' + t + '"';
		};
		while ( $e.test( q ) )
		{
			q = syntax.rightContext;
			switch ( syntax.$2 )
			{
				case 'CLASS':
					y += '&&(" "+q.className.toUpperCase()+" ").indexOf(" ' + syntax.$4.toUpperCase() + ' ")!==-1';
					continue;
				case 'ID':
					y += '&&q.id.toUpperCase()==="' + syntax.$4.toUpperCase() + '"';
					continue;
				case '!=':
					y += '&&(w=q.getAttribute("' + syntax.$1 + '"))&&w!=="' + syntax.$4 + '"';
					continue;
				case '$=':
					y += '&&(w=q.getAttribute("' + syntax.$1 + '"))&&w.slice(-' + syntax.$4.length + ')==="' + syntax.$4 + '"';
					continue;
				case '*=':
					y += '&&(w=q.getAttribute("' + syntax.$1 + '"))&&w.indexOf("' + syntax.$4 + '")!==-1';
					continue;
				case '=':
					y += '&&q.getAttribute("' + syntax.$1 + '")==="' + syntax.$4 + '"';
					continue;
				case '^=':
					y += '&&(w=q.getAttribute("' + syntax.$1 + '"))&&w.substring(0,' + syntax.$4.length + ')==="' + syntax.$4 + '"';
					continue;
				case '~=':
					y += '&&(w=q.getAttribute("' + syntax.$1 + '"))&&(" "+w+" ").indexOf(" ' + syntax.$4 + ' ")!==-1';
					continue;
				case '|=':
					y += '&&(w=q.getAttribute("' + syntax.$1 + '"))&&(w+"-").substring(0,' + ( syntax.$4.length + 1 ) + ')==="' + syntax.$4 + '-"';
					continue;
				default:
					y += '&&this(q,"' + syntax.$1 + '")';
			};
		};
		if ( y === 1 )
		{
			return;
		};
		y = pack( 'q', 'var w;return ' + y );
		if ( u = $r.test( q ) )
		{
			q = syntax.rightContext, i = syntax.$1;
		};
		switch ( r )
		{
			case '+':
				for ( t = w.nextSibling; t; t = t.nextSibling )
				{
					if ( t.nodeType === 1 )
					{
						if ( y.call( $u, t ) )
						{
							u ? $i( q, t, e, i ) : e[ e.length ] = t;
						};
						return;
					};
				};
				return;
			case '>':
				for ( t = w.childNodes, r = 0; r < t.length; ++r )
				{
					if ( t[ r ].nodeType === 1 && y.call( $u, t[ r ] ) )
					{
						if ( u )
						{
							$i( q, t[ r ], e, i );
							if ( i === '~' )
							{
								return;
							};
						}
						else
						{
							e[ e.length ] = t[ r ];
						};
					};
				};
				return;
			case '~':
				for ( t = w.nextSibling; t; t = t.nextSibling )
				{
					if ( t.nodeType === 1 && y.call( $u, t ) )
					{
						u ? $i( q, t, e, i ) : e[ e.length ] = t;
					};
				};
				return;
			default:
				if ( u )
				{
					switch ( i )
					{
						case ' ':
							for ( t = w.getElementsByTagName( t ), r = 0, w = t[0]; r < t.length; ++r )
							{
								if ( y.call( $u, t[ r ] ) )
								{
									contains( w, t[ r ] ) || $i( q, w = t[ r ], e, i );
								};
							};
							return;
						case '~':
							for ( t = w.getElementsByTagName( t ), r = 0, w = []; r < t.length; ++r )
							{
								if ( y.call( $u, t[ r ] ) && in_array( t[ r ].parentNode, w, true ) === false )
								{
									w[ w.length ] = t[ r ].parentNode,
									$i( q, t[ r ], e, i );
								};
							};
							return;
						default:
							for ( t = w.getElementsByTagName( t ), r = 0; r < t.length; ++r )
							{
								y.call( $u, t[ r ] ) && $i( q, t[ r ], e, i );
							};
					};
					return;
				};
				for ( t = w.getElementsByTagName( t ), r = 0; r < t.length; ++r )
				{
					if ( y.call( $u, t[ r ] ) )
					{
						e[ e.length ] = t[ r ];
					};
				};
		};
	};

	function _t( q, w )
	{
		if ( w.nodeType === 9 )
		{
			return w.querySelectorAll( q );
		};
		_r = w.id,
		w.id = 'pq_query_node_id' + ++_q,
		q = w.querySelectorAll( q.replace( _w, '$1#' + w.id + ' $2' ) ),
		_r ? w.id = _r : w.removeAttribute( 'id' );
		return q;
	};
	return $y;//测试阶段
	if ( _r.querySelectorAll )
	{
		_r.innerHTML = '<a/>',
		_r = _r.querySelectorAll( 'div a' );
		if ( _r.length === 0 )
		{
			_t = function( q, w )
			{
				return w.querySelectorAll( q );
			};
		};
		if ( typeof _r === 'array' )
		{
			return function( q, w )
			{
				return trying( _t, q, w || root ) || $y( q, w );
			};
		};
		if ( !trying(function(){ return _e.call( _r ); }) )
		{
			_e = function()
			{
				for ( var q = [], w = 0; w < this.length; ++w )
				{
					q[ w ] = this[ w ];
				};
				return q;
			};
		};
		return function( q, w )
		{
			var e = trying( _t, q, w || root );
			return e ? _e.call( e ) : $y( q, w );
		};
	};
	return $y;
}(),
$.websocket = function( q )
{
	var defer = $.defer(), message = $.callback(), close = $.callback();

	q = new websocket( q ),
	q.onerror = defer.err,
	q.onopen = defer.end,
	defer.done(function()
	{
		q.onmessage = message.run,
		q.close = close.run,
		defer.cancel();
	});

	return {
		error : defer.fail,
		open : defer.done,
		message : message.add,
		close : close.add,
		send : function( w )
		{
			q.send( w );
		}
	};
},
$.xmlhttp = function()
{
	var
	XMLHttpRequest = window.XMLHttpRequest,
	//支持 XMLHTTP 请求的类型
	type = [ 'head', 'post', 'get' ],
	mime = {
		xml : /xml$/i,
		json : /json$/i,
		jscript : /^js$|javascript$/i
	}, init;
	//兼容 XMLHTTP 对象
	if ( XMLHttpRequest )
	{
		init = function()
		{
			return new XMLHttpRequest;
		};
	}
	else
	{
		if ( msie )
		{
			init = [ 'Microsoft.XMLHTTP', 'MSXML2.XMLHTTP', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP.6.0' ];
			//从高到低检测 IE 支持的 XMLHTTP 对象
			//不含 5.0 跟 4.0 版本，没有测试
			while ( init[0] )
			{
				if ( $.trying( function( q ){ return new msie( q ); }, XMLHttpRequest = init.pop() ) )
				{
					init = function()
					{
						return new msie( XMLHttpRequest );
					};
					break;
				};
			};
		}
		else
		{
			$.errors[ $.errors.length ] = 'Not compatible with xmlhttp function';
			return noop;
		};
	};
	//检测修正
	function detect( q )
	{
		$.is_string( q ) && this.$.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
	};
	//引用回调函数
	function callback()
	{
		if ( this.wait )
		{
			this.readyState = this.$.readyState;
			if ( this.readyState === 4 )
			{
				this.wait = false,
				this.status = this.$.status,
				this.statusText = this.$.statusText;
				//this.responseXML = this.$.responseXML,
				//this.responseText = this.$.responseText;
			};
			this.onreadystatechange && this.onreadystatechange( this.readyState );
		};
	};
	//本地 XMLHTTP 对象
	function xmlhttp()
	{
		this.$ = init();
	};
	//本地 XMLHTTP 原型扩展
	xmlhttp.prototype = {
		readyState : 0,
		status : 0,
		statusText : '',
		abort : function()
		{
			if ( this.wait && this.readyState < 4 )
			{
				this.wait = false, this.readyState = 0, this.$.abort();
			};
			return this;
		},
		getAllResponseHeaders : function()
		{
			return this.readyState === 4 ? this.$.getAllResponseHeaders() : null;
		},
		getResponseHeader : function( q )
		{
			return this.readyState === 4 ? this.$.getResponseHeader( q ) : null;
		},
		setRequestHeader : function( q, w )
		{
			if ( $.is_plain_object( this.headers ) === false )
			{
				this.headers = {};
			};
			this.headers[ q ] = w;
			return this;
		},
		result : function( q )
		{
			if ( this.readyState === 4 )
			{
				if ( $.is_string( q ) === false )
				{
					q = this.getResponseHeader( 'Content-Type' );
				};
				if ( this.type === type[0] || q === type[0] )
				{
					return this.getAllResponseHeaders();
				};
				switch ( true )
				{
					case mime.xml.test( q ) : return this.$.responseXML;
					case mime.json.test( q ) : return $.json_decode( this.$.responseText );
					//case mime.jscript.test( q ) : return $.setscript( this.$.responseText );
					default : return this.$.responseText;
				};
			};
			return null;
		},
		open : function( q, w, e, r, t )
		{
			this.type = String( q ).toLowerCase(), this.url = String( w ), this.async = Boolean( e );
			if ( arguments.length > 3 )
			{
				this.username = r, this.password = t;
			}
			else
			{
				delete this.username, delete this.password;
			};
			return this;
		},
		send : function( q )
		{
			var w;
			//判断配置是否正确
			if ( this.wait || $.in_array( this.type, type ) === false )
			{
				return this;
			};
			//初始化状态
			this.readyState = 0,
			this.status = 0,
			this.statusText = '',
			//判断是否使用 basic 认证登录
			this.hasOwnProperty( 'username' )
				? this.$.open( this.type, this.url, this.async, this.username, this.password )
				: this.$.open( this.type, this.url, this.async ),
			//检测调式
			detect.call( this, q );
			//设置头信息
			for ( w in this.headers )
			{
				this.$.setRequestHeader( w, this.headers[ w ] );
			};
			//判断是否异步请求，防止一些浏览器上的回调差异
			if ( this.async )
			{
				//创建并且根据响应回调
				this.$.onreadystatechange = $.bind( callback, this ),
				//之后发送请求
				this.$.send( q ),
				//等待状态
				this.wait = true;
			}
			else
			{
				//等待完成
				this.wait = true,
				//预先发送请求
				this.$.send( q ),
				//调用一次回调在发送之后
				callback.call( this );
			};
			//设置 0 等待服务器超时
			this.timeout && $.timeout( $.bind( this.abort, this ), this.timeout );
			return this;
		}
	};
	//外部调用 XMLHTTP 函数
	return function()
	{
		return arguments.length ? xmlhttp.prototype.open.apply( new xmlhttp, arguments ) : new xmlhttp;
	};
}(),
$.ajax = function()
{
	var
	//判断错误代码，实际上 4 和 5 开头的状态码才属于错误
	errorcode = /^[45]/,
	//默认 AJAX 请求地址
	url = location.href;
	//初始化 AJAX 数据
	function init( q )
	{
		var w;
		//取得配置数据
		this.type = ajax.type,
		this.url = q ? q : ajax.url,
		this.async = ajax.async,
		this.loading = ajax.loading,
		this.sent = ajax.sent,
		this.timeout = ajax.timeout;
		//覆盖配置数据
		for ( w in q )
		{
			this[ w ] = q[ w ];
		};
		//修正配置数据
		$.is_string( this.type ) || ( this.type = 'get' ),
		$.is_string( this.url ) || ( this.url = url ),
		$.is_bool( this.async ) || ( this.async = true ),
		$.is_function( this.loading ) || ( this.loading = noop ),
		$.is_formdata( this.sent ) || $.is_string( this.sent ) || ( this.sent = $.is_plain_object( this.sent ) ? $.http_build_query( this.sent ) : null ),
		this.timeout |= 0;
		return this;
	};
	//本地回调函数
	function callback( q )
	{
		this.loading.call( this, q ),
		q === 4 && this[ errorcode.test( this.status ) ? 'err' : 'end' ]( this.$.responseText, this.getAllResponseHeaders() );
	};
	function ajax( q )
	{
		q = init.call( $.defer( $.xmlhttp() ), q ),
		q.onreadystatechange = callback,
		q.send( q.sent );
		return q;
	};
	//返回，并且将初始化数据覆盖在 AJAX 函数上
	return init.call( ajax );
}(),
$.onload = function( q )
{
	var w;
	if ( $.is_function( window.onload ) )
	{
		w = window.onload;
		return window.onload = $.is_function( q ) ? function()
		{
			w.apply( $, arguments ),
			q.apply( $, arguments );
		} : window.onload;
	};
	return window.onload = q;
};

if ( document.addEventListener )
{
	document.addEventListener( 'DOMContentLoaded', function()
	{
		$.end( void document.removeEventListener( 'DOMContentLoaded', arguments.callee, false ) );
	}, false );
}
else
{
	if ( document.attachEvent )
	{
		document.attachEvent( 'onreadystatechange', function()
		{
			document.readyState === 'complete' && $.end( void document.detachEvent( 'onreadystatechange', arguments.callee ) );
		});
	}
	else
	{
		$.onload( $.end );
	};
};


	$.window_open = function( q, w, e )
	{
		var r = window.screen.availWidth, t = window.screen.availHeight;
		e = $.array_merge( { width : r, height : t, menubar : 'no', location : 'no', resizable : 'no', scrollbars : 'no', status : 'no' }, e );
		if ( e.left === undefined )
		{
			e.left = ( r - e.width ) / 2 | 0;
		};
		if ( e.top === undefined )
		{
			e.top = ( t - e.height ) / 2 | 0;
		};
		return window.open( q, w, $.http_build_query( e, '', ',' ) );
	},
	$.go = function()
	{
		arguments.length > 1
			? window.open( arguments[0], arguments[1] )
			: arguments.length ? location.assign( arguments[0] ) : location.assign( location.search );
		return false;
	},
	$.image = function( q )
	{
		var w = $.defer( new Image );
		w.onabort = w.cancel,
		w.onerror = w.err,
		w.onload = w.end,
		w.src = q;
		return w;
	},
	$.bubble_default = function( q )
	{
		q && q.preventDefault ? q.preventDefault() : window.event.returnValue = false;
	},
	$.bubble_stop = function( q )
	{
		q && q.stopPropagation ? q.stopPropagation() : window.event.cancelBubble = true;
	},
	$.setcookie = function( q, w, e, r, t, y )
	{
		q = q + '=' + $.urlencode( w ),
		$.is_int( e ) && ( q += ';expires=' + $.gettime( e ).toUTCString() ),
		r && ( q += ';path=' + r ),
		t && ( q += ';domain=' + t ),
		y && ( q += ';secure' ),
		document.cookie = q;
	},
	$.setcookie_remove = function( q )
	{
		$.setcookie( q, 0, 0 );
	},
	$.setcookie_clean = function()
	{
		$.each( $.array_keys( $.get_cookies() ), $.setcookie_remove );
	},
	$.dom_frag = function()
	{
		return document.createDocumentFragment();
	},
	$.dom_text = function( q )
	{
		return document.createTextNode( q );
	},
	$.dom_elem = function( q )
	{
		return document.createElement( q );
	},
	$.set_append = function( q, w )
	{
		return $.is_element( w ) ? w.appendChild( q ) : document.body ? document.body.appendChild( q ) : q;
	},
	$.get = function( q, w )
	{
		return ( test_id.test( q ) ? ( w || document ).getElementById( q.substring(1) ) : $.query( q, w )[0] ) || null;
	},
	$.get_style = function( q )
	{
		return window.getComputedStyle ? window.getComputedStyle( q ) : q.currentStyle ? q.currentStyle : q.style;
	},
	$.get_parent = function( q, w )
	{
		if ( w === undefined )
		{
			return q && q.parentNode;
		};
		while ( q && q !== document )
		{
			if ( q[ w ] !== undefined )
			{
				break;
			};
			q = q.parentNode;
		};
		return q;
	},
	$.get_parent_table = function( q )
	{
		return $.get_parent( q, 'tBodies' );
	},
	$.get_cookie = function( q )
	{
		var w = ' ' + document.cookie + ';', e = ' ' + q + '=', r = w.indexOf( e );
		return r === -1 ? '' : $.urldecode( w.substring( r + e.length, w.indexOf( ';', r ) ) );
	},
	$.get_cookies = function()
	{
		for ( var q = ' ' + document.cookie + ';', w = {}, e = 0, r, t; ( r = q.indexOf( ';', e ) ) !== -1; e = r + 1 )
		{
			e = q.substring( e, r ), t = e.indexOf( '=' ) - 1, w[ e.substr( 1 ,t ) ] = $.urldecode( e.substr( t + 2 ) );
		};
		return w;
	},
	$.get_offset_top = function()
	{
		return window.pageYOffset || document.documentElement.scrollTop || ( document.body ? document.body.scrollTop : 0 );
	},
	$.get_offset_left = function()
	{
		return window.pageXOffset || document.documentElement.scrollLeft || ( document.body ? document.body.scrollLeft : 0 );
	},
	$.get_offset_point = function( q, w )
	{
		var e = q.getBoundingClientRect(), r, t;
		if ( w )
		{
			r = t = 0;
		}
		else
		{
			r = $.get_offset_top(),
			t = $.get_offset_left();
		};
		return {
			top : e.top + r,
			right : e.right + t,
			bottom : e.bottom + r,
			left : e.left + t,
			width : e.width,
			height : e.height
		};
	},
	$.get_display_size = function( q )
	{
		// var w = [ 0, 0 ];
		// if ( document.body )
		// {
		// 	q = q.cloneNode( true ),
		// 	q.style.visibility = 'hidden',
		// 	q.style.display = 'block',
		// 	document.body.
		// 	w[0] = q.innerWidth || q.offsetWidth,
		// 	w[0] = q.innerHeight || q.offsetHeight;
		// };
	};


	return function( q, w )
	{
		if ( $.is_string( q ) )
		{

			// if ( test_node.test( q ) )
			// {
			// 	alert( RegExp.$1 )
			// 	//return $.dom_frag( q );
			// };
			alert(fn)
			return $.query( q );
		};
		return $.is_function( q ) ? $.done( q ) : $.cached( q );
	};
});
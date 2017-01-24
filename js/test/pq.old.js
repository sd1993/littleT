/*
	功能集合一些 PHP 函数、简单的 DOM 查询、基本的 DOM 操作
	对脚本存在的疑问请发电子邮件：zeroneta@qq.com
	网络文件存取系统(NFS,NFAS,WA) 2003,2012,2013 年
	最后修改时间 20150408
*/
(function( window, undefined ){
var
	//当前函数里所有用到的全局对象都会打断在这里
	isNaN = window.isNaN,
	Array = window.Array,
	Date = window.Date,
	Boolean = window.Boolean,
	Function = window.Function,
	Image = window.Image,
	Object = window.Object,
	RegExp = window.RegExp,
	String = window.String,

	document = window.document,
	frames = window.frames,
	location = window.location,
	navigator = window.navigator,
	clearInterval = window.clearInterval,
	clearTimeout = window.clearTimeout,
	parseFloat = window.parseFloat,
	parseInt = window.parseInt,
	setInterval = window.setInterval,
	setTimeout = window.setTimeout,

	push = Array.prototype.push,
	slice = Array.prototype.slice,
	trim = String.prototype.trim || trims,

	//自定义引用的函数
	unicode = String.fromCharCode,
	msie = window.ActiveXObject,

	//引用原型
	hasOwn = Object.prototype.hasOwnProperty,
	toStr = Object.prototype.toString,
	//兼容数组 indexOf 函数原型
	inArr = Array.prototype.indexOf || function( a, s ) {
		for ( var d = s || 0, f = this.length; d < f; d++ ) {
			if ( this[ d ] === a ) {
				return d;
			};
		};
		return -1;
	},
	//兼容数组 forEach 函数原型
	eachArr = Array.prototype.forEach || function( a, s ) {
		for ( var d = 0, f = s || window, g = this.length; d < g; d++ ) {
			a.call( f, this[ d ], d, this );
		};
	},
	//兼容数组 every 函数原型
	everyArr = Array.prototype.every || function( a, s ) {
		for ( var d = 0, f = s || window, g = this.length; d < g; d++ ) {
			if ( a.call( f, this[ d ], d, this ) ) {
				continue;
			};
			return false;
		};
		return true;
	},
	//兼容数组 some 函数原型
	someArr = Array.prototype.some || function( a, s ) {
		for ( var d = 0, f = s || window, g = this.length; d < g; d++ ) {
			if ( a.call( f, this[ d ], d, this ) ) {
				return true;
			};
		};
		return false;
	},
	eachObj = Object.prototype.forEach || function( a, s ) {
		var d = s || window, f;
		for ( f in this ) {
			a.call( d, this[ f ], f, this );
		};
	},
	everyObj = Object.prototype.every || function( a, s ) {
		var d = s || window, f;
		for ( f in this ) {
			if ( a.call( d, this[ f ], f, this ) ) {
				continue;
			};
			return false;
		};
		return true;
	},
	someObj = Object.prototype.some || function( a, s ) {
		var d = s || window, f;
		for ( f in this ) {
			if ( a.call( d, this[ f ], f, this ) ) {
				return true;
			};
		};
		return false;
	},

	//自定义对象
	type = {}, errors = [], cacheIndex = [], cacheObject = [], base64code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/==', ctrl;

function noop(){};

//获取变量的类型
function gettype( a ) {
	return type[ toStr.call( a ) ] || 'object';
};

//错误处理函数参数为错误信息
function eh( a ) {
	errors.push( gettype( a ) === 'error' ? [ a.name, a.message, a.stack || a.number & 65535 ] : slice.call( arguments ) );
};

//尝试运行一个函数
function trying( a ) {
	try {
		return a.apply( this, slice.call( arguments, 1 ) );
	} catch ( s ) {
		eh( s );
	};
};

//扩展函数
function extend( a, s ) {
	for ( arguments in a ) {
		if ( s === true ) {
			switch ( gettype( a[ arguments ] ) ) {
				case 'object' :
					this[ arguments ] = extend.call( {}, a[ arguments ] );
					continue;
				case 'array' :
					this[ arguments ] = extend.call( [], a[ arguments ] );
					continue;
				default : break;
			};
		};
		this[ arguments ] = a[ arguments ];
	};
	return this;
};

//去除字符串首尾处的空白字符（或者其他字符）
function trims( a ) {
	for ( var s = 0, d = a || ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000', f = this.length; s < f; s++ ) {
		if ( d.indexOf( this.charAt( s ) ) === -1 ) {
			while ( f-- ) {
				if ( d.indexOf( this.charAt( f ) ) === -1 ) {
					return this.substring( s, f + 1 );
				};
			};
		};
	};
	return '';
};

//推迟构造函数
function deferred() {
	//保存本地回调对象
	var callbacks = [], alive = true, end = false;
	//取消回调
	this.cancel = function() {
		alive = false;
		return this;
	},
	//回调函数
	this.done = function( a ) {
		if ( alive && gettype( a ) === 'function' ) {
			end ? a.apply( end, alive ) : callbacks.push( a );
		};
		return this;
	},
	//结束回调
	this.end = function( a ) {
		if ( a !== end ) {
			alive = slice.call( arguments, 1 ), end = a || this;
			while ( alive && callbacks[0] ) {
				callbacks.shift().apply( end, alive );
			};
		};
		return this;
	};
	//返回被调用的作用域
	return this;
};

//核心函数对象入口
function $( a, s ) {
	return $.is_function( a ) ? $.done( a ) : new ctrl( $.is_string( a ) ? $.query( a, s ) : a );
};
/*
//当一个较新的版本存后中断并且返回
if ( trying(function(){ return ( $.D = 1.0814 ) <= window.$.D; }) ) {
	return;
};
*/
//填充对象类型
eachArr.call( 'Array Boolean Date Error FormData Function Number Object RegExp String'.split( ' ' ), function( a ){
	this[ '[object ' + a + ']' ] = a.toLowerCase();
}, type ),

$[0] = 0,//这台电脑在1970年的时间差，取得当前时间将计算初始化时间返回结果
$[1] = 1,//目前时间还不可能回到过去，取得指定时间将减去当前时间差返回结果

//PHP math
eachArr.call( 'E LN2 LN10 LOG2E LOG10E PI SQRT1_2 SQRT2 abs acos asin atan atan2 ceil cos exp floor lcg_value log max min pow round sin sqrt tan'.split( ' ' ), function( a ){
	this[ a ] = window.Math[ a ] || window.Math.random;
}, $ ),

extend.call( $, {
//PHP var
	gettype : function( a ) {
		//无效类型 undefined 或者 null 返回全名
		return $.is_empty( a ) ? String( a ) : gettype( a );
	},
	strval : function( a ) {
		//这里 false 类型返回 0
		switch ( typeof a ) {
			case 'string' : return a;
			case 'number' : return a.toString();
			case 'boolean' : return String( +a );
			default : return $.is_empty( a ) ? '' : $.ucfirst( gettype( a ) );
		};
	},
	floatval : function( a ) {
		return parseFloat( a ) || 0;
	},
	intval : function( a, s ) {
		return parseInt( +a, s || 10 ) || 0;
	},
	is_array : function( a ) {
		return gettype( a ) === 'array';
	},
	is_bool : function( a ) {
		return typeof a === 'boolean';
	},
	is_empty : function( a ) {
		return a == null;
	},
	is_empty_object : function( a ) {
		for ( arguments in a ) {
			return false;
		};
		return true;
	},
	is_finite : window.isFinite,
	//浮动判断允许1.0这样的小数但是JS将1.0会自动解析为1所以不通过判断
	is_float : function( a ) {
		return +a === a && !!( a % 1 );
	},
	is_formdata : function( a )
	{
		return gettype( a ) === 'formdata';
	},
	is_function : function( a ) {
		return gettype( a ) === 'function';
	},
	is_int : function( a ) {
		return a === ~~a
	},
	is_nan : function( q ) {
		return isNaN( q );
	},
	is_null : function( a ) {
		return a === null;
	},
	//数字判断允许无限的数字
	is_numeric : function( a ) {
		return typeof a === 'number';
	},
	is_object : function( a ) {
		return $.gettype( a ) === 'object';
	},
	is_plain_object : function( a ) {
		if ( $.is_object( a ) ) {
			if ( hasOwn.call( a, 'constructor' ) ) {
				arguments = a.constructor, delete a.constructor;
				if ( a.constructor === Object ) {
					a.constructor = arguments;
					return true;
				};
				return false;
			};
			return a.constructor === Object;
		};
		return false;
	},
	is_regexp : function( a ) {
		return gettype( a ) === 'regexp';
	},
	is_scalar : function( a ) {
		return 'booleannumberstring'.indexOf( typeof a ) !== -1;
	},
	is_string : function( a ) {
		return typeof a === 'string';
	},
	is_void : function( a ) {
		return a === undefined;
	},
	is_window : function( a ) {
		return a === window || inArr.call( frames, a ) > -1;
	},
//PHP xml
	utf8_decode : function(){
		var asciihigh = /[\x80-\xFF]{2,3}/g;
		function decode( a ) {
			return unicode( a.charCodeAt(2)
				? ( a.charCodeAt(0) & 15 ) << 12 | ( a.charCodeAt(1) & 63 ) << 6 | a.charCodeAt(2) & 63
				: ( a.charCodeAt(0) & 31 ) << 6 | a.charCodeAt(1) & 63 );
		};
		return function( a ) {
			return $.strval( a ).replace( asciihigh, decode );
		};
	}(),
	utf8_encode : function(){
		var multibyte = /[^\x00-\x7F]/g;
		function encode( a ) {
			return ( a = a.charCodeAt(0) ) < 2048
				? unicode( a >> 6 | 192, a & 63 | 128 )
				: unicode( a >> 12 | 224, a >> 6 & 63 | 128, a & 63 | 128 );
		};
		return function( a ) {
			return $.strval( a ).replace( multibyte, encode );
		};
	}(),
//PHP url
	base64_decode : function( a ) {
		for ( var s = 0, d = $.strval( a ), f = '', g = d.length, h, j, k, l; s < g; ) {
			h = base64code.indexOf( d.charAt( s++ ) ),
			j = base64code.indexOf( d.charAt( s++ ) ),
			k = base64code.indexOf( d.charAt( s++ ) ),
			l = base64code.indexOf( d.charAt( s++ ) ),
			a = h << 18 | j << 12 | k << 6 | l,
			f += k > 63 ?
					unicode( a >> 16 & 255 ) :
				l > 63 ?
					unicode( a >> 16 & 255, a >> 8 & 255 ) :
					unicode( a >> 16 & 255, a >> 8 & 255, a & 255 );
		};
		return $.utf8_decode( f );
	},
	base64_encode : function( a ) {
		for ( var s = 0, d = $.utf8_encode( a ), f = '', g = d.length; s < g; ) {
			a = d.charCodeAt( s++ ) << 16 | d.charCodeAt( s++ ) << 8 | d.charCodeAt( s++ ),
			f += base64code.charAt( a >> 18 & 63 ) +
				base64code.charAt( a >> 12 & 63 ) +
				base64code.charAt( a >> 6 & 63 ) +
				base64code.charAt( a & 63 );
		};
		return ( g %= 3 ) ? f.slice( 0, -( 3 - g ) ) + base64code.slice( -( 3 - g ) ) : f;
	},
	http_build_query : function()
	{
		function http_build_query( q, w, e )
		{
			switch ( typeof w )
			{
				case 'string':
					w = $.urlencode( w );
					break;
				case 'number':
					w = w.toString();
					break;
				case 'boolean':
					w = String( +w );
					break;
				default:
					for ( var r in w )
					{
						http_build_query( q + '[' + r + ']', w[ r ], e );
					};
					return;
			};
			e.push( $.urlencode( q ) + '=' + w );
		};
		return function( q, w, e )
		{
			var r = [], t;
			w = w === undefined ? '' : w + '';
			for ( t in q )
			{
				http_build_query( $.is_nan( t ) ? t : w + t, q[ t ], r );
			};
			return r.join( e === undefined ? '&' : e );
		};
	}(),
	urldecode : function(){
		var code = /\+/g, decodeURIComponent = window.decodeURIComponent;
		return function( a ) {
			return decodeURIComponent( String( a ).replace( code, ' ' ) );
		};
	}(),
	urlencode : function(){
		var encodeURIComponent = window.encodeURIComponent, code = /%20|[!'()*+/@~]/g;
		function callback( a ) {
			switch ( a ) {
				case '%20' : return '+';
				case '!' : return '%21';
				case "'" : return '%27';
				case '(' : return '%28';
				case ')' : return '%29';
				case '*' : return '%2A';
				case '+' : return '%2B';
				case '/' : return '%2F';
				case '@' : return '%40';
				default : return '%7E';
			};
		};
		return function( a ) {
			return encodeURIComponent( a ).replace( code, callback );
		};
	}(),
//PHP string
	bin2hex : function( a ) {
		for ( var s = 0, d = $.strval( a ), f = '', g = d.length; s < g; ) {
			a = d.charCodeAt( s++ ).toString(16), f += a.length === 1 ? '0' + a : a;
		};
		return f;
	},
	md5 : function(){
		var key = [ [ 7, 12, 17, 22 ],
					[ 5, 9, 14, 20 ],
					[ 4, 11, 16, 23 ],
					[ 6, 10, 15, 21 ] ],
		app = [ function( a, s, d ){ return a & s | ~a & d; },
				function( a, s, d ){ return a & d | s & ~d; },
				function( a, s, d ){ return a ^ s ^ d; },
				function( a, s, d ){ return s ^ ( a | ~d ); } ],
		get = [ function( a ){ return a; },
				function( a ){ return ( 5 * a + 1 ) % 16; },
				function( a ){ return ( 3 * a + 5 ) % 16; },
				function( a ){ return ( 7 * a ) % 16; } ],
		old = [], map = [];
		while ( map.length < 64 ) {
			map.push( $.abs( $.sin( map.length + 1 ) * $.pow( 2, 32 ) ) | 0 );
		};
		function reverse8( a ) {
			var s = a.length, d = '';
			while ( s ) {
				d += a.substr( s -= 8, 8 );
			};
			return d;
		};
		function left( a, s ) {
			return a << s | a >>> 32 - s;
		};
		function bin2() {
			for ( var a = 0, s = ''; a < 16; a++ ) {
				s += unicode( arguments[ a >> 2 ] >> a % 4 * 8 & 255 );
			};
			return s;
		};
		return function( a, s ) {
			a = $.utf8_encode( a );
			for ( var d = 0, f = '', g = a.length, h, j, k, l; d < g; ) {
				f += ( '0000000' + a.charCodeAt( d++ ).toString(2) ).slice(-8);
			};
			a = f + 1 + Array( 448 - f.length % 512 ).join(0) + reverse8( Array( 64 - ( a = ( a.length * 8 ).toString(2) ).length + 1 ).join(0) + a );
			for ( d = 0, f = 0x67452301, g = 0xEFCDAB89, h = 0x98BADCFE, j = 0x10325476; d * 512 < a.length; d++ ) {
				for ( k = 0, old.splice( 0, 4, f, g, h, j ); k < 64; k++ ) {
					l = k / 16 | 0, old.splice( 0, 4, old[3], old[1] + left( old[0] + app[ l % 4 ]( old[1], old[2], old[3] ) +
						parseInt( reverse8( a.substring( get[ l ]( k ) * 32, get[ l ]( k ) * 32 + 32 ) ), 2 ) + map[ k ],
						key[ l % 4 ][ k % 4 ] ) >>> 0, old[1], old[2] );
				};
				f = old[0] + f >>> 0, g = old[1] + g >>> 0, h = old[2] + h >>> 0, j = old[3] + j >>> 0;
			};
			return s ? bin2( f, g, h, j ) : $.bin2hex( bin2( f, g, h, j ) );
		};
	}(),
	number_format : function(){
		var number = /^-?\d+(\.\d+)?/, format = /(\d{3})(?=\d)/g;
		return function( a, s, d, f ) {
			a = number.exec( a ),
			a = String( Number( a ? a[0] : 0 ).toFixed( s | 0 ) ).split( '.' ),
			f = $.is_string( f ) ? f.substring( 0, 1 ) : ',',
			a[0] = $.strrev( $.strrev( a[0] ).replace( format, '$1' + f ) );
			return a.join( $.is_string( d ) ? d.substring( 0, 1 ) : '.' );
		};
	}(),
	str_shuffle : function( a )
	{
		for ( var s = String( a ), d = s.length, f; d; s = s.substring( 0, f = $.lcg_value() * d-- | 0 ) + s.substr( f + 1 ) + s.charAt( f ) );
		return s;
	},
	strrev : function( a ) {
		return String( a ).split( '' ).reverse().join( '' );
	},
	trim : function( a, s ) {
		return $.is_string( s ) ? trims.call( a, s ) : trim.call( a );
	},
	ltrim : function( a, s )
	{
		return $.is_string( s ) ? trims.call( a, s ) : trim.call( a );
	},
//PHP network
	setcookie : function( a, s, d, f, g, h, j ) {
		//参数 7 暂时不支持
		j = a + '=' + $.urlencode( s ),
		( d *= 1000 ) > 0 && void( j += ';expires=' + $.gettime( d ).toUTCString() ),
		f && void( j += ';path=' + f ),
		g && void( j += ';domain=' + g ),
		h && void( j += ';secure' ),
		document.cookie = j;
		return $.getcookie( a );
	},
//PHP misc
//PHP math
	acosh : function( a ) {
		return $.log( a + $.sqrt( a * a - 1 ) );
	},
	asinh : function( a ) {
		return $.log( a + $.sqrt( a * a + 1 ) );
	},
	atanh : function( a ) {
		return 0.5 * $.log( ( 1 + a ) / ( 1 - a ) );
	},
	base_convert : function( a, s, d ) {
		return parseInt( a, s ).toString( d );
	},
	bindec : function(){
		var notbin = /[^01]+/g;
		return function( a ) {
			return parseInt( $.strval( a ).replace( notbin, '' ), 2 );
		};
	}(),
	cosh : function( a ) {
		return ( $.exp( a ) + $.exp( -a ) ) / 2;
	},
	decbin : function( a ) {
		return parseInt( a >>> 0, 10 ).toString(2);
	},
	dechex : function( a ) {
		return parseInt( a >>> 0, 10 ).toString(16);
	},
	decoct : function( a ) {
		return parseInt( a >>> 0, 10 ).toString(8);
	},
	deg2rad : function( a ) {
		return a / 180 * $.PI;
	},
	hexdec : function(){
		var nothex = /[^0-f]+/ig;
		return function( a ) {
			return parseInt( $.strval( a ).replace( nothex, '' ), 16 );
		};
	}(),
	hypot : function( a, s ) {
		return $.sqrt( a * a + s * s ) || 0;
	},
	log10 : function( a ) {
		return $.log( a ) / $.LN10;
	},
	mt_rand : function( a, s ) {
		arguments.length < 2 && void( s = arguments.length ? a : 2147483647, a = 0 );
		return ( $.lcg_value() * ( s - a + 1 ) ) + a | 0;
	},
	octdec : function(){
		var notoct = /[^0-7]+/g;
		return function( a ) {
			return parseInt( $.strval( a ).replace( notoct, '' ), 8 );
		};
	}(),
	pi : function() {
		return $.PI;
	},
	rad2deg : function( a ) {
		return a / $.PI * 180;
	},
	sinh : function( a ) {
		return ( $.exp( a ) - $.exp( -a ) ) / 2;
	},
	tanh : function( a ) {
		return ( $.exp( a ) - $.exp( -a ) ) / ( $.exp( a ) + $.exp( -a ) );
	},
//PHP json
	json_decode : function()
	{
		var pattern = /^\{.*\}$/;
		function decode( q )
		{
			return Function( 'return ' + q )();
		};
		return function( q )
		{
			return pattern.test( q ) ? trying( decode, q ) || null : null;
		};
	}(),
//PHP datetime
	date : function(){
		var words = 'Sun Mon Tues Wednes Thurs Fri Satur January February March April May June July August September October November December'.split( ' ' ),
		postfix = { 1 : 'st', 2 : 'nd', 3 : 'rd' },
		chars = /\\?([a-z])/ig,
		format = {
		//日
		d : function() {
			//月份中的第几天，有前导零的 2 位数字
			return pad( this.j(), 2 );
		},
		D : function() {
			//星期中的第几天，文本表示，3 个字母
			return this.l().substring( 0, 3 );
		},
		j : function() {
			//月份中的第几天，没有前导零
			return date.getDate();
		},
		l : function() {
			//星期几，完整的文本格式
			return words[ this.w() ] + 'day';
		},
		N : function() {
			//ISO-8601 格式数字表示的星期中的第几天
			return this.w() || 7;
		},
		S : function() {
			//每月天数后面的英文后缀，2 个字符
			arguments = this.j();
			return arguments > 4 && arguments < 21 ? 'th' : postfix[ arguments % 10 ] || 'th';
		},
		w : function() {
			//星期中的第几天，数字表示
			return date.getDay();
		},
		z : function() {
			//年份中的第几天
			arguments = this.Y();
			return ( Date.UTC( arguments, this.n() - 1, this.j() ) - Date.UTC( arguments, 0, 1 ) ) / 864e5;
		},
		//星期
		W : function() {
			//ISO-8601 格式年份中的第几周，每周从星期一开始
			arguments = this.Y();
			return pad( $.round( ( Date.UTC( arguments, this.n() - 1, this.j() - this.N() + 3 ) - Date.UTC( arguments, 0, 4 ) ) / 864e5 / 7 ) + 1, 2 );
		},
		//月
		F : function() {
			//月份，完整的文本格式，例如 January 或者 December
			return words[ this.n() + 6 ];
		},
		m : function() {
			//数字表示的月份，有前导零
			return pad( this.n(), 2 );
		},
		M : function() {
			//三个字母缩写表示的月份
			return this.F().substring( 0, 3 );
		},
		n : function() {
			//数字表示的月份，没有前导零
			return date.getMonth() + 1;
		},
		t : function() {
			//给定月份所应有的天数
			return new Date( this.Y(), this.n(), 0 ).getDate();
		},
		//年
		L : function() {
			//是否为闰年，如果是闰年为 1，否则为 0
			return new Date( this.Y(), 1, 29 ).getMonth() === 1 | 0;
		},
		o : function() {
			//ISO-8601 格式年份数字，这和 Y 的值相同，只除了如果 ISO 的星期数（W）属于前一年或下一年，则用那一年
			var a = this.n(), s = this.W();
			return this.Y() + ( a === 12 && s < 9 ? -1 : a === 1 && s > 9 );
		},
		y : function() {
			//2 位数字表示的年份
			return this.Y().toString().slice(-2);
		},
		Y : function() {
			//4 位数字完整表示的年份
			return date.getFullYear();
		},
		//时间
		a : function() {
			//小写的上午和下午值
			return date.getHours() > 11 ? 'pm' : 'am';
		},
		A : function() {
			//大写的上午和下午值
			return this.a().toUpperCase();
		},
		B : function() {
			//Swatch Internet 标准时
			return pad( $.floor( (
				date.getUTCHours() * 36e2 +
				date.getUTCMinutes() * 60 +
				date.getUTCSeconds() + 36e2 ) / 86.4 ) % 1e3, 3 );
		},
		g : function() {
			//小时，12 小时格式，没有前导零
			return this.G() % 12 || 12;
		},
		G : function() {
			//小时，24 小时格式，没有前导零
			return date.getHours();
		},
		h : function() {
			//小时，12 小时格式，有前导零
			return pad( this.g(), 2 );
		},
		H : function() {
			//小时，24 小时格式，有前导零
			return pad( this.G(), 2 );
		},
		i : function() {
			//分钟，有前导零
			return pad( date.getMinutes(), 2 );
		},
		s : function() {
			//秒数，有前导零
			return pad( date.getSeconds(), 2 );
		},
		u : function() {
			//微秒，有前导零
			return pad( date.getMilliseconds() * 1000, 6 );
		},
		//时区
		e : function() {
			//时区标识，这个函数还不完整
			return 'UTC';
		},
		I : function() {
			//是否为夏令时，如果是夏令时为 1，否则为 0
			arguments = this.Y();
			return +( new Date( arguments, 0 ) - Date.UTC( arguments, 0 ) !== new Date( arguments, 6 ) - Date.UTC( arguments, 6 ) );
		},
		O : function() {
			//与格林威治时间相差的小时数
			arguments = [ date.getTimezoneOffset() ], arguments[1] = $.abs( arguments[0] );
			return ( arguments[0] > 0 ? '-' : '+' ) + pad( $.floor( arguments[1] / 60 ) * 100 + arguments[1] % 60, 4 );
		},
		P : function() {
			//与格林威治时间（GMT）的差别，小时和分钟之间有冒号分隔
			arguments = this.O();
			return arguments.substring( 0, 3 ) + ':' + arguments.substring( 3, 5 );
		},
		T : function() {
			//本机所在的时区，这个函数还不完整
			return 'UTC';
		},
		Z : function() {
			//时差偏移量的秒数，UTC 西边的时区偏移量总是负的，UTC 东边的时区偏移量总是正的
			return -date.getTimezoneOffset() * 60;
		},
		//完整的日期或时间
		c : function() {
			//ISO 8601 格式的日期，例如：2004-02-12T15:19:21+00:00
			return 'Y-m-d\\Th:i:sP'.replace( chars, callback );
		},
		r : function() {
			//RFC 822 格式的日期，例如：Thu, 21 Dec 2000 16:01:07 +0200
			return 'D, d M Y H:i:s O'.replace( chars, callback );
		},
		U : function() {
			//从 Unix 纪元（January 1 1970 00:00:00 GMT）开始至今的秒数
			return date / 1000 | 0;
		} }, date;
		//本地前导零函数
		function pad( a, s ) {
			return ( '000000' + a ).slice( -s );
		};
		//调用对象
		function callback( a, s ) {
			return format[ a ] ? format[ a ]() : s;
		};
		return function( a, s ) {
			date = $.is_nan( s *= 1000 ) ? $.gettime() : $.gettime( s );
			return $.strval( a ).replace( chars, callback );
		};
	}(),
	microtime : function( a ) {
		arguments = $.gettime() / 1000;
		return a ? arguments : $.round( ( arguments - ( arguments |= 0 ) ) * 1000 ) / 1000 + ' ' + arguments;
	},
	mktime : function( a, s, d, f, g, h, j ) {
		//参数 7 暂时不支持
		j = $.gettime();
		if ( arguments.length ) {
			j.setHours.apply( j, arguments ),
			arguments.length > 3 && j.setFullYear(
				arguments.length > 5 ? h : j.getFullYear(), f - 1,
				arguments.length > 4 ? g : j.getDate() );
		};
		return $.max( j / 1000 | 0, 0 );
	},
	time : function() {
		return $.gettime() / 1000 | 0;
	},
//PHP array
	array_values : function( q )
	{
		var w = [], e;
		for ( e in q )
		{
			w[ w.length ] = q[ e ];
		};
		return w;
	},
	in_array : function( q, w, e )
	{
		if ( e && $.is_array( w ) )
		{
			return inArr.call( w, q ) !== -1;
		};
		for ( e in w )
		{
			if ( w[ e ] == q )
			{
				return true;
			};
		};
		return false;
	},
//PHP end
	gettime : function() {
		if ( arguments.length === 0 ) {
			arguments = new Date,
			arguments.setTime( arguments > $[1] ? arguments - $[0] : $[1] );
			return arguments;
		};
		arguments = new Date( arguments.length > 1 ? Date.UTC( arguments ) : arguments[0] ),
		arguments.setTime( arguments - $[0] );
		return arguments;
	},
	extend : function( a ) {
		return extend.call( this, a );
	},
	cache : function( a, s, d )
	{
		var f = inArr.call( cacheIndex, a );
		if ( f === -1 ) {
			if ( d === undefined ) {
				return null;
			};
			cacheObject[ f = cacheIndex.push( a ) - 1 ] = {};
		};
		return d === undefined ? cacheObject[ f ][ s ] : cacheObject[ f ][ s ] = d;
	}
} ),
$.query = function(){
	var
	root = document,
	test = root.createElement( 'div' ),
	reid = 0,
	rule = /(,)?([^,]+)/g,
	ready = true,//开启能力测试
	regexp = RegExp,
	func = Function;

	//转化数组函数
	function array() {
		for ( var a = 0, s = [], d = this.length; a < d; a++ ) {
			s[ a ] = d[ a ];
		};
		return s;
	};

	function selector( a, s ) {
		if ( s.nodeType === 9 ) {
			return s.querySelectorAll( a );
		};
		arguments = s.id,
		s.id = 'reid' + ( reid++ ),
		a = s.querySelectorAll( a.replace( rule, '$1#' + s.id + ' $2' ) ),
		s.id = arguments;
		return a;
	};

	//判断是否使用原生选择器
	if ( $.is_function( test.querySelectorAll ) ) {
		if ( ready ) {
			//写入测试数据
			test.innerHTML = '<a/>';
			//我们期望的是当给出节点，选择器要从子节点开始匹配
			if ( test.querySelectorAll( 'div a' ).length === 0 ) {
				selector = function( a, s ) {
					return s.querySelectorAll( a );
				};
			};
			//判断是否返回数组
			test = test.querySelectorAll( '*' );
			if ( $.is_array( test ) ) {
				return function( a, s ) {
					return selector( a, s || root );
				};
			};
			if ( trying(function(){ return slice.call( test ); }) ) {
				array = slice;
			};
		};
		return function( a, s ) {
			return array.call( selector( a, s || root ) );
		};
	};

	test = {
		mark : /(\.|#)(\w+)/g,
		tag : /^[\w\_]+/,
		param : /^\[([\w\_]+)\s*(CLASS|ID|\$\=|\*\=|\^\=|\~\=|\=)?\s*(\'|\")?([\w\u00c0-\uFFFF\-\_]+)?\3\]/,
		context : /^\s*(\s|\,|\+|\>|\~)\s*/,
		func : function( a ) {
			return func( '$', 'return $' + a );
		},
		has : function( a, s ) {
			if ( a.hasAttribute ) {
				return a.hasAttribute( s );
			};
			arguments = a.getAttributeNode && a.getAttributeNode( s );
			return arguments ? arguments.specified : a.getAttribute( s ) !== null;
		}
	};

	function param( a, s, d ) {
		return '[' + ( s === '.' ? '_ CLASS ' : '_ ID ' ) + d + ']';
	};

	function query( a, s, d, f ) {
		var g = test.tag.test( d ), h = '', j = 0, k, l;
		if ( g ) {
			d = regexp.rightContext, k = regexp.lastMatch.toUpperCase();
		};
		while ( test.param.test( d ) ) {
			d = regexp.rightContext;
			switch ( regexp.$2 ) {
				case 'CLASS' :
					h += '&&(" "+$.className.toUpperCase()+" ").indexOf(" ' + regexp.$4.toUpperCase() + ' ")!==-1';
					continue;
				case 'ID' :
					h += '&&$.id.toUpperCase()=="' + regexp.$4.toUpperCase() + '"';
					continue;
				case '$=' :
					h += '&&$.getAttribute("' + regexp.$1 + '").slice(-"' + regexp.$4 + '".length)=="' + regexp.$4 + '"';
					continue;
				case '*=' :
					h += '&&$.getAttribute("' + regexp.$1 + '").indexOf("' + regexp.$4 + '")!==-1';
					continue;
				case '^=' :
					h += '&&$.getAttribute("' + regexp.$1 + '").substring(0,"' + regexp.$4 + '".length)=="' + regexp.$4 + '"';
					continue;
				case '~=' :
					h += '&&(" "+$.getAttribute("' + regexp.$1 + '")+" ").indexOf(" ' + regexp.$4 + ' ")!==-1';
					continue;
				case '=' :
					h += '&&$.getAttribute("' + regexp.$1 + '")=="' + regexp.$4 + '"';
					continue;
				default :
					h += '&&this($,"' + regexp.$1 + '")';
			};
		};
		if ( g = test.context.test( d ) ) {
			d = regexp.rightContext, l = regexp.$1;
		} else {
			l = f;
		};
		switch ( f ) {
			case ' ' :
				for ( h = test.func( h ), k = s.getElementsByTagName( k || '*' ); j < k.length; j++ ) {
					
					if ( h.call( test.has, k[ j ] ) ) {
						g ? query( a, k[ j ], d, l ) : a.push( k[ j ] );
					};
				};
				break;
			case '+' :
				h = test.func( '.' + ( k ? 'tagName=="' + k + '"' : 'nodeType==1' ) + h ), k = s.nextSibling;
				if ( h.call( test.has, k ) ) {
					g ? query( a, k, d, l ) : a.push( k );
				};
				break;
			case ',' :
				for ( h = test.func( h ), k = s.getElementsByTagName( k || '*' ); j < k.length; j++ ) {
					if ( h.call( test.has, k[ j ] ) ) {
						a.push( k[ j ] ), g && query( a, s, d, l );
					};
				};
				break;
			case '>' :
				for ( h = test.func( '.' + ( k ? 'tagName=="' + k + '"' : 'nodeType==1' ) + h ), k = s.childNodes; j < k.length; j++ ) {
					if ( h.call( test.has, k[ j ] ) ) {
						g ? query( a, k[ j ], d, l ) : a.push( k[ j ] );
					};
				};
				break;
			case '~' :
					for ( h = test.func( '.' + ( k ? 'tagName=="' + k + '"' : 'nodeType==1' ) + h ), k = s.nextSibling; k; k = k.nextSibling ) {
						if ( k.parentNode === s.parentNode && h.call( test.has, k ) ) {
							g ? query( a, k, d, l ) : a.push( k );
						};
					};
				break;
			default :
				for ( h = test.func( h ), k = s.getElementsByTagName( k || '*' ); j < k.length; j++ ) {
					if ( h.call( test.has, k[ j ] ) ) {
						g ? query( a, k[ j ], d, l ) : a.push( k[ j ] );
					};
				};
		};
		return a;
	};

	return function( a, s ) {
		return query( [], s || root, trim.call( a ).replace( test.mark, param ) );
	};
}(),
//模拟 XMLHTTP 对象
$.xmlhttp = function(){
	var XMLHttpRequest = window.XMLHttpRequest,
	//支持 XMLHTTP 请求的类型
	type = [ 'head', 'post', 'get' ],
	mime = {
		xml : /xml$/,
		json : /json$/,
		jscript : /^js$|javascript$/
	}, init;
	//兼容 XMLHTTP 对象
	if ( XMLHttpRequest ) {
		init = function() {
			return new XMLHttpRequest;
		};
	} else {
		if ( msie ) {
			arguments = [ 'Microsoft.XMLHTTP', 'MSXML2.XMLHTTP', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP.6.0' ];
			//从高到低检测 IE 支持的 XMLHTTP 对象
			//不含 5.0 跟 4.0 版本，没有测试
			while ( arguments[0] ) {
				if ( trying( function( a ){ new msie( a ); return true; }, XMLHttpRequest = arguments.pop() ) ) {
					init = function() {
						return new msie( XMLHttpRequest );
					};
					break;
				};
			};
		} else {
			eh( 'Not compatible with xmlhttp function' );
		};
	};
	//检测修正
	function detect( a, s ) {
		!$.is_formdata( s ) && this.type === type[1] && a.setRequestHeader( 'content-type', 'application/x-www-form-urlencoded' );
	};
	//引用回调函数
	function callback() {
		if ( this.wait ) {
			this.readyState = this.xmlhttp.readyState;
			if ( this.readyState === 4 ) {
				this.wait = false,
				this.status = this.xmlhttp.status,
				this.statusText = this.xmlhttp.statusText;
				//this.responseXML = this.xmlhttp.responseXML,
				//this.responseText = this.xmlhttp.responseText;
			};
			this.onreadystatechange && this.onreadystatechange.call( this, this.readyState );
		};
	};
	//本地 XMLHTTP 对象
	function xmlhttp() {
		this.xmlhttp = init();
	};
	//扩展本地 XMLHTTP 原型
	xmlhttp.prototype = {
		readyState : 0,
		status : 0,
		statusText : '',
		abort : function() {
			if ( this.wait && this.readyState < 4 ) {
				this.wait = false,
				this.readyState = 0,
				this.xmlhttp.abort();
			};
			return this;
		},
		getAllResponseHeaders : function() {
			return this.readyState === 4 ? this.xmlhttp.getAllResponseHeaders() : null;
		},
		getResponseHeader : function( a ) {
			return this.readyState === 4 ? this.xmlhttp.getResponseHeader( a ) : null;
		},
		setRequestHeader : function( a, s ) {
			this.hasOwnProperty( 'headers' ) || void( this.headers = {} );
			this.headers[ a ] = s;
			return this;
		},
		result : function( a ) {
			if ( this.readyState === 4 ) {
				if ( this.type === type[0] || type[0] === ( a = ( $.is_string( a ) ? a : this.getResponseHeader( 'content-type' ) ).toLowerCase() ) ) {
					return this.getAllResponseHeaders();
				};
				switch ( true ) {
					case mime.xml.test( a ) : return this.xmlhttp.responseXML;
					case mime.json.test( a ) : return $.json_decode( this.xmlhttp.responseText );
					case mime.jscript.test( a ) : return $.setscript( this.xmlhttp.responseText );
					default : return this.xmlhttp.responseText;
				};
			};
			return null;
		},
		open : function( a, s, d, f, g ) {
			this.type = a,
			this.url = s,
			this.async = d;
			if ( arguments.length > 3 ) {
				this.username = f, this.password = g;
			} else {
				delete this.username, delete this.password;
			};
			return this;
		},
		send : function( a ) {
			//判断配置是否正确
			if ( this.wait || inArr.call( type, this.type = String( this.type ).toLowerCase() ) === -1 || !$.is_string( this.url ) ) {
				return this;
			};
			//初始化状态
			this.readyState = 0,
			this.status = 0,
			this.statusText = '',
			//判断是否使用 basic 认证登录
			this.hasOwnProperty( 'username' ) ?
				this.xmlhttp.open( this.type, this.url, this.async, this.username, this.password ) :
				this.xmlhttp.open( this.type, this.url, this.async ),
			//检测调式
			detect.call( this, this.xmlhttp, a );
			//设置头信息
			for ( arguments in this.headers ) {
				this.xmlhttp.setRequestHeader( arguments, this.headers[ arguments ] );
			};
			//判断是否异步请求，防止一些浏览器上的回调差异
			if ( this.async ) {
				//创建并且根据响应回调
				this.xmlhttp.onreadystatechange = $.scope( this, callback ),
				//之后发送请求
				this.xmlhttp.send( a ),
				//等待状态
				this.wait = true;
			} else {
				//等待完成
				this.wait = true,
				//预先发送请求
				this.xmlhttp.send( a ),
				//调用一次回调在发送之后
				callback.call( this );
			};
			//设置 0 等待服务器超时
			this.timeout && setTimeout( $.scope( this, this.abort ), this.timeout );
			return this;
		}
	};
	//外部调用 XMLHTTP 函数
	return function() {
		return arguments.length ? xmlhttp.prototype.open.apply( new xmlhttp, arguments ) : new xmlhttp;
	};
}(),
//扩展 AJAX 功能
$.ajax = function(){
	var
	//默认 AJAX 请求地址
	url = location.href,
	//判断错误代码，实际上 4 和 5 开头的状态码才属于错误
	errors = /^[45]/;
	//将对象序列化提交参数
	function param( a ) {
		var s = '', d;
		for ( d in a ) {
			s += '&' + $.urlencode( d ) + '=' + $.urlencode( a[ d ] );
		};
		return s.substring(1);
	};
	//初始化 AJAX 数据
	function init( a ) {
		//取得配置数据
		this.type = ajax.type,
		this.url = a ? a : ajax.url,
		this.async = ajax.async,
		this.loading = ajax.loading,
		this.sent = ajax.sent,
		this.timeout = ajax.timeout;
		//覆盖配置数据
		for ( arguments in a ) {
			this[ arguments ] = a[ arguments ];
		};
		//修正配置数据
		$.is_string( this.type ) || void( this.type = 'get' ),
		$.is_string( this.url ) || void( this.url = url ),
		$.is_bool( this.async ) || void( this.async = true ),
		$.is_function( this.loading ) || void( this.loading = noop ),
		$.is_string( this.sent ) || void( this.sent = $.is_plain_object( this.sent ) ? param( this.sent ) : null ),
		this.timeout |= this.timeout;
		return this;
	};
	//本地回调函数
	function callback( a ) {
		this.loading.call( this, a );
		if ( a === 4 ) {
			errors.test( this.status )
				? this.err( this, this.xmlhttp.responseText )
				: this.end( this, this.xmlhttp.responseText );
		};
	};
	function ajax( a ) {
		a = init.call( $.defer( $.xmlhttp() ), a ),
		a.onreadystatechange = callback,
		a.send( a.sent );
		return a;
	};
	//返回，并且将初始化数据覆盖在 AJAX 函数上
	return init.call( ajax );
}(),
//部署推迟对象到核心函数上
deferred.call( $ ),
//事件绑定
function(){
	var attach, detach,
	//节点索引对象
	nodes = [],
	//调用事件对象
	calls = [],
	//点燃触发对象
	fires = [];
	//事件创建函数
	function event( a, s ) {
		calls[ a ][ s ] = [],
		fires[ a ][ s ] = function( d ) {
			trigger.call( calls[ a ][ s ], nodes[ a ], d || window.event );
		};
	};
	//触发调用函数
	function trigger( a, s ) {
		for ( arguments = 0; arguments < this.length; arguments++ ) {
			this[ arguments ].call( a, s );
		};
	};
	//本地绑定对象
	function bind( a ) {
		for ( arguments = 0; arguments < a.length; arguments++ ) {
			$.is_function( a[ arguments ] ) && this.push( a[ arguments ] );
		};
	};
	//本地解除绑定
	function unbind( a ) {
		for ( var s = 0, d = a.length, f; s < d; s++ ) {
			if ( $.is_function( a[ s ] ) ) {
				f = inArr.call( this, a[ s ] ),
				f === -1 || this.splice( f, 1 );
			};
		};
	};
	$.bind = function( a, s, d ) {
		arguments = inArr.call( nodes, a );
		if ( arguments === - 1 ) {
			arguments = nodes.length,
			fires[ arguments ] = {},
			calls[ arguments ] = {},
			nodes[ arguments ] = a;
		};
		if ( $.is_plain_object( s ) ) {
			for ( d in s ) {
				hasOwn.call( calls[ arguments ], d ) || event( arguments, d );
				if ( $.is_array( s[ d ] ) ) {
					bind.call( calls[ arguments ][ d ], s[ d ] );
				} else {
					$.is_function( s[ d ] ) && calls[ arguments ][ d ].push( s[ d ] );
				};
				calls[ arguments ][ d ].length && attach( a, d, fires[ arguments ][ d ] );
			};
			return;
		};
		hasOwn.call( calls[ arguments ], s ) || event( arguments, s );
		if ( $.is_array( d ) ) {
			bind.call( calls[ arguments ][ s ], d );
		} else {
			$.is_function( d ) && calls[ arguments ][ s ].push( d );
		};
		calls[ arguments ][ s ].length && attach( a, s, fires[ arguments ][ s ] );
	},
	$.unbind = function( a, s, d ) {
		a = inArr.call( nodes, a );
		if ( a === -1 ) {
			return;
		};
		if ( $.is_plain_object( s ) ) {
			for ( d in s ) {
				if ( calls[ a ][ d ] ) {
					if ( $.is_array( s[ d ] ) ) {
						unbind.call( calls[ a ][ d ], s[ d ] );
					} else {
						arguments = inArr.call( calls[ a ][ d ], s[ d ] ),
						arguments === -1 || calls[ a ][ d ].splice( arguments, 1 );
					};
					calls[ a ][ d ].length || detach( nodes[ a ], d, fires[ a ][ d ] );
				};
			};
			return;
		};
		if ( calls[ a ][ s ] ) {
			if ( $.is_array( d ) ) {
				unbind.call( calls[ a ][ s ], d );
			} else {
				if ( $.is_function( d ) ) {
					d = inArr.call( calls[ a ][ s ], d ),
					d === -1 || calls[ a ][ s ].splice( d, 1 );
				} else {
					calls[ a ][ s ].length = 0;
				};
			};
			calls[ a ][ s ].length || detach( nodes[ a ], s, fires[ a ][ s ] );
			return;
		};
		for ( s in calls[ a ] ) {
			calls[ a ][ s ].length = 0,
			detach( nodes[ a ], s, fires[ a ][ s ] );
		};
	};
	if ( document.addEventListener ) {
		attach = function( a, s, d, f ) {
			a.addEventListener( s, d, !!f );
		},
		detach = function( a, s, d, f ) {
			a.removeEventListener( s, d, !!f );
		},
		document.addEventListener( 'DOMContentLoaded', function() {
			document.removeEventListener( 'DOMContentLoaded', arguments.callee, false ), $.end( $ );
		}, false );
		return;
	};
	if ( document.attachEvent ) {
		attach = function( a, s, d ) {
			a.attachEvent( 'on' + s, d );
		},
		detach = function( a, s, d ) {
			a.detachEvent( 'on' + s, d );
		},
		document.attachEvent( 'onreadystatechange', function() {
			if ( document.readyState === 'complete' ) {
				document.detachEvent( 'onreadystatechange', arguments.callee ), $.end( $ );
			};
		} );
		return;
	};
	//当浏览器不支持以上 2 种事件时选择以下方式是实现
	attach = function( a, s, d ) {
		a[ 'on' + s ] = d;
	},
	detach = function( a, s, d ) {
		if ( a[ 'on' + s ] === d ) {
			a[ 'on' + s ] = null;
		};
	},
	$.onload( $.end );
}(),
//扩展功能
$.extend({
	0 : +$.gettime(0),//时间函数返回差
	1 : +$.gettime(),//函数初始化时间
	go : function( a, s ) {
		a ? s ? window.open( a, s ) : location.replace( a ) : location.reload();
		return false;
	},
	get : function(){
		var id = /^#/;
		return function( a, s ) {
			return id.test( a ) ? ( s || document ).getElementById( a.substring(1) ) : $.query( a, s )[0];
		};
	}(),
	getstyle : function( a ) {
		return window.getComputedStyle ? window.getComputedStyle( a ) : a.currentStyle ? a.currentStyle : a.style;
	},
	getcookie : function( a ) {
		var s = ' ' + document.cookie + ';', d = ' ' + a + '=', f = s.indexOf( d );
		return f === -1 ? '' : $.urldecode( s.substring( f + d.length, s.indexOf( ';', f ) ) );
	},
	delcookie : function( a ) {
		$.setcookie( a, 0, 1 );
	},
	getcookies : function() {
		for ( var a = 0, s = document.cookie + ';', d = {}, f = s.length; a < f; a += 2 ) {
			d[ s.substring( a, a = s.indexOf( '=', a ) ) ] = $.urldecode( s.substring( a + 1, a = s.indexOf( ';', a ) ) );
		};
		return d;
	},
	delcookies : function() {
		for ( arguments in $.getcookies() ) {
			$.delcookie( arguments );
		};
	},
	//外部推迟函数
	defer : function( a ) {
		//需要创建 2 个推迟对象，一个用来当失败回调
		var s = new deferred, d;
		if ( arguments.length ) {
			//如果参数传入对象就将一个完成回调对象部署到该对象
			deferred.call( a ), $.is_function( a ) && a.call( a );
		} else {
			//创建一个新的完成回调对象
			a = new deferred;
		};
		//同样将失败回调对象部署到该对象上
		d = a.cancel, a.fail = s.done, a.err = function() {
			//改写失败状态，防止发生失败继续完成回调
			return d.call( s.end.apply( a, arguments ) );
		}, a.cancel = function() {
			//改写取消状态可以同时让 2 个回调失效
			return d.call( a, s.cancel() );
		};
		return a;
	},
	onload : function( a ) {
		var s;
		if ( $.is_function( window.onload ) ) {
			s = window.onload;
			return window.onload = $.is_function( a ) ?
				function() {
					s.apply( $, arguments ),
					a.apply( $, arguments );
				} : window.onload;
		};
		return window.onload = a;
	},
	//转义正规表达式中敏感的符号
	repesc : function(){
		var code = /[!$()*+,-./:<=?\[\\\]^{|}]/g;
		return function( a ) {
			return String( a ).replace( code, '\\$&' );
		};
	}(),
	//创建一个闭包函数范围
	scope : function( a, s ) {
		return function() {
			return s.apply( a, arguments );
		};
	},
	each : function( a, s, d ) {
		( $.is_array( a ) ? eachArr : eachObj ).call( a, s, d );
		return a;
	},
	some : function( a, s, d ) {
		( $.is_array( a ) ? someArr : someObj ).call( a, s, d );
		return a;
	},
	every : function( a, s, d ) {
		( $.is_array( a ) ? everyArr : everyObj ).call( a, s, d );
		return a;
	},
	image : function( a ) {
		arguments = $.defer( new Image ),
		arguments.onabort = arguments.cancel,
		arguments.onerror = arguments.err,
		arguments.onload = arguments.end,
		arguments.src = a;
		return arguments;
	},
	frag : function() {
		return document.createDocumentFragment();
	},
	text : function( a ) {
		return document.createTextNode( a );
	},
	offset_top : function() {
		return window.pageYOffset || document.documentElement.scrollTop || ( document.body ? document.body.scrollTop : 0 );
	},
	offset_left : function() {
		return window.pageXOffset || document.documentElement.scrollLeft || ( document.body ? document.body.scrollLeft : 0 );
	},
	offset_point : function( a ) {
		var s = a.getBoundingClientRect(), d = $.offset_top(), f = $.offset_left();
		return { top : s.top + d, left : s.left + f, right : s.right + f, bottom : s.bottom + d, width : s.width, height : s.height };
	},
	isnode : function( a ) {
		return a && a.nodeType === 1;
	},
	create : function(){
		var tags = /^([a-z]{1,10}|h[1-6])$/i;
		return function( a ) {
			return tags.test( a ) ? document.createElement( a ) : null;
		};
	}(),
	parent : function( a )
	{
		return a && a.parentElement;
	},
	append : function( a, s )
	{
		return $.isnode( s ) ? s.appendChild( a ) : document.body ? document.body.appendChild( a ) : a;
	},
	remove : function()
	{
		function remove( a ) {
			$.unbind( a );
			return a.parentNode.removeChild( a );
		};
		return function( a ) {
			return trying( remove, a );
		};
	}(),
	stopbubble : function( a )
	{
		if ( a && a.stopPropagation ) {
			a.stopPropagation();
		} else {
			window.event.cancelBubble = true;
		};
	},
	stopdefault : function( a )
	{
		if ( a && a.preventDefault ) {
			a.preventDefault();
		} else {
			window.event.returnValue = false;
		};
	},
	iframeout : function( a )
	{
		if ( a.parentNode && a.contentWindow ) {
			//a.src = 'about:blank',
			a.contentWindow.document.write(),
			a.contentWindow.document.close(),
			a.parentNode.removeChild( a );
		};
	},
	uid : function()
	{
		return $.str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
	},
	fn : function( a ) {
		return extend.call( ctrl.prototype, a, true );
	}
}),

ctrl = push.apply( {}, [0] ) ?
function ctrl( a ) {
	if ( $.is_array( a ) ) {
		push.apply( this, a ), this.length = a.length;
	} else {
		this[0] = a, this.length = 1;
	};
} : function ctrl( a ) {
	if ( $.is_array( a ) ) {
		eachArr.call( a, ctrl.copy, this ), this.length = a.length;
	} else {
		this[0] = a, this.length = 1;
	};
},
ctrl.copy = function( a, s ) {
	this[ s ] = a;
},
ctrl.events = function( a, s, d ) {
	for ( var f = 0, g = this.length; f < g; $[ a ]( this[ f++ ], s, d ) );
	return this;
},
$.fn({
	find : function( a ) {
		return $( a, this[0] );
	},
	each : function( a ) {
		for ( var s = 0, d = this.length; s < d; a.call( this[ s++ ], this, s ) );
		return this;
	},
	unbind : function( a, s ) {
		return ctrl.events.call( this, 'unbind', a, s );
	},
	bind : function( a, s ) {
		return ctrl.events.call( this, 'bind', a, s );
	},
	click : function( a ) {
		return this.bind( 'click', a );
	}
}),
window.$ = $;
}(this));
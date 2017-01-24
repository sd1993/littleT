/*
	���ܼ���һЩ PHP �������򵥵� DOM ��ѯ�������� DOM ����
	�Խű����ڵ������뷢�����ʼ���zeroneta@qq.com
	�����ļ���ȡϵͳ(NFS,NFAS,WA) 2003,2012,2013 ��
	����޸�ʱ�� 20150408
*/
(function( window, undefined ){
var
	//��ǰ�����������õ���ȫ�ֶ��󶼻���������
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

	//�Զ������õĺ���
	unicode = String.fromCharCode,
	msie = window.ActiveXObject,

	//����ԭ��
	hasOwn = Object.prototype.hasOwnProperty,
	toStr = Object.prototype.toString,
	//�������� indexOf ����ԭ��
	inArr = Array.prototype.indexOf || function( a, s ) {
		for ( var d = s || 0, f = this.length; d < f; d++ ) {
			if ( this[ d ] === a ) {
				return d;
			};
		};
		return -1;
	},
	//�������� forEach ����ԭ��
	eachArr = Array.prototype.forEach || function( a, s ) {
		for ( var d = 0, f = s || window, g = this.length; d < g; d++ ) {
			a.call( f, this[ d ], d, this );
		};
	},
	//�������� every ����ԭ��
	everyArr = Array.prototype.every || function( a, s ) {
		for ( var d = 0, f = s || window, g = this.length; d < g; d++ ) {
			if ( a.call( f, this[ d ], d, this ) ) {
				continue;
			};
			return false;
		};
		return true;
	},
	//�������� some ����ԭ��
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

	//�Զ������
	type = {}, errors = [], cacheIndex = [], cacheObject = [], base64code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/==', ctrl;

function noop(){};

//��ȡ����������
function gettype( a ) {
	return type[ toStr.call( a ) ] || 'object';
};

//������������Ϊ������Ϣ
function eh( a ) {
	errors.push( gettype( a ) === 'error' ? [ a.name, a.message, a.stack || a.number & 65535 ] : slice.call( arguments ) );
};

//��������һ������
function trying( a ) {
	try {
		return a.apply( this, slice.call( arguments, 1 ) );
	} catch ( s ) {
		eh( s );
	};
};

//��չ����
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

//ȥ���ַ�����β���Ŀհ��ַ������������ַ���
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

//�Ƴٹ��캯��
function deferred() {
	//���汾�ػص�����
	var callbacks = [], alive = true, end = false;
	//ȡ���ص�
	this.cancel = function() {
		alive = false;
		return this;
	},
	//�ص�����
	this.done = function( a ) {
		if ( alive && gettype( a ) === 'function' ) {
			end ? a.apply( end, alive ) : callbacks.push( a );
		};
		return this;
	},
	//�����ص�
	this.end = function( a ) {
		if ( a !== end ) {
			alive = slice.call( arguments, 1 ), end = a || this;
			while ( alive && callbacks[0] ) {
				callbacks.shift().apply( end, alive );
			};
		};
		return this;
	};
	//���ر����õ�������
	return this;
};

//���ĺ����������
function $( a, s ) {
	return $.is_function( a ) ? $.done( a ) : new ctrl( $.is_string( a ) ? $.query( a, s ) : a );
};
/*
//��һ�����µİ汾����жϲ��ҷ���
if ( trying(function(){ return ( $.D = 1.0814 ) <= window.$.D; }) ) {
	return;
};
*/
//����������
eachArr.call( 'Array Boolean Date Error FormData Function Number Object RegExp String'.split( ' ' ), function( a ){
	this[ '[object ' + a + ']' ] = a.toLowerCase();
}, type ),

$[0] = 0,//��̨������1970���ʱ��ȡ�õ�ǰʱ�佫�����ʼ��ʱ�䷵�ؽ��
$[1] = 1,//Ŀǰʱ�仹�����ܻص���ȥ��ȡ��ָ��ʱ�佫��ȥ��ǰʱ���ؽ��

//PHP math
eachArr.call( 'E LN2 LN10 LOG2E LOG10E PI SQRT1_2 SQRT2 abs acos asin atan atan2 ceil cos exp floor lcg_value log max min pow round sin sqrt tan'.split( ' ' ), function( a ){
	this[ a ] = window.Math[ a ] || window.Math.random;
}, $ ),

extend.call( $, {
//PHP var
	gettype : function( a ) {
		//��Ч���� undefined ���� null ����ȫ��
		return $.is_empty( a ) ? String( a ) : gettype( a );
	},
	strval : function( a ) {
		//���� false ���ͷ��� 0
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
	//�����ж�����1.0������С������JS��1.0���Զ�����Ϊ1���Բ�ͨ���ж�
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
	//�����ж��������޵�����
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
		//���� 7 ��ʱ��֧��
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
		//��
		d : function() {
			//�·��еĵڼ��죬��ǰ����� 2 λ����
			return pad( this.j(), 2 );
		},
		D : function() {
			//�����еĵڼ��죬�ı���ʾ��3 ����ĸ
			return this.l().substring( 0, 3 );
		},
		j : function() {
			//�·��еĵڼ��죬û��ǰ����
			return date.getDate();
		},
		l : function() {
			//���ڼ����������ı���ʽ
			return words[ this.w() ] + 'day';
		},
		N : function() {
			//ISO-8601 ��ʽ���ֱ�ʾ�������еĵڼ���
			return this.w() || 7;
		},
		S : function() {
			//ÿ�����������Ӣ�ĺ�׺��2 ���ַ�
			arguments = this.j();
			return arguments > 4 && arguments < 21 ? 'th' : postfix[ arguments % 10 ] || 'th';
		},
		w : function() {
			//�����еĵڼ��죬���ֱ�ʾ
			return date.getDay();
		},
		z : function() {
			//����еĵڼ���
			arguments = this.Y();
			return ( Date.UTC( arguments, this.n() - 1, this.j() ) - Date.UTC( arguments, 0, 1 ) ) / 864e5;
		},
		//����
		W : function() {
			//ISO-8601 ��ʽ����еĵڼ��ܣ�ÿ�ܴ�����һ��ʼ
			arguments = this.Y();
			return pad( $.round( ( Date.UTC( arguments, this.n() - 1, this.j() - this.N() + 3 ) - Date.UTC( arguments, 0, 4 ) ) / 864e5 / 7 ) + 1, 2 );
		},
		//��
		F : function() {
			//�·ݣ��������ı���ʽ������ January ���� December
			return words[ this.n() + 6 ];
		},
		m : function() {
			//���ֱ�ʾ���·ݣ���ǰ����
			return pad( this.n(), 2 );
		},
		M : function() {
			//������ĸ��д��ʾ���·�
			return this.F().substring( 0, 3 );
		},
		n : function() {
			//���ֱ�ʾ���·ݣ�û��ǰ����
			return date.getMonth() + 1;
		},
		t : function() {
			//�����·���Ӧ�е�����
			return new Date( this.Y(), this.n(), 0 ).getDate();
		},
		//��
		L : function() {
			//�Ƿ�Ϊ���꣬���������Ϊ 1������Ϊ 0
			return new Date( this.Y(), 1, 29 ).getMonth() === 1 | 0;
		},
		o : function() {
			//ISO-8601 ��ʽ������֣���� Y ��ֵ��ͬ��ֻ������� ISO ����������W������ǰһ�����һ�꣬������һ��
			var a = this.n(), s = this.W();
			return this.Y() + ( a === 12 && s < 9 ? -1 : a === 1 && s > 9 );
		},
		y : function() {
			//2 λ���ֱ�ʾ�����
			return this.Y().toString().slice(-2);
		},
		Y : function() {
			//4 λ����������ʾ�����
			return date.getFullYear();
		},
		//ʱ��
		a : function() {
			//Сд�����������ֵ
			return date.getHours() > 11 ? 'pm' : 'am';
		},
		A : function() {
			//��д�����������ֵ
			return this.a().toUpperCase();
		},
		B : function() {
			//Swatch Internet ��׼ʱ
			return pad( $.floor( (
				date.getUTCHours() * 36e2 +
				date.getUTCMinutes() * 60 +
				date.getUTCSeconds() + 36e2 ) / 86.4 ) % 1e3, 3 );
		},
		g : function() {
			//Сʱ��12 Сʱ��ʽ��û��ǰ����
			return this.G() % 12 || 12;
		},
		G : function() {
			//Сʱ��24 Сʱ��ʽ��û��ǰ����
			return date.getHours();
		},
		h : function() {
			//Сʱ��12 Сʱ��ʽ����ǰ����
			return pad( this.g(), 2 );
		},
		H : function() {
			//Сʱ��24 Сʱ��ʽ����ǰ����
			return pad( this.G(), 2 );
		},
		i : function() {
			//���ӣ���ǰ����
			return pad( date.getMinutes(), 2 );
		},
		s : function() {
			//��������ǰ����
			return pad( date.getSeconds(), 2 );
		},
		u : function() {
			//΢�룬��ǰ����
			return pad( date.getMilliseconds() * 1000, 6 );
		},
		//ʱ��
		e : function() {
			//ʱ����ʶ�����������������
			return 'UTC';
		},
		I : function() {
			//�Ƿ�Ϊ����ʱ�����������ʱΪ 1������Ϊ 0
			arguments = this.Y();
			return +( new Date( arguments, 0 ) - Date.UTC( arguments, 0 ) !== new Date( arguments, 6 ) - Date.UTC( arguments, 6 ) );
		},
		O : function() {
			//���������ʱ������Сʱ��
			arguments = [ date.getTimezoneOffset() ], arguments[1] = $.abs( arguments[0] );
			return ( arguments[0] > 0 ? '-' : '+' ) + pad( $.floor( arguments[1] / 60 ) * 100 + arguments[1] % 60, 4 );
		},
		P : function() {
			//���������ʱ�䣨GMT���Ĳ��Сʱ�ͷ���֮����ð�ŷָ�
			arguments = this.O();
			return arguments.substring( 0, 3 ) + ':' + arguments.substring( 3, 5 );
		},
		T : function() {
			//�������ڵ�ʱ�������������������
			return 'UTC';
		},
		Z : function() {
			//ʱ��ƫ������������UTC ���ߵ�ʱ��ƫ�������Ǹ��ģ�UTC ���ߵ�ʱ��ƫ������������
			return -date.getTimezoneOffset() * 60;
		},
		//���������ڻ�ʱ��
		c : function() {
			//ISO 8601 ��ʽ�����ڣ����磺2004-02-12T15:19:21+00:00
			return 'Y-m-d\\Th:i:sP'.replace( chars, callback );
		},
		r : function() {
			//RFC 822 ��ʽ�����ڣ����磺Thu, 21 Dec 2000 16:01:07 +0200
			return 'D, d M Y H:i:s O'.replace( chars, callback );
		},
		U : function() {
			//�� Unix ��Ԫ��January 1 1970 00:00:00 GMT����ʼ���������
			return date / 1000 | 0;
		} }, date;
		//����ǰ���㺯��
		function pad( a, s ) {
			return ( '000000' + a ).slice( -s );
		};
		//���ö���
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
		//���� 7 ��ʱ��֧��
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
	ready = true,//������������
	regexp = RegExp,
	func = Function;

	//ת�����麯��
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

	//�ж��Ƿ�ʹ��ԭ��ѡ����
	if ( $.is_function( test.querySelectorAll ) ) {
		if ( ready ) {
			//д���������
			test.innerHTML = '<a/>';
			//�����������ǵ������ڵ㣬ѡ����Ҫ���ӽڵ㿪ʼƥ��
			if ( test.querySelectorAll( 'div a' ).length === 0 ) {
				selector = function( a, s ) {
					return s.querySelectorAll( a );
				};
			};
			//�ж��Ƿ񷵻�����
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
//ģ�� XMLHTTP ����
$.xmlhttp = function(){
	var XMLHttpRequest = window.XMLHttpRequest,
	//֧�� XMLHTTP ���������
	type = [ 'head', 'post', 'get' ],
	mime = {
		xml : /xml$/,
		json : /json$/,
		jscript : /^js$|javascript$/
	}, init;
	//���� XMLHTTP ����
	if ( XMLHttpRequest ) {
		init = function() {
			return new XMLHttpRequest;
		};
	} else {
		if ( msie ) {
			arguments = [ 'Microsoft.XMLHTTP', 'MSXML2.XMLHTTP', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP.6.0' ];
			//�Ӹߵ��ͼ�� IE ֧�ֵ� XMLHTTP ����
			//���� 5.0 �� 4.0 �汾��û�в���
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
	//�������
	function detect( a, s ) {
		!$.is_formdata( s ) && this.type === type[1] && a.setRequestHeader( 'content-type', 'application/x-www-form-urlencoded' );
	};
	//���ûص�����
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
	//���� XMLHTTP ����
	function xmlhttp() {
		this.xmlhttp = init();
	};
	//��չ���� XMLHTTP ԭ��
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
			//�ж������Ƿ���ȷ
			if ( this.wait || inArr.call( type, this.type = String( this.type ).toLowerCase() ) === -1 || !$.is_string( this.url ) ) {
				return this;
			};
			//��ʼ��״̬
			this.readyState = 0,
			this.status = 0,
			this.statusText = '',
			//�ж��Ƿ�ʹ�� basic ��֤��¼
			this.hasOwnProperty( 'username' ) ?
				this.xmlhttp.open( this.type, this.url, this.async, this.username, this.password ) :
				this.xmlhttp.open( this.type, this.url, this.async ),
			//����ʽ
			detect.call( this, this.xmlhttp, a );
			//����ͷ��Ϣ
			for ( arguments in this.headers ) {
				this.xmlhttp.setRequestHeader( arguments, this.headers[ arguments ] );
			};
			//�ж��Ƿ��첽���󣬷�ֹһЩ������ϵĻص�����
			if ( this.async ) {
				//�������Ҹ�����Ӧ�ص�
				this.xmlhttp.onreadystatechange = $.scope( this, callback ),
				//֮��������
				this.xmlhttp.send( a ),
				//�ȴ�״̬
				this.wait = true;
			} else {
				//�ȴ����
				this.wait = true,
				//Ԥ�ȷ�������
				this.xmlhttp.send( a ),
				//����һ�λص��ڷ���֮��
				callback.call( this );
			};
			//���� 0 �ȴ���������ʱ
			this.timeout && setTimeout( $.scope( this, this.abort ), this.timeout );
			return this;
		}
	};
	//�ⲿ���� XMLHTTP ����
	return function() {
		return arguments.length ? xmlhttp.prototype.open.apply( new xmlhttp, arguments ) : new xmlhttp;
	};
}(),
//��չ AJAX ����
$.ajax = function(){
	var
	//Ĭ�� AJAX �����ַ
	url = location.href,
	//�жϴ�����룬ʵ���� 4 �� 5 ��ͷ��״̬������ڴ���
	errors = /^[45]/;
	//���������л��ύ����
	function param( a ) {
		var s = '', d;
		for ( d in a ) {
			s += '&' + $.urlencode( d ) + '=' + $.urlencode( a[ d ] );
		};
		return s.substring(1);
	};
	//��ʼ�� AJAX ����
	function init( a ) {
		//ȡ����������
		this.type = ajax.type,
		this.url = a ? a : ajax.url,
		this.async = ajax.async,
		this.loading = ajax.loading,
		this.sent = ajax.sent,
		this.timeout = ajax.timeout;
		//������������
		for ( arguments in a ) {
			this[ arguments ] = a[ arguments ];
		};
		//������������
		$.is_string( this.type ) || void( this.type = 'get' ),
		$.is_string( this.url ) || void( this.url = url ),
		$.is_bool( this.async ) || void( this.async = true ),
		$.is_function( this.loading ) || void( this.loading = noop ),
		$.is_string( this.sent ) || void( this.sent = $.is_plain_object( this.sent ) ? param( this.sent ) : null ),
		this.timeout |= this.timeout;
		return this;
	};
	//���ػص�����
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
	//���أ����ҽ���ʼ�����ݸ����� AJAX ������
	return init.call( ajax );
}(),
//�����Ƴٶ��󵽺��ĺ�����
deferred.call( $ ),
//�¼���
function(){
	var attach, detach,
	//�ڵ���������
	nodes = [],
	//�����¼�����
	calls = [],
	//��ȼ��������
	fires = [];
	//�¼���������
	function event( a, s ) {
		calls[ a ][ s ] = [],
		fires[ a ][ s ] = function( d ) {
			trigger.call( calls[ a ][ s ], nodes[ a ], d || window.event );
		};
	};
	//�������ú���
	function trigger( a, s ) {
		for ( arguments = 0; arguments < this.length; arguments++ ) {
			this[ arguments ].call( a, s );
		};
	};
	//���ذ󶨶���
	function bind( a ) {
		for ( arguments = 0; arguments < a.length; arguments++ ) {
			$.is_function( a[ arguments ] ) && this.push( a[ arguments ] );
		};
	};
	//���ؽ����
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
	//���������֧������ 2 ���¼�ʱѡ�����·�ʽ��ʵ��
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
//��չ����
$.extend({
	0 : +$.gettime(0),//ʱ�亯�����ز�
	1 : +$.gettime(),//������ʼ��ʱ��
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
	//�ⲿ�Ƴٺ���
	defer : function( a ) {
		//��Ҫ���� 2 ���Ƴٶ���һ��������ʧ�ܻص�
		var s = new deferred, d;
		if ( arguments.length ) {
			//��������������ͽ�һ����ɻص������𵽸ö���
			deferred.call( a ), $.is_function( a ) && a.call( a );
		} else {
			//����һ���µ���ɻص�����
			a = new deferred;
		};
		//ͬ����ʧ�ܻص������𵽸ö�����
		d = a.cancel, a.fail = s.done, a.err = function() {
			//��дʧ��״̬����ֹ����ʧ�ܼ�����ɻص�
			return d.call( s.end.apply( a, arguments ) );
		}, a.cancel = function() {
			//��дȡ��״̬����ͬʱ�� 2 ���ص�ʧЧ
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
	//ת��������ʽ�����еķ���
	repesc : function(){
		var code = /[!$()*+,-./:<=?\[\\\]^{|}]/g;
		return function( a ) {
			return String( a ).replace( code, '\\$&' );
		};
	}(),
	//����һ���հ�������Χ
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
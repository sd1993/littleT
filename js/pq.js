//Core by ZERONETA
//不要压缩先观察一段
;(function( global, undefined )
{
	var
	//object
	Array = global.Array,
	Date = global.Date,
	Boolean = global.Boolean,
	Function = global.Function,
	Number = global.Number,
	Object = global.Object,
	RegExp = global.RegExp,
	String = global.String,
	//function
	clearInterval = global.clearInterval,
	clearTimeout = global.clearTimeout,
	isNaN = global.isNaN,
	noop = function(){},
	parseFloat = global.parseFloat,
	parseInt = global.parseInt,
	setInterval = global.setInterval,
	setTimeout = global.setTimeout,
	unicode = String.fromCharCode,
	//call
	_push = Array.prototype.push,
	_slice = Array.prototype.slice,
	_inArr = Array.prototype.indexOf || function( q, w )
	{
		for ( var e = w | 0, r = this.length; e < r; ++e )
		{
			if ( this[ e ] === q )
			{
				return e;
			};
		};
		return -1;
	},
	_eachArr = Array.prototype.forEach || function( q, w )
	{
		for ( var e = w || global, r = 0, t = this.length; r < t; ++r )
		{
			q.call( e, this[ r ], r, this );
		};
	},
	_everyArr = Array.prototype.every || function( q, w )
	{
		for ( var e = w || global, r = 0, t = this.length; r < t; ++r )
		{
			if ( q.call( e, this[ r ], r, this ) )
			{
				continue;
			};
			return false;
		};
		return true;
	},
	_someArr = Array.prototype.some || function( q, w )
	{
		for ( var e = w || global, r = 0, t = this.length; r < t; ++r )
		{
			if ( q.call( e, this[ r ], r, this ) )
			{
				return true;
			};
		};
		return false;
	},
	_bind = Function.bind || function( q )
	{
		var w = this, e = _slice.call( arguments, 1 );
		return function()
		{
			return w.apply( q, e.concat( _slice.call( arguments ) ) );
		};
	},
	_hasOwn = Object.prototype.hasOwnProperty,
	_toStr = Object.prototype.toString,
	_eachObj = Object.prototype.forEach || function( q, w )
	{
		var e = w || global;
		for ( w in this )
		{
			q.call( e, this[ w ], w, this );
		};
	},
	_everyObj = Object.prototype.every || function( q, w )
	{
		var e = w || global;
		for ( w in this )
		{
			if ( q.call( e, this[ w ], w, this ) )
			{
				continue;
			};
			return false;
		};
		return true;
	},
	_someObj = Object.prototype.some || function( q, w )
	{
		var e = w || global;
		for ( w in this )
		{
			if ( q.call( e, this[ w ], w, this ) )
			{
				return true;
			};
		};
		return false;
	},
	_ltrim = String.prototype.ltrim || ltrim,
	_rtrim = String.prototype.rtrim || rtrim,
	_trim = String.prototype.trim || trim,
	//vars
	null_chars = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000',
	type = {},
	errors = [],
	base64code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/==',
	timecode = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwx',
	cacheIndex = [],
	cacheObject = [];

_eachArr.call( 'Arguments Array Boolean Date Error Function Null Number Object RegExp String Undefined'.split( ' ' ), function( q )
{
	type[ '[object ' + q + ']' ] = q.toLowerCase();
}),
_eachArr.call( 'E LN2 LN10 LOG2E LOG10E PI SQRT1_2 SQRT2 abs acos asin atan atan2 ceil cos exp floor lcg_value log max min pow round sin sqrt tan'.split( ' ' ), function( q )
{
	$[ q ] = global.Math[ q ] || global.Math.random;
});

function $()
{
	return $.fn.apply( $, arguments );
};

function gettime()
{
	var q = new Date;
	arguments.length && q.setTime( arguments.length === 1
	 	? gettype( arguments[0] ) === 'number' ? arguments[0] * 1000 : Date.parse( arguments[0] )
		: Date.UTC.apply( null, arguments ) + q.getTimezoneOffset() * 60000 );
	return q;
};

function ltrim( q )
{
	for ( var w = q === undefined ? null_chars : q, e = 0, r = this.length; e < r; ++e )
	{
		if ( w.indexOf( this.charAt( e ) ) === -1 )
		{
			return this.substr( e );
		};
	};
	return '';
};

function rtrim( q )
{
	for ( var w = q === undefined ? null_chars : q, e = this.length; e--; )
	{
		if ( w.indexOf( this.charAt( e ) ) === -1 )
		{
			return this.substr( 0, e + 1 );
		};
	};
	return '';
};

function trim( q )
{
	return lrtim.call( rtrim.call( this, q ), q );
};

function gettype( q )
{
	return type[ q = _toStr.call( q ) ] || q;
};

function trying( q )
{
	try
	{
		return q.apply( this, _slice.call( arguments, 1 ) );
	}
	catch ( w )
	{
		errors[ errors.length ] = w.name + ': ' + w.message;
	};
};

function callback()
{
	var callbacks = [];

	return {
		add : function( q )
		{
			if ( $.is_function( q ) )
			{
				callbacks[ callbacks.length ] = q;
			};
			return this;
		},
		run : function()
		{
			for ( var q = 0; q < callbacks.length; callbacks[ q++ ].apply( this, arguments ) );
			return this;
		},
		remove : function( q )
		{
			callbacks[ q = _inArr.call( callbacks, q ) ] && callbacks.splice( q, 1 );
			return this;
		}
	};
};

function deferred()
{
	var callbacks = callback(), alive = true, end;

	this.cancel = function()
	{
		alive = false;
		return this;
	},
	this.done = function( q )
	{
		if ( alive )
		{
			end === undefined ? callbacks.add( q ) : gettype( q ) === 'function' && q.apply( end, alive );
		};
		return this;
	},
	this.end = function()
	{
		alive && end === undefined && callbacks.run.apply( end = this, alive = _slice.call( arguments ) );
		return this;
	};
	return this;
};
//PHP var
	$.floatval = function( q ) {
		return parseFloat( q ) || 0;
	},
	$.intval = function( q )
	{
		return parseInt( q ) || 0;
	},
	$.is_array = function( q )
	{
		return gettype( q ) === 'array';
	},
	$.is_bool = function( q )
	{
		return typeof q === 'boolean';
	},
	$.is_empty = function( q )
	{
		return q == null;
	},
	$.is_empty_object = function( q )
	{
		for ( var w in q )
		{
			return false;
		};
		return true;
	},
	$.is_finite = global.isFinite,
	$.is_float = function( q )
	{
		return +q === q && !!( q % 1 );
	},
	$.is_function = function( q )
	{
		return gettype( q ) === 'function';
	},
	$.is_int = function( q )
	{
		return q === ~~q;
	},
	$.is_nan = function( q )
	{
		return isNaN( q );
	},
	$.is_null = function( q )
	{
		return q === null;
	},
	$.is_numeric = function( q )
	{
		return typeof q === 'number';
	},
	$.is_object = function( q )
	{
		return gettype( q ) === 'object';
	},
	$.is_plain_object = function( q )
	{
		var w;
		if ( $.is_object( q ) )
		{
			if ( _hasOwn.call( q, 'constructor' ) )
			{
				w = q.constructor, delete q.constructor;
				if ( q.constructor === Object )
				{
					q.constructor = w;
					return true;
				};
				return false;
			};
			return q.constructor === Object;
		};
		return false;
	},
	$.is_regexp = function( q )
	{
		return gettype( q ) === 'regexp';
	},
	$.is_scalar = function( q )
	{
		return 'booleannumberstring'.indexOf( typeof q ) !== -1;
	},
	$.is_string = function( q )
	{
		return typeof q === 'string';
	},
	$.is_void = function( q )
	{
		return q === undefined;
	},
	$.strval = function( q )
	{
		switch ( typeof q )
		{
			case 'string' : return q;
			case 'number' : return q.toString();
			case 'boolean' : return String( +q );
			default : return $.is_empty( q ) ? '' : $.ucfirst( gettype( q ) );
		};
	},
//PHP xml
	$.utf8_decode = function()
	{
		var coding = /[\x80-\xff]{2,3}/g;
		function decode( q )
		{
			return unicode( q.length === 2
				? ( q.charCodeAt(0) & 31 ) << 6 | q.charCodeAt(1) & 63
				: ( q.charCodeAt(0) & 15 ) << 12 | ( q.charCodeAt(1) & 63 ) << 6 | q.charCodeAt(2) & 63 );
		};
		return function( q )
		{
			return String( q ).replace( coding, decode );
		};
	}(),
	$.utf8_encode = function()
	{
		var coding = /[^\x00-\x7f]/g;
		function encode( q )
		{
			return ( q = q.charCodeAt(0) ) < 2048
				? unicode( q >> 6 | 192, q & 63 | 128 )
				: unicode( q >> 12 | 224, q >> 6 & 63 | 128, q & 63 | 128 );
		};
		return function( q )
		{
			return String( q ).replace( coding, encode );
		};
	}(),
//PHP url
	$.base64_decode = function( q )
	{
		for ( var w = String( q ), e = '', r = 0, t = w.length, y, u, i, o; r < t; )
		{
			y = base64code.indexOf( w.charAt( r++ ) ),
			u = base64code.indexOf( w.charAt( r++ ) ),
			i = base64code.indexOf( w.charAt( r++ ) ),
			o = base64code.indexOf( w.charAt( r++ ) ),
			q = y << 18 | u << 12 | i << 6 | o,
			e += i > 63
				? unicode( q >> 16 & 255 )
				: o > 63
					? unicode( q >> 16 & 255, q >> 8 & 255 )
					: unicode( q >> 16 & 255, q >> 8 & 255, q & 255 );
		};
		return $.utf8_decode( e );
	},
	$.base64_encode = function( q )
	{
		for ( var w = $.utf8_encode( q ), e = '', r = 0, t = w.length; r < t; )
		{
			q = w.charCodeAt( r++ ) << 16 | w.charCodeAt( r++ ) << 8 | w.charCodeAt( r++ ),
			e += base64code.charAt( q >> 18 & 63 ) +
				base64code.charAt( q >> 12 & 63 ) +
				base64code.charAt( q >> 6 & 63 ) +
				base64code.charAt( q & 63 );
		};
		return ( t %= 3 ) ? e.slice( 0, -( t = 3 - t ) ) + base64code.slice( -t ) : e;
	},
	$.http_build_query = function()
	{
		function http_build_query( q, w, e )
		{
			var r;
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
					for ( r in w )
					{
						http_build_query( q + '[' + r + ']', w[ r ], e );
					};
					return;
			};
			e[ e.length ] = $.urlencode( q ) + '=' + w;
		};
		return function( q, w, e )
		{
			var r = [], t;
			w = w === undefined ? '' : String( w );
			for ( t in q )
			{
				http_build_query( $.is_nan( t ) ? t : w + t, q[ t ], r );
			};
			return r.join( e === undefined ? '&' : e );
		};
	}(),
	$.urldecode = function()
	{
		var decodeURIComponent = global.decodeURIComponent, chars = /\+/g;
		return function( q )
		{
			return decodeURIComponent( String( q ).replace( chars, ' ' ) );
		};
	}(),
	$.urlencode = function()
	{
		var encodeURIComponent = global.encodeURIComponent, chars = /%20|[\!'\(\)\*\+\/@~]/g, collates = {
		'%20' : '+',
		'!' : '%21',
		"'" : '%27',
		'(' : '%28',
		')' : '%29',
		'*' : '%2A',
		'+' : '%2B',
		'/' : '%2F',
		'@' : '%40',
		'~' : '%7E' };
		function escape( q )
		{
			return collates[ q ];
		};
		return function( q )
		{
			return encodeURIComponent( q ).replace( chars, escape );
		};
	}(),
//PHP string
	$.addslashes = function()
	{
		var escape = /[\\"']/g, coding = /\u0000/g;
		return function( q )
		{
			return String( q ).replace( escape, '\\$&' ).replace( coding, '\\0' );
		};
	}(),
	$.bin2hex = function( q )
	{
		for ( var w = String( q ), e = '', r = 0, t = w.length; r < t; )
		{
			q = w.charCodeAt( r++ ).toString(16),
			e += q.length === 1 ? '0' + q : q;
		};
		return e;
	},
	$.chr = function( q )
	{
		return unicode( q );
	},
	$.chunk_split = function( q, w, e )
	{
		w = w === undefined ? 76 : w | 0;
		return String( q ).match( RegExp( '.{0,' + w + '}', 'g' ) ).join( e === undefined ? '\r\n' : e );
	},
	$.lcfirst = function( q )
	{
		return ( q = String( q ) ).charAt(0).toLowerCase() + q.substring(1);
	},
	$.ltrim = function( q, w )
	{
		return w === undefined ? _ltrim.call( String( q ) ) : ltrim.call( String( q ), w );
	},
	$.md5 = function()
	{
		var
		key = [ [ 7, 12, 17, 22 ],
				[ 5, 9, 14, 20 ],
				[ 4, 11, 16, 23 ],
				[ 6, 10, 15, 21 ] ],
		app = [ function( q, w, e ){ return q & w | ~q & e; },
				function( q, w, e ){ return q & e | w & ~e; },
				function( q, w, e ){ return q ^ w ^ e; },
				function( q, w, e ){ return w ^ ( q | ~e ); } ],
		get = [ function( q ){ return q; },
				function( q ){ return ( 5 * q + 1 ) % 16; },
				function( q ){ return ( 3 * q + 5 ) % 16; },
				function( q ){ return ( 7 * q ) % 16; } ],
		old = [], map = [];
		function reverse8( q )
		{
			var w = q.length, e = '';
			while ( w )
			{
				e += q.substr( w -= 8, 8 );
			};
			return e;
		};
		function left( q, w )
		{
			return q << w | q >>> 32 - w;
		};
		function bin2()
		{
			for ( var q = '', w = 0; w < 16; ++w )
			{
				q += unicode( arguments[ w >> 2 ] >> w % 4 * 8 & 255 );
			};
			return q;
		};
		while ( map.length < 64 )
		{
			map[ map.length ] = $.abs( $.sin( map.length + 1 ) * $.pow( 2, 32 ) ) | 0;
		};
		return function( q, w )
		{
			var e = '', r = 0, t, y, u, i, o;
			for ( q = $.utf8_encode( q ), t = q.length; r < t; e += ( '0000000' + q.charCodeAt( r++ ).toString(2) ).slice(-8) );
			q = e + 1 + Array( 448 - e.length % 512 ).join(0) + reverse8( Array( 64 - ( q = ( q.length * 8 ).toString(2) ).length + 1 ).join(0) + q );
			for ( e = 0x67452301, r = 0xEFCDAB89, t = 0x98BADCFE, y = 0x10325476, u = 0; u * 512 < q.length; ++u )
			{
				for ( old.splice( 0, 4, e, r, t, y ), i = 0; i < 64; ++i )
				{
					o = i / 16 | 0, old.splice( 0, 4, old[3], old[1] + left( old[0] + app[ o % 4 ]( old[1], old[2], old[3] )
						+ parseInt( reverse8( q.substring( get[ o ]( i ) * 32, get[ o ]( i ) * 32 + 32 ) ), 2 ) + map[ i ],
						key[ o % 4 ][ i % 4 ] ) >>> 0, old[1], old[2] );
				};
				e = old[0] + e >>> 0, r = old[1] + r >>> 0, t = old[2] + t >>> 0, y = old[3] + y >>> 0;
			};
			return w ? bin2( e, r, t, y ) : $.bin2hex( bin2( e, r, t, y ) );
		};
	}(),
	$.number_format = function()
	{
		var number = /^-?\d+(\.\d+)?$/, format = /(\d{3})(?=\d)/g;
		return function( q, w, e, r )
		{
			q = number.exec( q ),
			q = String( Number( q ? q[0] : 0 ).toFixed( w | 0 ) ).split( '.' ),
			r = $.is_string( r ) ? r.substring( 0, 1 ) : ',',
			q[0] = $.strrev( $.strrev( q[0] ).replace( format, '$1' + r ) );
			return q.join( $.is_string( e ) ? e.substring( 0, 1 ) : '.' );
		};
	}(),
	$.ord = function( q )
	{
		return q.charCodeAt(0);
	},
	$.rtrim = function( q, w )
	{
		return w === undefined ? _rtrim.call( String( q ) ) : rtrim.call( String( q ), w );
	},
	$.soundex = function()
	{
		var indexed = { B : 1, F : 1, P : 1, V : 1, C : 2, G : 2, J : 2, K : 2, Q : 2, S : 2, X : 2, Z : 2, D : 3, T : 3, L : 4, M : 5, N : 5, R : 6 }
		return function( q )
		{
			var w = [ 0, 0, 0, 0 ], e, r, t, y, u;
			if ( q )
			{
				for ( q = String( q ).toUpperCase(), e = 0, r = 0; e < 4 && ( t = q.charAt( r++ ) ); )
				{
					if ( y = indexed[ t ] )
					{
						if ( y !== u )
						{
							w[ e++ ] = u = y;
						};
					}
					else
					{
						e += r === 1, u = 0;
					};
				};
				w[0] = q.charAt(0);
			};
			return w.join( '' );
		};
	}(),
	$.str_rot13 = function()
	{
		var chars = /[a-z]/ig;
		function rot13( q )
		{
			return unicode( q.charCodeAt(0) + ( q.toLowerCase() < 'n' ? 13 : -13 ) );
		};
		return function( q )
		{
			return String( q ).replace( chars, rot13 );
		};
	}(),
	$.str_shuffle = function( q )
	{
		for ( var w = String( q ), e = w.length; e; w = w.substring( 0, q = $.lcg_value() * e-- | 0 ) + w.substr( q + 1 ) + w.charAt( q ) );
		return w;
	},
	$.strrev = function( q )
	{
		return String( q ).split( '' ).reverse().join( '' );
	},
	$.strtolower = function( q )
	{
		return String( q ).toLowerCase();
	},
	$.strtoupper = function( q )
	{
		return String( q ).toUpperCase();
	},
	$.trim = function( q, w )
	{
		return w === undefined ? _trim.call( String( q ) ) : trim.call( String( q ), w );
	},
	$.ucfirst = function( q )
	{
		return ( q = String( q ) ).charAt(0).toUpperCase() + q.substring(1);
	},
	$.ucwords = function()
	{
		var chars = /^([a-z\u00e0-\u00fc])|\s+([a-z\u00e0-\u00fc])/g;
		function uppercase( q )
		{
			return q.toUpperCase();
		};
		return function( q )
		{
			return String( q ).replace( chars, uppercase );
		};
	}(),
//PHP pcre
	$.preg_quote = function( q, w )
	{
		return String( q ).replace( RegExp( '[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + ( w === undefined ? '' : w ) + '-]', 'g' ), '\\$&' );
	},
//PHP network
//PHP misc
	$.pack = function()
	{
	},
	$.uniqid = function( q, w )
	{
	},
//PHP math
	$.acosh = function( q )
	{
		return $.log( q + $.sqrt( q * q - 1 ) );
	},
	$.asinh = function( q )
	{
		return $.log( q + $.sqrt( q * q + 1 ) );
	},
	$.atanh = function( q )
	{
		return 0.5 * $.log( ( 1 + q ) / ( 1 - q ) );
	},
	$.base_convert = function( q, w, e )
	{
		return parseInt( q, w ).toString( e );
	},
	$.bindec = function()
	{
		var notbin = /[^01]+/g;
		return function( q )
		{
			return parseInt( String( q ).replace( notbin, '' ), 2 ) | 0;
		};
	}(),
	$.cosh = function( q )
	{
		return ( $.exp( q ) + $.exp( -q ) ) * 0.5;
	},
	$.decbin = function( q )
	{
		return parseInt( q >>> 0, 10 ).toString(2);
	},
	$.dechex = function( q )
	{
		return parseInt( q >>> 0, 10 ).toString(16);
	},
	$.decoct = function( q )
	{
		return parseInt( q >>> 0, 10 ).toString(8);
	},
	$.deg2rad = function( q )
	{
		return q / 180 * $.PI;
	},
	$.hexdec = function()
	{
		var nothex = /[^0-f]+/g;
		return function( q )
		{
			return parseInt( String( q ).replace( nothex, '' ), 16 ) | 0;
		};
	}(),
	$.hypot = function( q, w )
	{
		return $.sqrt( q * q + w * w ) || 0;
	},
	$.log10 = function( q )
	{
		return $.log( q ) / $.LN10;
	},
	$.mt_rand = function( q, w )
	{
		arguments.length < 2 && ( w = arguments.length ? q : 2147483647, q = 0 );
		return ( $.lcg_value() * ( w - q + 1 ) ) + q | 0;
	},
	$.octdec = function()
	{
		var notoct = /[^0-7]+/g;
		return function( q )
		{
			return parseInt( String( q ).replace( notoct, '' ), 8 );
		};
	}(),
	$.pi = function()
	{
		return $.PI;
	},
	$.rad2deg = function( q )
	{
		return q / $.PI * 180;
	},
	$.sinh = function( q )
	{
		return ( $.exp( q ) - $.exp( -q ) ) * 0.5;
	},
	$.tanh = function( q )
	{
		return ( $.exp( q ) - $.exp( -q ) ) / ( $.exp( q ) + $.exp( -q ) );
	},
//PHP json
	$.json_decode = function()
	{
		var pattern = /^\s*(\{|\[).*(\}|\])\s*$/;
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
	$.date = function()
	{
		var
		words = 'Sun Mon Tues Wednes Thurs Fri Satur January February March April May June July August September October November December'.split( ' ' ),
		suffix = 'th st nd rd'.split( ' ' ),
		chars = /\\?([a-z])/ig,
		format = {
			//Day
			d : function(){ return pad( this.j(), 2 ); },//月份中的第几天，有前导零的 2 位数字
			D : function(){ return this.l().substring( 0, 3 ); },//星期中的第几天，文本表示，3 个字母
			j : function(){ return this.$j = dates.getDate(); },//月份中的第几天，没有前导零
			l : function(){ return words[ this.w() ] + 'day'; },//星期几，完整的文本格式
			N : function(){ return this.w() || 7; },//ISO-8601 格式数字表示的星期中的第几天
			S : function(){ return suffix[ this.j() > 4 && this.$j < 21 ? 0 : this.$j % 10 || 0 ]; },//每月天数后面的英文后缀，2 个字符
			w : function(){ return dates.getDay(); },//星期中的第几天，数字表示
			z : function(){ return $.round( Date.UTC( this.Y(), this.n() - 1, this.j() ) - Date.UTC( this.$Y, 0, 1 ) ) / 864e5; },//年份中的第几天
			//Week
			W : function(){ return this.$W = pad( $.round( ( Date.UTC( this.Y(), this.n() - 1, this.j() - this.N() + 3 ) - Date.UTC( this.$Y, 0, 4 ) ) / 864e5 / 7 ) + 1, 2 ); },//ISO-8601 格式年份中的第几周，每周从星期一开始
			//Month
			F : function(){ return words[ this.n() + 6 ]; },//月份，完整的文本格式，例如 January 或者 December
			m : function(){ return pad( this.n(), 2 ); },//数字表示的月份，有前导零
			M : function(){ return this.F().substring( 0, 3 ); },//三个字母缩写表示的月份
			n : function(){ return this.$n = dates.getMonth() + 1; },//数字表示的月份，没有前导零
			t : function(){ return gettime( this.Y(), this.n(), 0 ).getDate(); },//给定月份所应有的天数
			//Year
			L : function(){ return gettime( this.Y(), 1, 29 ).getMonth() === 1 | 0; },//是否为闰年，如果是闰年为 1，否则为 0
			o : function(){ return this.Y() + ( this.n() === 12 && this.W() < 9 ? -1 : this.$n === 1 && this.$W > 9 ); },//ISO-8601 格式年份数字，这和 Y 的值相同，只除了如果 ISO 的星期数（W）属于前一年或下一年，则用那一年
			y : function(){ return this.Y().toString().slice(-2); },//2 位数字表示的年份
			Y : function(){ return this.$Y = dates.getFullYear(); },//4 位数字完整表示的年份
			//Time
			a : function(){ return dates.getHours() > 11 ? 'pm' : 'am'; },//小写的上午和下午值
			A : function(){ return this.a().toUpperCase(); },//大写的上午和下午值
			B : function(){ return pad( $.floor( ( dates.getUTCHours() * 36e2 + dates.getUTCMinutes() * 60 + dates.getUTCSeconds() + 36e2 ) / 86.4 ) % 1e3, 3 ); },//Swatch Internet 标准时
			g : function(){ return this.G() % 12 || 12; },//小时，12 小时格式，没有前导零
			G : function(){ return dates.getHours(); },//小时，24 小时格式，没有前导零
			h : function(){ return pad( this.g(), 2 ); },//小时，12 小时格式，有前导零
			H : function(){ return pad( this.G(), 2 ); },//小时，24 小时格式，有前导零
			i : function(){ return pad( dates.getMinutes(), 2 ); },//分钟，有前导零
			s : function(){ return pad( dates.getSeconds(), 2 ); },//秒数，有前导零
			u : function(){ return pad( dates.getMilliseconds() * 1000, 6 ); },//微秒，有前导零
			//Timezone
			e : function(){ return 'UTC'; },//时区标识，这个函数还不完整
			I : function(){ return 0; },//是否为夏令时，如果是夏令时为 1，否则为 0
			O : function(){ return this.$O = ( ( this.O0 = dates.getTimezoneOffset() ) > 0 ? '-' : '+' ) + pad( $.floor( ( this.O1 = $.abs( this.O0 ) ) / 60 ) * 100 + this.O1 % 60, 4 ); },//与格林威治时间相差的小时数
			P : function(){ return this.O().substring( 0, 3 ) + ':' + this.$O.substring( 3, 5 ); },//与格林威治时间（GMT）的差别，小时和分钟之间有冒号分隔
			T : function(){ return 'UTC'; },//本机所在的时区，这个函数还不完整
			Z : function(){ return -dates.getTimezoneOffset() * 60; },//时差偏移量的秒数，UTC 西边的时区偏移量总是负的，UTC 东边的时区偏移量总是正的
			//Full Date/Time
			c : function(){ return 'Y-m-d\\Th:i:sP'.replace( chars, callback ); },//ISO 8601 格式的日期，例如：2004-02-12T15:19:21+00:00
			r : function(){ return 'D, d M Y H:i:s O'.replace( chars, callback ); },//RFC 822 格式的日期，例如：Thu, 21 Dec 2000 16:01:07 +0200
			U : function(){ return dates * 0.001 | 0; }//从 Unix 纪元（January 1 1970 00:00:00 GMT）开始至今的秒数
		}, dates;

		function pad( q, w )
		{
			return ( '000000' + q ).slice( -w );
		};

		function callback( q, w )
		{
			return format[ q ] ? format[ q ]() : w;
		};

		return function( q, w )
		{
			dates = w === undefined ? gettime() : gettime( $.intval( w ) );
			return String( q ).replace( chars, callback );
		};
	}(),
	$.microtime = function( q )
	{
		var w = gettime() * 0.001;
		return q ? w : ( w - ( w = parseInt( w ) ) ).toFixed(8) + ' ' + w;
	},
	$.mktime = function()
	{
		var q = gettime();
		if ( arguments.length )
		{
			q.setHours.apply( q, arguments ),
			arguments.length > 3 && q.setFullYear(
				arguments.length > 5 ? arguments[5] : q.getFullYear(),
				arguments[3] - 1,
				arguments.length > 4 ? arguments[4] : q.getDate() );
		};
		return $.max( q * 0.001 | 0, 0 );
	},
	$.time = function()
	{
		return parseInt( gettime() * 0.001 );
	},
//PHP array
	$.array_flip = function( q )
	{
		var w = {}, e;
		for ( e in q )
		{
			w[ q[ e ] ] = e;
		};
		return w;
	},
	$.array_reverse = function( q, w )
	{

	},
	$.array_keys = function( q )
	{
		var w = [], e;
		for ( e in q )
		{
			w[ w.length ] = e;
		};
		return w;
	},
	$.array_merge = function()
	{
		var q = {}, w, e;
		for ( w in arguments )
		{
			for ( e in arguments[ w ] )
			{
				q[ e ] = arguments[ w ][ e ];
			};
		};
		return q;
	},
	$.array_unique = function( q )
	{
		var w = [], e;
		for ( e in q )
		{
			if ( _inArr.call( w, q[ e ] ) === -1 )
			{
				w[ w.length ] = q[ e ];
			};
		};
		return w;
	},
	$.array_values = function( q )
	{
		var w = [], e;
		for ( e in q )
		{
			w[ w.length ] = q[ e ];
		};
		return w;
	},
	$.in_array = function( q, w, e )
	{
		if ( e )
		{
			return _inArr.call( w, q ) !== -1;
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
//extend function
	$.gettime = gettime,
	$.gettype = gettype,
	$.errors = errors,
	$.trying = trying,
	$.callback = callback,
	$.deferred = deferred,
	$.noop = noop,
	$.timeout = function( q, w )
	{
		return setTimeout( q, w | 0 );
	},
	$.timeout_remove = clearTimeout,
	$.interval = setInterval,
	$.interval_remove = clearInterval,
	$.entime = function( q )
	{
		q = q === undefined ? gettime() : gettime( $.intval( q ) );
		return q.getFullYear()
			+ timecode.charAt( q.getMonth() + 1 )
			+ timecode.charAt( q.getDate() )
			+ timecode.charAt( q.getHours() )
			+ timecode.charAt( q.getMinutes() )
			+ timecode.charAt( q.getSeconds() )
	},
	$.detime = function( q )
	{
		q = String( q );
		if ( q.length === 9 && ltrim.call( q, timecode ) === '' )
		{
			q = $.mktime(
				timecode.indexOf( q.charAt( 6 ) ),
				timecode.indexOf( q.charAt( 7 ) ),
				timecode.indexOf( q.charAt( 8 ) ),
				timecode.indexOf( q.charAt( 4 ) ),
				timecode.indexOf( q.charAt( 5 ) ),
				q.substr( 0, 4 ) | 0 );
			return q > 0 ? q : 0;
		};
		return 0;
	},
	$.bind = function( q )
	{
		return _bind.apply( q, _slice.call( arguments, 1 ) );
	},
	$.defer = function( q )
	{
		var w = new deferred, e;
		if ( arguments.length )
		{
			deferred.call( q ), $.is_function( q ) && q.call( q );
		}
		else
		{
			q = new deferred;
		};
		e = q.cancel,
		q.cancel = function()
		{
			return e.call( w.cancel.call( q ) );
		},
		q.fail = w.done,
		q.err = function()
		{
			return e.call( w.end.apply( q, arguments ) );
		};
		return q;
	},
	$.cached = function( q )
	{
		var w = _inArr.call( cacheIndex, q );
		if ( w === -1 )
		{
			cacheIndex[ w = cacheIndex.length ] = q,cacheObject[ w ] = {};
		};
		return cacheObject[ w ];
	},
	$.cached_remove = function( q )
	{
		_inArr.call( cacheIndex, q ) === -1 || cacheIndex.splice( q, 1 );
	},
	$.each = function( q, w, e )
	{
		( $.is_array( q ) ? _eachArr : _eachObj ).call( q, w, e );
		return q;
	},
	$.every = function( q, w, e )
	{
		( $.is_array( q ) ? _everyArr : _everyObj ).call( q, w, e );
		return q;
	},
	$.some = function( q, w, e )
	{
		( $.is_array( q ) ? _someArr : _someObj ).call( q, w, e );
		return q;
	},
	$.trims = function()
	{
		var chars = /\s+/g;
		return function( q )
		{
			return String( q ).replace( chars, '' );
		};
	}(),
	$.fn = function( q )
	{
		if ( $.is_function( q = q.call( $, global ) ) )
		{
			$.fn = q;
		};
	},
	global.$ = deferred.call( $ );
}(this.window?window:global));
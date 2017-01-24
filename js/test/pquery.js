$.query = function()
{
	var
		$q = /(\.|#)([\w\u00c0-\uFFFF\-]+)/g,
		$w = /^\s*(\w+)/,
		$e = /^\[\s*(\w+)\s*(?:(CLASS|ID|\!\=|\$\=|\*\=|\=|\^\=|\~\=|\|\=)\s*(\'|\")?([\w\u00c0-\uFFFF\-]+)\3\s*)?\]/,
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
	return $y;
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
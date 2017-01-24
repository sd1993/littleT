(function( $, undefined )
{
	var
		_ = {},
		xmlhttp = $.xmlhttp(),
		menuelem = [],
		runtime = {},
		hidden;

	function call_false()
	{
		return false;
	};

	function bind()
	{
		var q = Array.prototype.slice, w = q.call( arguments );
		return function()
		{
			return w[1].apply( w[0], w.slice(2).concat( q.call( arguments ) ) );
		};
	}

	function ajax( q, w, e, r )
	{
		xmlhttp.abort(),
		xmlhttp.open( q, w, true ),
		xmlhttp.onreadystatechange = $.is_function( e ) ? function()
		{
			this.readyState == 4 && e.call( this, this.$.responseText );
		} : null,
		xmlhttp.$.upload.onprogress = $.is_function( r ) ? r : null;
		return xmlhttp;
	}

	window.onmousedown = function( q )
	{
		if ( $.is_element( hidden ) && hidden !== q )
		{
			hidden.style.display = 'none';
		};
	};

	_.menu_act = function( q )
	{
		var w = $.get( '#' + q.dataset.target ), e, r;
		if ( w )
		{
			w.onmousedown = function()
			{
				$.bubble_stop( w );
			};
			w.onmouseup = function()
			{
				w.style.display = 'none';
			};
			q.onmousedown = function()
			{
				$.bubble_stop( q );
				window.onmousedown( w );
				if ( $.get_style( w ).display === 'none' )
				{
					w.style.visibility = 'hidden',
					w.style.display = 'block',
					e = $.get_offset_point( q, q.dataset.fixed );
					if ( q.dataset.center )
					{
						r = $.get_offset_point( w ),
						e.left += ( e.width - r.width ) * 0.5;
					};
					if ( q.dataset.offset_y )
					{
						e.bottom += 2,
						e.bottom += ( q.dataset.offset_y | 0 );
					};
					w.style.top = e.bottom + 'px';
					w.style.left = e.left + 'px';
					w.style.visibility = 'visible';
					hidden = w;
				}
				else
				{
					w.style.display = 'none';
				};
			};
			q.onmousedown();
		};
	};


	_.over_input = function( q, w, e, r )
	{
		q.onblur = function()
		{
			clearInterval( r );
			w.call( q, e );
		};
		r = setInterval(function()
		{
			w.call( q, e );
		}, r === undefined ? 420 : r | 0 );
	},
	_.status_callback = function( q, w )
	{
		var e = $.detime( q.substr( 0, 9 ) ), r = $.is_function( w ) ? w : alert;
		if ( e < 1 || ( w = $.json_decode( q.substring(9) ) ) === null )
		{
			return void r.call( window, q );
		};
		if ( w.response_time === undefined )
		{
			w.response_time = e;
		};
		switch ( w[1] )
		{
			case 'goto':
			case 'goto_referer':
				return void $.go( w[1] == 'goto' ? w[0] : w.http_referer || w[0] );
			case 'warn':
			case 'alert':
				return void window.alert( w[0] );
			case 'close':
				w[0] === 'reload' && window.opener.location.reload();
				return void window.close();
			case 'reload':
			case 'refresh':
				return void $.timeout( function(){ location.reload(); }, w[0] );
			default:
				e = _[ w[1] ] || window[ w[1] ], $.is_function( e ) ? e.call( w, w[0] ) : r.call( window, q );
		};
	},
	_.ajax_query = function( q, w )
	{
		var e = 'get', r = null;
		if ( w )
		{
			if ( w.confirm && confirm( w.confirm ) === false )
			{
				return false;
			};
			if ( w.prompt )
			{
				r = prompt( w.prompt, w.value );
				if ( r === null )
				{
					return false;
				};
				e = 'post', r = 'value=' + $.urlencode( r );
			};
		};
		ajax( e, q, _.status_callback ).send( r );
		return false;
	},
	_.query_act = function( q, w )
	{
		for ( var e = {}, r = ( '(0)' + location.search.substr(1) ).match( /\((\d)\)([\%\+\-\.\/\=\w]+)?/g ) || [], t; t = r.shift(); )
		{
			e[ t.substr( 1, 1 ) ] = t.substr(3);
		};
		for ( r in q )
		{
			e[ r ] = q[ r ];
		};
		t = '?' + e[0];
		e[0] = null;
		for ( r in e )
		{
			if ( e[ r ] !== null )
			{
				t += '(' + r + ')' + e[ r ];
			};
		};
		return w ? t : $.go( t );
	},
	_.filter = function( q )
	{
		if ( q['field[]'].nodeType )
		{
			return $.go( q.action );
		};
		for ( var w = [], e = q['field[]'].length, r = 1; r < e; ++r )
		{
			w[ w.length ] = [
				q['field[]'][ r ].value,
				q['query[]'][ r ].value,
				$.urlencode( q['value[]'][ r ].value )
			].join( '.' );
		};
		e = {}, e[ q.dataset.index ] = w.join( '/' );
		return _.query_act( e );
	},
	_.filter_add_conditions = function( q )
	{
		$.get( 'dl', q.form ).appendChild( $.get( 'dd', q.form ).cloneNode( true ) ).removeAttribute( 'style' );
	}






	//取得form字段信息
	_.form_fields = function( q )
	{
		for ( var w = 0, e = [], r = {}; w < q.length; w++ )
		{
			if ( q[ w ].name && r[ '_' + q[ w ].name ] === undefined )
			{
				r[ '_' + q[ w ].name ] = true;
				e.push( q[ w ].name );
			};
		};
		return e;
	};

	//设置form字段值
	_.form_values = function( a, s )
	{

	};


	//废除form字段输入
	_.form_disableds = function( q, w )
	{
		w = w === undefined ? true : !!w;
		for ( var e = 0; e < q.length; e++ )
		{
			if ( q[ e ].name )
			{
				q[ e ].disabled = w;
			};
		};
		$( q ).onsubmit || ( $( q ).onsubmit = q.onsubmit )
		q.onsubmit = w ? call_false : $( q ).onsubmit;
	};

	_.form_post_file = function( q )
	{
		// for ( var w = 0, e; w < q.files.length; w++ )
		// {
		// 	e = q.parentElement.appendChild( $.dom_elem( 'label' ) );
		// 	e.className = 'wa_label_input over-bgcolor';
		// 	e.innerHTML = q.files[ w ].name + '(' + q.files[ w ].size + ')';
		// };
	}
	//获得表单数据结构
	_.form_post_values = function( q )
	{
		for ( var w = {}, e = {}, r = 0, t, y, u, i, o; r < q.length; r++ )
		{
			t = q[ r ];
			if ( !t.name || t.value === undefined || t.disabled || ( ( t.type == 'checkbox' || t.type == 'radio' ) && t.checked === false ) )
			{
				continue;
			};
			for ( y = w, u = t.name.match( /^\w+|\[\w+\]|\[\]/g ) || [], i = 0; i < u.length; i++ )
			{
				if ( i == 0 && u[ i ].charAt(0) == '[' )
				{
					break;
				};
				if ( u[ i ] == '[]' )
				{
					o = u.slice( 0, i ).join( '_' );
					e[ o ] === undefined ? e[ o ] = 0 : ++e[ o ];
					o = e[ o ];
				}
				else
				{
					o = u[ i ].replace( /^\[|\]$/, '' );
				};
				if ( i + 1 == u.length )
				{
					y[ o ] = t.value;
					break;
				};
				if ( y[ o ] === undefined )
				{
					y[ o ] = {};
				};
				y = y[ o ];
			};
		};
		return w;
	}

	_.form_post = function( q, w )
	{
		var e, r, t, y, u;
		if ( w )
		{
			e = _.form_post_values( q );
			for ( r in w )
			{
				if ( $.is_string( t = e[ r ] === undefined ? '' : e[ r ] ) )
				{
					y = $.utf8_encode( t ).length;
					if ( y >= w[ r ][0] && y <= w[ r ][1] && ( w[ r ][2] ? w[ r ][2].test( t ) : true ) )
					{
						continue;
					};
				}
				else
				{
					if ( w[ r ][2] )
					{
						y = [];
						for ( u in t )
						{
							if ( w[ r ][2].test( t[ u ] ) )
							{
								continue;
							};
							y = null;
							break;
						};
					}
					else
					{
						y = $.array_values( t );
					};
					if ( y && y.length >= w[ r ][0] && y.length <= w[ r ][1] )
					{
						continue;
					};
				};
				//以下是处理表单没有通过验证的代码
				q = q[ r ] || q[ r + '[]' ];
				if ( q.nodeType )
				{
					if ( !q.onfocus )
					{
						q.onfocus = function()
						{
							_.over_input( this, function( q )
							{
								var w = $.utf8_encode( this.value ).length;
								this.className = w >= q[0] && w <= q[1] && ( q[2] ? q[2].test( this.value ) : true ) ? null : 'r';
							}, w[ r ] );
						},
						q.className = 'r';
					};
					q.focus();
				};
				if ( q.length )
				{
					q = q[0].parentElement.parentElement;
					q.onclick = function()
					{
						this.className = 'set';
					},
					q.className = 'set-warn';
				};
				return false;
			};
		};
		w = $.get( 'tr>td>div>div>div', q.firstChild.tFoot );
		ajax( 'post', q.action, function( e )
		{
			_.status_callback( e ),
			_.form_disableds( q, false ),
			w.style.width = '0%';
		}, function( q )
		{
			w.style.width = $.round( q.loaded * 100 / q.total ) + '%';
		}).send( new FormData( q ) );
		_.form_disableds( q );
		return false;
	};

	_.multiselect_ajax = function( q )
	{
		var w = $.trim( this.value );
		if ( w === '' || this.dataset.cache === w )
		{
			return;
		};
		this.dataset.cache = w;
		ajax( 'get', this.dataset.url + $.urlencode( w ), function( e, r )
		{
			if ( e = $.json_decode( e ) )
			{
				q.innerHTML = '';
				for ( w in e )
				{
					r = $.dom_elem( 'option' ),
					r.value = w,
					r.innerHTML = e[ w ],
					q.appendChild( r );
				};
			};
		}).send();
	},
	_.multiselect_append = function( q, w, e, r )
	{
		for ( var t = 0; t < w.length; )
		{
			if ( r || w[ t ].selected )
			{
				q.appendChild( w[ t ].cloneNode( true ) ).selected = true,
				e.appendChild( w[ t ] ).selected = false;
				continue;
			};
			++t;
		};
	},
	_.multiselect_remove = function( q, w, e, r )
	{
		for ( var t = 0, y; t < e.length; )
		{
			if ( r || e[ t ].selected )
			{
				y = q.childNodes.length;
				while ( q[ --y ] )
				{
					if ( q[ y ].value === e[ t ].value )
					{
						q[ y ].remove();
						break;
					};
				};
				w.appendChild( e[ t ] ).selected = false;
				continue;
			};
			++t;
		};
	};

	_.progressbar = function( q, w )
	{
		var e = $.dom_elem( 'div' ), r = e.appendChild( $.dom_elem( 'div' ) );
		$.is_string( w ) && void( e.style.width = w ),
		r = r.appendChild( $.dom_elem( 'div' ) ),
		e.className = 'wa_progressbar',
		r.style.width = ( q | 0 ) + '%';
		if ( q && q.nodeType == 1 )
		{
			q.appendChild( e );
			return function( q )
			{
				r.style.width = ( q | 0 ) + '%';
			};
		};
		return e.outerHTML;
	};

	_.signin = function( q )
	{
		var w = [
			'username=' + $.urlencode( q.username.value ),
			'password=' + $.md5( q.password.value )
		], e = $.get( 'i', q ), r, t;
		q.captcha_encrypt && q.captcha_decrypt && w.push(
			'captcha_encrypt=' + $.urlencode( q.captcha_encrypt.value ),
			'captcha_decrypt=' + $.urlencode( q.captcha_decrypt.value )
		),
		e.innerHTML = '',
		_.form_disableds( q ),
		ajax( 'post', q.action, function( w )
		{
			_.form_disableds( q, false ),
			r = $.detime( w.substr( 0, 9 ) ),
			t = q.keep.value | 0;
			clearTimeout( runtime.signin );
			if ( r )
			{
				if ( $.abs( $.time() - r ) < t )
				{
					q.keep.checked
						? $.setcookie( q.dataset.tagname, w.substr(9), $.time() + t )
						: $.setcookie( q.dataset.tagname, w.substr(9) );
					return $.go( q.dataset.referer );
				};
				e.innerHTML = e.dataset.wa_warn_client_time_diff;
			}
			else
			{
				e.innerHTML = w;
			};
			e.style.opacity = 1,
			runtime.signin = setTimeout(function()
			{
				e.style.opacity = 0;
			}, 4000 );
		}).send( w.join( '&' ) );
		return false;
	},
	_.signin_captcha_refresh = function( q )
	{
		q.style.backgroundImage = null,
		q.innerHTML = 'Loading..',
		_.form_disableds( q.parentElement ),
		ajax( 'get', '?/wa/captcha', function( w )
		{
			q.innerHTML = '',
			q.parentElement.captcha_encrypt.value = w,
			q.style.backgroundImage = 'url("?/wa/captcha(1)' + w + '")',
			_.form_disableds( q.parentElement, false );
		}).send();
	};



	_.user_change_password = function( q )
	{
		if ( q.password_old.dataset.password_md5 != $.md5( q.password_old.value ) )
		{
			alert( q.dataset.error_old_password );
			return false;
		};
		if ( !q.password_confirm.value || q.password_confirm.value != q.password_new.value )
		{
			alert( q.dataset.error_confirm_password );
			return false;
		};
		_.form_disableds( q ),
		ajax( 'post', q.action, function( w )
		{
			if ( $.detime( w.substr( 0, 9 ) ) )
			{
				return $.go( location.search );
			};
			alert( w );
			_.form_disableds( q, false );
		}).send($.http_build_query({
			password_old : $.md5( q.password_old.value ),
			password_new : q.password_new.value
		}));
		return false;
	},
	_.user_change_password_input = function()
	{
		this.style.background = ( this.name == 'password_old'
			? this.dataset.password_md5 == $.md5( this.value )
			: this.value && this.value == this.form.password_new.value
		) ? '#e0ffe0' : '#ffe0e0';
	},
	_.user_change_password_test = function()
	{
		for ( var q = $.get( 'div', this.form ), w = $.utf8_encode( this.value ), e = {}, r = w.length, t = 0, y; t < r; t++ )
		{
			y = w.charCodeAt( t );
			if ( e[ y ] === undefined )
			{
				e[ y ] = 1;
			}
			else
			{
				++e[ y ];
			};
		};
		w = 0;
		for ( t in e )
		{
			y = e[ t ] / r;
			w -= y * $.log( y ) / $.log(2);
		};
		w = w / 4 * 100 | 0;
		switch ( true )
		{
			case w < 33:
				this.style.background = '#ffe0e0';
				break;
			case w < 66:
				this.style.background = '#ffffe0';
				break;
			default:
				this.style.background = '#e0ffe0';
		};
		this.form.password_confirm.style.background = this.value && this.value == this.form.password_confirm.value ? '#e0ffe0' : '#ffe0e0';
	},
	_.input_time = function()
	{
		var
			grid = [],
			frame = $.dom_elem( 'div' ),
			current = $.date( 'G,0,0,n,j,Y' ).split( ',' ),
			select = current.join( ',' ).split( ',' ),
			index,
			object,
			callback;

		function show_calendar()
		{
			for ( var q = $.mktime( 0, 0, 0, select[3], 1, select[5] ), w = $.date( 'w', q ) - 1, e = $.date( 't', q ) * 1 + w, r = 0, t; r < 42; r++ )
			{
				grid[ r ].className = grid[ r ].innerHTML = '',
				grid[ r ].style.cssText = 'visibility:hidden';
				if ( r > w && r <= e )
				{
					grid[ r ].innerHTML = t = r - w,
					grid[ r ].style.visibility = 'visible',
					t == current[4] && select[3] == current[3] && select[5] == current[5]
						? this_day.call( grid[ r ] )
						: t == 1 && this_day.call( grid[ r ] );
				};
			};
		};

		function return_time()
		{
			var q = this.name == 'current' ? $.time() : $.mktime.apply( $, select );
			$.is_function( callback ) ? callback.call( object, q ) : object.value = $.date( callback, q ),
			frame.style.display = 'none';
		};

		function change_time()
		{
			switch ( this.name )
			{
				case 'm': return show_calendar( select[3] = this.value );
				case 'y': return show_calendar( select[5] = this.value );
				case 'h': return void( select[0] = this.value );
				case 'i': return void( select[1] = this.value );
				case 's': return void( select[2] = this.value );
				default: return this_day.call( this );
			};
		};

		function this_day()
		{
			grid[ index ] && ( grid[ index ].className = '' ),
			grid[ index = this.name ].className = 'this_day',
			select[4] = this.innerHTML;
		};

		frame.className = 'wa_input_time',
		frame.onmouseover = function(){ document.onmousedown = object.onblur = null; },
		frame.onmouseout = function(){ document.onmousedown = object.onblur = _.input_time; },
		frame.innerHTML = [ '<table><thead><tr>',
			'<td colspan="4"><select name="y"></select><span>-</span><select name="m"></select></td>',
			'<td colspan="3" style="text-align:right;"><button name="current" class="b">Current time</button></td>',
			'</tr></thead><tbody><tr>',
			'<td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td>',
			'</tr></tbody><tfoot><tr>',
			'<td colspan="5"><select name="h"></select><span>:</span><select name="i"></select><span>:</span><select name="s"></select></td>',
			'<td colspan="2" style="text-align:right;"><button name="select">Selected</button></td>',
			'</tr></tfoot></table>' ].join( '' ),
		show_calendar(function()
		{
			for ( var q = 0, w; q < 42; q++ )
			{
				q % 7 || ( w = $.get( 'tbody', frame ).appendChild( $.dom_elem( 'tr' ) ) );
				grid[ q ] = w.appendChild( $.dom_elem( 'td' ) ),
				grid[ q ].name = q,
				grid[ q ].onmouseover = function(){ this.style.color = 'green'; },
				grid[ q ].onmouseout = function(){ this.style.color = null; },
				grid[ q ].onmousedown = change_time;
			};
			q = $.query( 'button', frame ), q[0].onclick = q[1].onclick = return_time;
		}()),
		function( q, w, e, r )
		{
			var t = $.get( q, frame );
			while ( w <= e )
			{
				q = t.appendChild( $.dom_elem( 'option' ) ),
				q.value = q.innerHTML = w,
				q.selected = r == w++;
			};
			t.onchange = change_time;
			return arguments.callee;
		}
		( 'select[name=y]', select[5] - 40, select[5] * 1 + 10, select[5] )
		( 'select[name=m]', 1, 12, select[3] )
		( 'select[name=h]', 0, 23, select[0] )
		( 'select[name=i]', 0, 59, select[1] )
		( 'select[name=s]', 0, 59, select[2] ),
		(_.input_time = function( q, w )
		{
			if ( $.is_string( w ) || $.is_function( w ) )
			{
				object = q,
				callback = w,
				q.onblur = _.input_time;
				if ( document.body )
				{
					q = $.get_offset_point( q ),
					frame.style.top = ( q.bottom - 8 ) + 'px',
					frame.style.left = ( q.left + 32 ) + 'px',
					$.get_parent( frame ) || $.set_append( frame );
				};
				return void( frame.style.display = '' );
			};
			return void( frame.style.display = 'none' );
		}).apply( _, arguments );
	},
	_.ajax = ajax,
	window.wa = _;
})( $ );
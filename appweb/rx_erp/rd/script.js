function rd_form_ip0_search( q )
{
	q = $.trim( q.value );
	if ( /^\d{1,3}(\.\d{1,3}){0,3}$/.test( q ) )
	{
		q = q.split( '.' );
		return wa.query_act({ 7 : q.length > 3
			? 'ip_prefix_address.eq.' + $.urlencode( q.slice( 0, 3 ).join( '.' ) ) + '/ip_suffix_start.le.' + ( q[3] | 0 ) + '/ip_suffix_end.ge.' + ( q[3] | 0 )
			: 'ip_prefix_address.like.' + $.urlencode( q.join( '.' ) + '%' ) });
	};
	return wa.query_act({ 7 : q.length ? 'note.like.' + $.urlencode( '%' + q + '%' ) : null });
}

function rd_form_ip0_input()
{
	var q = this.form, w = q.ip_prefix_address.value.replace( /[^\d\.]+/g, '' ), e;
	if ( w )
	{
		e = q.ip_suffix_start.value.replace( /[^\d]+/g, '' ) | 0,
		q.gateway.value = w + '.' + ( e + 1 ),
		q.network_id.value = w + '.' + e,
		q.broadcast_address.value = w + '.' + q.ip_suffix_end.value.replace( /[^\d]+/g, '' );
	}
	else
	{
		q.gateway.value =
		q.network_id.value =
		q.broadcast_address.value = '';
	};
}

function rd_form_device_search( q )
{
	var w = $.trim( q[0].value ), e = [ $.trim( q[1].value ), $.trim( q[2].value ), $.trim( q[3].value ) ];
	if ( /^\d{1,3}(\.\d{1,3}){0,3}$/.test( w ) )
	{
		w = { 6 : w, 7 : null };
	}
	else
	{
		w = { 6 : null, 7 : w ? 'only.like.' + $.urlencode( '%' + w + '%' ) : null };
	};
	if ( e[0] || e[1] || e[2] )
	{
		e = 'sb_info.like.' + $.urlencode( '%CPU' + ( e[0] ? '%' + e[0] : '' )
			+ '%内存' + ( e[1] ? '%' + e[1] : '' )
			+ '%硬盘' + ( e[2] ? '%' + e[2] : '' )
			+ '%电源%' );
	}
	else
	{
		e = null;
	};
	if ( e )
	{
		w[7] = w[7] ? w[7] + '/' + e : e;
	};
	return wa.query_act( w );
}

function rd_form_device_userid( q )
{
	if ( $.trim( this.value ) == '' || this.dataset.userid == this.value )
	{
		return;
	};
	this.dataset.userid = this.value;
	wa.ajax( 'get', this.form.action + '(3)' + $.urlencode( this.value ), function( w )
	{
		var e = 0, r, t;
		q.innerHTML = '';
		if ( w == '' )
		{
			return;
		};
		w = w.split( ' ' );
		while ( e < w.length )
		{
			r = w[ e++ ].split( ',' );
			t = $.dom_elem( 'option' );
			t.value = r[0];
			t.label = r[1];
			q.appendChild( t );
		};
	}).send();
}

function rd_form_device_note( q )
{
	if ( $.trim( this.value ) == '' || this.dataset.note == this.value )
	{
		return;
	};
	this.dataset.note = this.value;
	wa.ajax( 'get', this.form.action + '(3)' + this.value, function( w )
	{
		var e = 0, r, t;
		q.innerHTML = '';
		if ( w == '' )
		{
			return;
		};
		w = w.split( ' ' );
		while ( e < w.length )
		{
			r =  w[ e++ ].split( ',' );
			t = $.dom_elem( 'option' );
			t.value = r[0];
			t.label = r[1];
			q.appendChild( t );
		};
	}).send();
}

function rd_form_device_ip( q )
{
	var w = this.value.split( '.' );
	w = ( '0' + ( w[0] | 0 ).toString(16) ).slice(-2)
		+ ( '0' + ( w[1] | 0 ).toString(16) ).slice(-2)
		+ ( '0' + ( w[2] | 0 ).toString(16) ).slice(-2)
		+ ( '0' + ( w[3] | 0 ).toString(16) ).slice(-2);
	if ( this.dataset.ip == w && this.dataset.select === this.form.sb_info )
	{
		return;
	};
	this.dataset.ip = w;
	wa.ajax( 'get', this.form.action + '(4)' + w + '(5)' + $.urlencode( this.dataset.select ), function( w )
	{
		for ( var e = '', r = q[1].getElementsByTagName( 'label' ), t = 0; t < r.length; ++t )
		{
			r[ t ].firstChild.checked ? e += ',' + r[ t ].firstChild.value + ',' : r[ t-- ].remove();
		};
		for ( r = q[2].getElementsByTagName( 'label' ), t = 0; t < r.length; ++t )
		{
			r[ t ].firstChild.checked ? e += ',' + r[ t ].firstChild.value + ',' : r[ t-- ].remove();
		};
		if ( w == '' )
		{
			return;
		};
		for ( w = w.split( ' ' ), r = 0; r < w.length; ++r )
		{
			if ( e.indexOf( ',' + w[ r ] + ',' ) === -1 )
			{
				t = q[0].cloneNode( true ),
				t.firstChild.onclick = function(){ rd_sb_type_onchange( this.form.sb_type ); },
				t.lastChild.innerHTML = t.firstChild.value = w[ r ],
				q[1].appendChild( t ).firstChild.name = 'ip_main[]',
				t = t.cloneNode( true ),
				t.firstChild.onclick = function(){ rd_sb_type_onchange( this.form.sb_type ); },
				q[2].appendChild( t ).firstChild.name = 'ip_vice[]';
			};
		};
	} ).send();
}

function rd_device_select_filter( q, w, qq )
{
	var e = /\(7\)[\%\+\-\.\/\=\w]+/.exec( location.search ), r;
	e = e ? e.toString().substr(3).split( '/' ) : [];

	if ( qq === undefined )
	{
		qq = 'eq';
	};

	for ( r = 0; r < e.length; r++ )
	{
		if ( e[ r ].indexOf( q + '.' + qq ) === 0 )
		{
			
			if ( w.value )
			{
				
				e[ r ] = q + '.' + qq + '.' + $.urlencode( w.value );
			}
			else
			{
				e.splice( r, 1 );
			};
			return wa.query_act( { 7 : e.join( '/' ) || null } );
		};
	};
	if ( w.value )
	{
		e[ e.length ] = q + '.' + qq + '.' + $.urlencode( w.value );
	};
	wa.query_act( { 7 : e.join( '/' ) || null } );
}

function rd_filter_callback( q )
{
	'time,pw_time,pw_time,pw_starts,pw_expire'.indexOf( q.previousSibling.previousSibling.value ) === -1 || wa.input_time( q, 'U' );
}

function rd_sb_type_onchange( q )
{
	var w = $.get_parent( q, 'tBodies' ).tBodies[0], e = 11, r, t, y, u, i, sb_type_jg = $.get( '#sb_type_jg' );
	$.setcookie( 'rd_sb_type', q.value );
	while ( ++e < w.rows.length )
	{
		w.deleteRow( e-- );
	};
	if ( sb_type_jg )
	{
		sb_type_jg.style.display = 'none';
	};
	if ( /^(|请选择|路由器|防火墙|交换机|其它)$/.test( q.value ) )
	{
		return void( w.rows[11].style.display = null );
	};

	w.rows[11].style.display = 'none';
	if ( q.value === '服务器' || q.value === '云设备' || q.value === '独立刀片机' || q.value === '刀片服务器' )
	{
		y = $.json_decode( q.form.sb_info.value ) || {};
		e = w.insertRow();
		e.insertCell().innerHTML = '主板';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[主板]';
		r.value = y[ '主板' ] ? y[ '主板' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = 'CPU';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[CPU]';
		r.value = y[ 'CPU' ] ? y[ 'CPU' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '内存';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[内存]';
		r.value = y[ '内存' ] ? y[ '内存' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '硬盘';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[硬盘]';
		r.value = y[ '硬盘' ] ? y[ '硬盘' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '电源';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[电源]';
		r.value = y[ '电源' ] ? y[ '电源' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '风扇';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[风扇]';
		r.value = y[ '风扇' ] ? y[ '风扇' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '机箱';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[机箱]';
		r.value = y[ '机箱' ] ? y[ '机箱' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '系统';
		r = $.dom_elem( 'select' );
		r.style.cssText = 'width:100%';
		r.name = 'sb_info[系统]';
		

		t = $.dom_elem( 'option' );
		t.value = '';
		t.innerHTML = '其他';
		r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'winxp';
		// t.innerHTML = 'Microsoft Windows XP';
		// r.appendChild( t );

		t = $.dom_elem( 'option' );
		t.value = 'win2003';
		t.innerHTML = 'Microsoft Windows 2003';
		r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'win7';
		// t.innerHTML = 'Microsoft Windows 7';
		// r.appendChild( t );

		t = $.dom_elem( 'option' );
		t.value = 'win2008';
		t.innerHTML = 'Microsoft Windows 2008';
		r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'win2012';
		// t.innerHTML = 'Microsoft Windows 2012';
		// r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'redhat';
		// t.innerHTML = 'Redhat';
		// r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'centos';
		// t.innerHTML = 'Centos';
		// r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'ubuntu';
		// t.innerHTML = 'Ubuntu';
		// r.appendChild( t );

		// t = $.dom_elem( 'option' );
		// t.value = 'xenserver';
		// t.innerHTML = 'Xenserver';
		// r.appendChild( t );

		e.insertCell().appendChild( r );
		r.value = y[ '系统' ] ? y[ '系统' ] : '';

		e = w.insertRow();
		e.insertCell().innerHTML = '账号';
		r = $.dom_elem( 'div' ).appendChild( $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[账号]';
		r.value = y[ '账号' ] ? y[ '账号' ] : '';
		e.insertCell().appendChild( r.parentNode );

		e = w.insertRow();
		e.insertCell().innerHTML = '密码';
		r = $.dom_elem( 'div' ).appendChild( t = $.dom_elem( 'input' ) );
		r.parentNode.className = 'fix';
		r.type = 'text';
		r.name = 'sb_info[密码]';
		r.value = y[ '密码' ] ? y[ '密码' ] : '';
		e.insertCell().appendChild( r.parentNode );

		r = $.dom_elem( 'button' );
		r.type = 'button';
		r.onclick = function()
		{
			t.value = $.str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyz' ).substr(-8);

		};
		r.innerHTML = '生成密码';


		e.insertCell().appendChild( r );

		return;
	};
	if ( sb_type_jg && q.value === '机柜' )
	{
		sb_type_jg.style.display = '';
	}
}

function rd_device_add_device( q )
{
	return !$.window_open( q, 'rd_device_add_device', { width : 640, height : 600 } );
}

function rd_device_ip_input_toggle( q )
{
	var w = q.firstChild, e = q.lastChild;
	if ( w !== e )
	{
		e.remove();
		w.style.display = '';
		return;
	};
	w.style.display = 'none',
	w = $.dom_elem( 'div' ),
	w.className = 'fix',
	e = w.appendChild( $.dom_elem( 'textarea' ) ),
	e.name = q.firstChild.caption.firstChild.name.slice( 0, -2 ),
	e.style.cssText = 'width:100%;height:199px',
	e.placeholder = '请输入IP，回车切开',
	q.appendChild( w );
	return e;
}

function rd_task_null( q, w )
{
	q.sbxx.placeholder =
	q.pkno.placeholder =
	q.pkip.placeholder =
	q.sbxx.value =
	q.pkno.value =
	q.pkip.value = '';
}

function rd_ajax_json()
{
	var q = this.form, w;
	switch ( this.name )
	{
		case 'pkip':
			w = this.value.match( /^\d{1,3}(\.\d{1,3}){3}$/ );
			break;
		case 'pkno':
			w = this.value.match( /^[A-Z\d\-]+$/i );
			break;
		default:
			w = this.value.match( /\d{1,3}(\.\d{1,3}){3}/ );
	};
	if ( w === null )
	{
		return;
	};
	if ( this.dataset.old == w[0] )
	{
		return;
	};
	rd_task_null( q );
	this.dataset.old = w[0];
	q[ this.name ].placeholder = w[0];
	q.sbxx.placeholder = '正在获取,请稍候..';
	w = this.name == 'pkno' ? '(1)' + w[0] : '(2)' + w[0];
	wa.ajax( 'get', '?/rx_erp/ermsd/ajax_sbxx' + w, function( w )
	{
		rd_task_null( q );
		w = $.json_decode( w );
		if ( w === null )
		{
			q.pkip.value = '0.0.0.0';
			return;
		};
		q.pkip.value = w.IP;
		q.pkno.value = w.server_No;
		q.sbxx.value = [
			'使用情况: '	+ w.server_state_type,
			'所在位置: '	+ w.rack,
			'所在机房: '	+ w.machine_room_name,
			'业务员: '		+ w.user_name,
			'维护QQ: '		+ w.client_machine_manager_QQ1 + ' - ' + w.client_machine_manager_QQ2,
			'主板: '		+ w.mainboard,
			'CPU: '			+ w.CPU,
			'内存: '		+ w.RAM,
			'硬盘: '		+ w.ROM,
			'产权: '		+ w.configuration_owner_type,
			'电源: '		+ w.power_supply,
			'风扇: '		+ w.fan,
			'机箱: '		+ w.bandwidth,
			'防护: '		+ w.defend ].join( '\n' );
	}).send();
}
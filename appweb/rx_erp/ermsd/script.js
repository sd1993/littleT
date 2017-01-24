var
	ermsd_task_work = [];

function ermsd_task_submit_confirm()
{
	var q = prompt( this[0] );
	if ( $.md5( q ) === this.md5 )
	{
		q = $.get( 'form' );
		wa.form_disableds( q, false );
		q.action += '(9)confirm';
		q.onsubmit()
		return;
	};
	q === null || alert( '密码错误' );
}

function ermsd_staff_select()
{
	var q = $.dom_elem( 'select' ), w = {}, e, r;
	e = q.appendChild( $.dom_elem( 'option' ) );
	e.value = '';
	e.innerHTML = '';
	w._ = q.appendChild( $.dom_elem( 'optgroup' ) );
	w._.label = '未分配';
	w._.style.background = 'blue';
	w._.style.color = 'white';
	for ( e in ermsd_staff_list )
	{
		r = ermsd_staff_list[ e ].team ? ermsd_staff_list[ e ].team : '_';
		if ( w[ r ] === undefined )
		{
			w[ r ] = q.appendChild( $.dom_elem( 'optgroup' ) );
			w[ r ].label = r;
			w[ r ].style.background = 'blue';
			w[ r ].style.color = 'white';
		};
		r = w[ r ];
		r = r.appendChild( $.dom_elem( 'option' ) );
		r.style.color = 'black';
		r.value = e;
		if ( ermsd_staff_list[ e ].tasks > 1 )
		{
			r.style.background = 'LightPink';
		}
		else if ( ermsd_staff_list[ e ].tasks == 1 )
		{
			r.style.background = 'Khaki';
		};
		r.innerHTML = ermsd_staff_list[ e ].name;
	};
	q.style.cssText = 'background:transparent;padding:2px;border:0;box-shadow:none';
	return q;
}

function ermsd_task_work_assign()
{
	var q = this.value && ermsd_staff_list[ this.value ].team ? '(4)' + ermsd_staff_list[ this.value ].team : '';
	wa.ajax_query( '?/rx_erp/ajax(1)ermsd_task_work(2)' + this.parentNode.parentNode.dataset.md5 + '(3)' + this.value + q );
}

function ermsd_task_work_runtime()
{
	++server_time;
	for ( var q = 0, w, e; q < ermsd_task_work.length; q++ )
	{
		w = ermsd_task_work[ q ];
		e = w.start + w.timer - server_time;
		if ( e > 0 )
		{
			w.show.style.color = 'green';
			w.show.innerHTML = '+ ' + ( e / 60 | 0 ) + ':' + ( '0' + ( e % 60 ) ).substr(-2);
		}
		else
		{
			w.show.style.color = 'red';
			w.show.innerHTML = '- ' + $.abs( e / 60 | 0 ) + ':' + ( '0' + $.abs( e % 60 ) ).substr(-2);
		};
		
	};
}

function ermsd_task_work_init()
{
	var q = ermsd_staff_select(), w = $.query( 'table#ermsd_task_work_list>tbody>tr' ), e, r, t, y, u;
	for ( e = 0, r = w.length; e < r; e += 2 )
	{
		t = w[ e ].getElementsByTagName( 'td' );
		y = q.cloneNode( true );
		y.onchange = ermsd_task_work_assign;
		if ( t[3].dataset.start )
		{
			t[4].innerHTML = $.date( 'Y-m-d H:i:s', t[3].dataset.start );
			u = $.get( '[value="' + t[3].dataset.d_userid + '"]', y );
			if ( u )
			{
				y.style.background = u.style.background;
				u.selected = true;
			};
			ermsd_task_work.push({
				start : t[3].dataset.start | 0,
				timer : ermsd_task_type[ t[2].dataset.type ] | 0,
				wors : w[ e ],
				show : t[5]
			});
		};
		t[3].appendChild( y );
		if ( ermsd_staff_list[ t[10].innerHTML ] )
		{
			t[10].innerHTML = ermsd_staff_list[ t[10].innerHTML ].name;
		};
	};
	ermsd_task_work_runtime(), setInterval( ermsd_task_work_runtime, 1000 );
}

function ermsd_task_work_init_my()
{

	var q = $.query( 'table#ermsd_task_work_list>tbody>tr' ), w, e, r, t, y, u;
	for ( w = 0, e = q.length; w < e; w += 2 )
	{
		r = q[ w ].getElementsByTagName( 'td' );
		if ( r[4].dataset.start )
		{
			ermsd_task_work.push({
				start : r[4].dataset.start | 0,
				timer : ermsd_task_type[ r[1].innerHTML ] | 0,
				wors : q[ w ],
				show : r[4]
			});
		};
	};
	ermsd_task_work_runtime(), setInterval( ermsd_task_work_runtime, 1000 );
}

function ermsd_task_runtime_init( q )
{
	var w = q - $.time();
	window.server_time = q;
	setInterval( function()
	{
		window.server_time = $.time() + w;
	}, 1000 );
};

function ermsd_task_null( q, w )
{
	q.sbxx.placeholder =
	q.pkno.placeholder =
	q.pkip.placeholder =
	q.sbxx.value =
	q.pkno.value =
	q.pkip.value = '';
}

function ermsd_ajax_json()
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
	ermsd_task_null( q );
	this.dataset.old = w[0];
	q[ this.name ].placeholder = w[0];
	q.sbxx.placeholder = '正在获取,请稍候..';
	w = this.name == 'pkno' ? '(1)' + w[0] : '(2)' + w[0];
	wa.ajax( 'get', '?/rx_erp/ermsd/ajax_sbxx' + w, function( w )
	{
		ermsd_task_null( q );
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

function ermsd_task_stat( q )
{
	q.parentNode.firstChild.value ? wa.query_act({7:[
		'team.eq.' + $.urlencode( q.parentNode.firstChild.value ),
		'start.ge.' + q.dataset.start_time,
		'start.le.' + q.dataset.end_time
	].join( '/' )}) : alert( '请选择团队' );
}

function ermsd_filter_callback( q )
{
	'time,start'.indexOf( q.previousSibling.previousSibling.value ) === -1 || wa.input_time( q, 'U' );
}

function ermsd_device_filter( q )
{
	var w = $.trim( q[0].value );
	if ( /^\d{1,3}(\.\d{1,3}){0,3}$/.test( w ) )
	{
		w = { 6 : w, 7 : null };
	}
	else
	{
		w = { 6 : null, 7 : w ? 'only.like.' + $.urlencode( '%' + w + '%' ) : null };
	};
	return wa.query_act( w );
}
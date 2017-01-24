
function sd_form_device_search( q )
{
	q = $.trim( q.value );
	q = /^\d{1,3}(\.\d{1,3}){0,3}$/.test( q )
		? { 6 : q, 7 : null }
		: { 6 : null, 7 : q ? 'only.like.' + $.urlencode( '%' + q + '%' ) : null };
	return wa.query_act( q );
}

function sd_filter_callback( q )
{
	'time,start,over,done'.indexOf( q.previousSibling.previousSibling.value ) === -1 || wa.input_time( q, 'U' );
}

function sd_task_submit_confirm()
{
	var q;
	if ( confirm( this[0] ) )
	{
		q = $.get( 'form' );
		wa.form_disableds( q, false );
		q.action += '(9)confirm';
		q.onsubmit()
	};
}

function sd_ajax_decive( q )
{
	var w = this.value.match( /\d{1,3}(\.\d{1,3}){3}/ );
	if ( w ===null || this.dataset.old === w[0] )
	{
		return;
	};
	this.dataset.old = w[0],
	this.value = this.value.replace( /\d{1,3}(\.\d{1,3}){3}/, '$&\n\n' ),
	q.sbxx.value = '正在获取 ' + w[0] + ' 设备信息, 请稍后...',
	wa.form_disableds( q ),
	wa.ajax( 'get', '?/rx_erp/ermsd/ajax_sbxx(2)' + w[0], function( w )
	{
		wa.form_disableds( q, false ),
		q.note.focus(),
		w = $.json_decode( w );
		if ( w === null )
		{
			return q.sbxx.value = q.sbstatus.value = q.sbqq.value = q.pkno.value = q.pkip.value = '';
		};
		q.pkip.value = w.IP,
		q.pkno.value = w.server_No,
		q.sbqq.value = w.client_machine_manager_QQ1 + ' - ' + w.client_machine_manager_QQ2,
		q.sbstatus.value = w.server_state_type,
		q.user_name.value = w.user_name,
		q.sbxx.value = [
			'使用情况: '	+ w.server_state_type,
			'所在位置: '	+ w.rack,
			'所在机房: '	+ w.machine_room_name,
			'业务员: '		+ w.user_name,
			'维护QQ: '		+ ( w.client_machine_manager_QQ1 + ' - ' + w.client_machine_manager_QQ2 ).replace( /\//g, '\n' ),
			'主IP: ',		w.ip_main,
			'副IP: ',		w.ip_vice,
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

function sd_task_search( q )
{
	return wa.query_act({ 7 : q.length ? 'note.like.' + $.urlencode( '%' + q + '%' ) : null });
}


function sd_device_filter( q )
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
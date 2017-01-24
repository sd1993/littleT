<?php
wa::$buffers->js[] = 'appweb/rx_erp/rd/script.js';
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'rx_pack':				goto rx_pack;
	case 'rd_services':			goto rd_services;
	case 'rd_serverrd':			goto rd_serverrd;
	case 'rd_ip0_admin':		goto rd_ip0_admin;
	case 'rd_ip0_insert':		goto rd_ip0_insert;
	case 'rd_ip0_update':		goto rd_ip0_update;
	case 'rd_ip1_list':			goto rd_ip1_list;
	case 'rd_ip1_edit':			goto rd_ip1_edit;
	case 'rd_forcer_admin':		goto rd_forcer_admin;
	case 'rd_forcer_device':	goto rd_forcer_device;
	case 'rd_forcer_insert':	goto rd_forcer_insert;
	case 'rd_forcer_update':	goto rd_forcer_update;
	case 'rd_device_admin':		goto rd_device_admin;
	case 'rd_device_insert':	goto rd_device_insert;
	case 'rd_device_update':	goto rd_device_update;
	case 'rd_device_status':	goto rd_device_status;
	case 'rd_services_device':	goto rd_services_device;
	case 'rd_delete_record':	goto rd_delete_record;
	case 'rd_action_record':	goto rd_action_record;
	case 'rd_staff_update':		goto rd_staff_update;
	case 'rx_notice_insert':	goto rx_notice_insert;
 	default:					goto rx_notice;
};

rx_pack:
rx_pack_record( '资源部' );
goto rd_end;

rd_services:
wa::$buffers->script = 'rx_ws_init("/rd_services")';
$rx_staff = rx_get_staff( $q );
if ( isset( $_GET[7] ) )
{
	$rx_task_query = [ '`describe`="回收设备"', 'done is null' ];
	$_GET[7] > 1 && $rx_task_query[] = 'over is not null';
}
else
{
	$rx_task_query = [ '`describe`="回收设备"', 'done is not null' ];
}



$rx_task_query = wa::get_filter( [ 'userid', 'describe', 'pkno', 'note' ], $rx_task_query );
$rx_task_count = wa::$sql->get_rows( 'rx_rd_task', $rx_task_query );
wa::htt_data_table( 'rx_rd_task', [
	'only' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'done' => FALSE,
	'time' => [ 'desc', '创建时间' ],
	'userid' => [ FALSE, '创建员工' ],
	'd_group' => [ FALSE, '完成部门' ],
	'describe' => [ FALSE, '描述' ],
	'pkno' => [ FALSE, '设备编号' ],
	'pkip' => [ FALSE, '设备IP' ],
	'start' => [ FALSE, '开始时间' ],
	'name' => [ FALSE, '跟进员工' ],
	'over' => [ FALSE, '完成时间' ],
	0 => '操作'
], function( $w, $e ) use( $q, &$rx_staff )
{
	static $bgcolor;
	$w['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
	$w->td[] = date( 'Y-m-d H:i:s', $e['time'] );
	$w->td[] = isset( $rx_staff[ $e['userid'] ] ) ? $rx_staff[ $e['userid'] ]['name'] : $e['userid'];
	$w->td[] = $e['d_group'];
	$w->td[] = $e['describe'];
	$r = &$w->td[];
	$r->tag_a( $e['pkno'], '?/rx_erp(1)rd_device_admin(7)only.eq.' . $e['pkno'] );
	$w->td[] = $e['pkip'];
	if ( $e['start'] === NULL )
	{
		$w->td[] = '等待开始';
		$w->td[] = '未分配';
		$w->td[] = '等待完成';
		$r = &$w->td[];
		$r = $r->tag_a( '删除', '?/rx_erp/ajax(1)rd_task_delete(2)' . $e['only'] . '(3)' . $e['pkno'] );
		$r['data-confirm'] = '靓仔！你确定要删除吗？';
		$r['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	}
	else
	{
		$w->td[] = date( 'Y-m-d H:i:s', $e['start'] );
		$w->td[] = $e['name'];
		if ( $e['over'] === NULL )
		{
			$w->td[] = '等待完成';
			$w->td[] = '等待';
		}
		else
		{
			$w->td[] = date( 'Y-m-d H:i:s', $e['over'] );
			$r = &$w->td[];
			$e['done'] === NULL
				? $r->tag_a( '验收', '?/rx_erp/ajax(1)rd_task_done(2)' . $e['only'] )['onclick'] = 'return wa.ajax_query(this.href)'
				: $r[0] = date( 'Y-m-d H:i:s', $e['done'] );
			
		};
	};
	$w = $w->get_parent()->add( 'tr' );
	$w['style'] = 'background:' . $bgcolor;
	$r = &$w->td[];
	$r['colspan'] = 3;
	$r->pre = $e['sbxx'];
	$r = &$w->td[];
	$r['colspan'] = 7;
	$r->pre = $e['note'];
}, [
	'merge_query' => $rx_task_query,
	'stat_rows' => $rx_task_count,
	'page_rows' => 21
] )->set(function( $q ) use( $rx_task_count )
{
	$q['style'] = 'margin:21px auto';
	$q->thead->tr->add_before( 'tr' )->add( 'td' )->set_attr( 'colspan', 10 )->ins_filter([
		'userid' => '创建员工',
		'describe' => '描述',
		'pkno' => '设备编号',
		'note' => '详细' ] );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', 10 );
	$w = &$q->thead->tr[1]->td->form->dl->dt;

	$w->tag_button( '所有等待维护记录' )['onclick'] = 'return wa.query_act({7:1})';
	$w->tag_button( '未验收的维护记录' )['onclick'] = 'return wa.query_act({7:2})';
	$w->tag_button( '验收历史维护记录' )['onclick'] = 'return wa.query_act({6:null,7:null})';


	$w->add( 'span', '共找到 ' . $rx_task_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rd_serverrd:
if ( isset( $_GET[6] ) )
{
	wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';
	$q = wa::htt_form_post( $rd_serverrd_editor, [ 'pkip' => '0.0.0.0' ] );
	$q['action'] = '?/rx_erp/ajax(1)rd_serverrd';
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
	$q->table->tbody->tr[2]->td[1]->div->input['readonly'] = TRUE;
	$w = $q->xpath( '//textarea[@name]' );
	$w[0]['readonly'] = TRUE;
	$w[0]['style'] = $w[1]['style'] = 'height:119px;overflow-y:auto';
	$w[1]['placeholder'] = '任务说明IP将自动匹配并且异步获取';
	$w[1]['onfocus'] = 'wa.over_input(this,rd_ajax_json)';
	$w = $q->xpath( '//input' );
	$w[1]['onfocus'] = $w[0]['onfocus'] = 'wa.over_input(this,rd_ajax_json)';
	$w[0]['data-old'] = '0.0.0.0';
	exit;
};
wa::$buffers->script = 'rx_ws_init("/rd_services")';
$q = ['`describe`!="回收设备"'];


switch ( isset( $_GET[2] ) ? $_GET[2] : NULL )
{
	case 'wait_start':
		$q[] = 'start is null';
		break;
	case 'wait_done':
		$q[] = 'over is not null';
		$q[] = 'done is null';
		break;
	case 'done_all':
		$q[] = 'done is not null';
		break;
	default:
		$q[] = 'done is null';
};



$rx_task_query = wa::get_filter( [ 'userid', 'describe', 'pkno', 'note' ], $q );
$rx_task_count = wa::$sql->get_rows( 'rx_rd_task', $rx_task_query );

$t_action_night=isset( $_GET[9] ) ? $_GET[9] : NULL;
$t_action_two=isset( $_GET[2] ) ? $_GET[2] : NULL;
if( $t_action_night== 'fuckyou' && $t_action_two == 'wait_done') {
	/*
	$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
	$txt = $rx_task_count."\n";
	fwrite($myfile, $txt);
	*/
	ob_end_clean();
	echo json_encode($rx_task_count);exit;
}

//var_dump($rx_task_query);
wa::htt_data_table( 'rx_rd_task', [
	'only' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'done' => FALSE,
	'time' => [ 'desc', '创建时间' ],
	'userid' => [ FALSE, '创建员工' ],
	'd_group' => [ FALSE, '完成部门' ],
	'describe' => [ FALSE, '描述' ],
	'pkno' => [ FALSE, '设备编号' ],
	'pkip' => [ FALSE, '设备IP' ],
	'start' => [ FALSE, '开始时间' ],
	'name' => [ FALSE, '跟进员工' ],
	'over' => [ FALSE, '完成时间' ],
	0 => '操作'
], function( $q, $w )
{
	static $bgcolor;
	$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
	$e = &$q->td[];
	//$e['onclick'] = 'rx_tr_resh(this.parentNode)';
	//$e['style'] = 'cursor:pointer;text-decoration:underline';
	$e[0] = date( 'Y-m-d H:i:s', $w['time'] );


	$q->td[] = $w['userid'];
	$q->td[] = $w['d_group'];
	$q->td[] = $w['describe'];
	$e = &$q->td[];
	$e->tag_a( $w['pkno'], '?/rx_erp(1)rd_device_admin(7)only.eq.' . $w['pkno'] );
	$q->td[] = $w['pkip'];
	if ( $w['start'] === NULL )
	{
		$q->td[] = '等待开始';
		$q->td[] = '未分配';
		$q->td[] = '等待完成';
		$e = &$q->td[];
		$e = $e->tag_a( '删除', '?/rx_erp/ajax(1)rd_task_delete(2)' . $w['only'] . '(3)' . $w['pkno'] );
		$e['data-confirm'] = '靓仔！你确定要删除吗？';
		$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	}
	else
	{
		$q->td[] = date( 'Y-m-d H:i:s', $w['start'] );
		$q->td[] = $w['name'];
		if ( $w['over'] === NULL )
		{
			$q->td[] = '等待完成';
			$q->td[] = '等待';
		}
		else
		{
			$q->td[] = date( 'Y-m-d H:i:s', $w['over'] );
			$e = &$q->td[];
			$w['done'] === NULL
				? $e->tag_a( '验收', '?/rx_erp/ajax(1)rd_task_done(2)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href)'
				: $e[0] = date( 'Y-m-d H:i:s', $w['done'] );
			
		};
	};
	$e = &$q->get_parent()->tr[];
	$e['style'] = 'background:' . $bgcolor;
	//$e['style'] = 'display:none';
	$e->td['colspan'] = 3;
	$e->td->pre = $w['sbxx'];
	$e->td[]['colspan'] = 7;
	$e->td[1]->pre = $w['note'];
}, [
	'merge_query' => $rx_task_query,
	'stat_rows' => $rx_task_count,
	'page_rows' => 21
] )->set(function( $q ) use( $rx_task_count )
{
	$q['style'] = 'margin:21px auto';
	$q->thead->tr->add_before( 'tr' )->add( 'td' )->set_attr( 'colspan', 10 )->ins_filter([
		'userid' => '创建员工',
		'describe' => '描述',
		'pkno' => '设备编号',
		'note' => '详细' ] );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', 10 );
	$w = &$q->thead->tr[1]->td->form->dl->dt;

	$q = $w->tag_button( '提交任务' );
	$q['onclick'] = '$.window_open("?/rx_erp(1)rd_serverrd(2)(6)","rd_services_device",{width:600,height:600})';
	$q['class'] = 'r';

	$w->tag_button( '所有等待维护记录' )['onclick'] = 'return wa.query_act({2:"wait_start"})';
	$w->tag_button( '未验收的维护记录' )['onclick'] = 'return wa.query_act({2:"wait_done"})';
	$w->tag_button( '验收历史维护记录' )['onclick'] = 'return wa.query_act({2:"done_all"})';


	$w->add( 'span', '共找到 ' . $rx_task_count . ' 个记录' )->set_class( 'a_c000' );
	
	
	
	$txt='123666';
	$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
$txt =json_encode( $txt, JSON_UNESCAPED_UNICODE );
fwrite($myfile, $txt);
});
goto rd_end;

rd_ip0_admin:
$rd_ip0_field = [
	'ip_prefix_address' => FALSE,
	0 => '删除',
	//'time' => [ 'desc', '时间' ],
	'only' => [ TRUE, 'IP（查看）' ],
	'ip_suffix_start' => [ FALSE, '开始' ],
	'ip_suffix_end' => [ FALSE, '结束' ],
	'netmask' => [ FALSE, '掩码' ],
	'gateway' => [ FALSE, '网关' ],
	'network_id' => [ FALSE, '网络号' ],
	'broadcast_address' => [ FALSE, '广播地址' ],
	'last_update_time' => [ 'desc', '最后更新时间' ],
	'last_update_userid' => [ FALSE, '最后更新员工' ],
	'note' => [ FALSE, '备注' ],
	2 => '更新'
];
$rd_ip0_filter['ip_prefix_address'] = 'IP 前缀地址';
foreach ( $rd_ip0_field as $q => $w )
{
	is_array( $w ) && $rd_ip0_filter[ $q ] = $w[1];
};
unset( $rd_ip0_filter['only'] );
$rd_ip0_query = wa::get_filter( array_keys( $rd_ip0_filter ) );
$rd_ip0_count = wa::$sql->get_rows( 'rx_rd_ip0', $rd_ip0_query );
wa::htt_data_table( 'rx_rd_ip0', $rd_ip0_field, function( $q, $w )
{
	$e = $q->add( 'td' )->tag_a( '删除', '?/rx_erp/ajax(1)rd_ip0_delete(2)' . $w['only'] );
	$e['data-confirm'] = '删除该段所有 IP（处于空闲状态）, 不能撤消！';
	$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	//$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) );
	$q->add( 'td' )->tag_a( $w['ip_prefix_address'], '?/rx_erp(1)rd_ip1_list(2)' . $w['only'] . substr( 0 . dechex( $w['ip_suffix_end'] ), -2 ) );
	$q->add( 'td', $w['ip_suffix_start'] );
	$q->add( 'td', $w['ip_suffix_end'] );
	$q->add( 'td', $w['netmask'] );
	$q->add( 'td', $w['gateway'] );
	$q->add( 'td', $w['network_id'] );
	$q->add( 'td', $w['broadcast_address'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['last_update_time'] ) );
	$q->add( 'td', $w['last_update_userid'] );
	$q->add( 'td', $w['note'] );
	$q->add( 'td' )->tag_a( '更新', '?/rx_erp(1)rd_ip0_update(2)' . $w['only'] );
}, [
	'merge_query' => $rd_ip0_query,
	'stat_rows' => $rd_ip0_count,
	'page_rows' => 21
] )->set(function( $q ) use( &$rd_ip0_filter, $rd_ip0_count )
{
	$q['style'] = 'margin:21px auto';
	$e = count( $q->thead->tr->td );
	$w = $q->thead->tr->add_before( 'tr' )->set_attr( 'style', 'background:#fff' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $rd_ip0_filter, [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$w->tag_button( '添加 IP' )['onclick'] = '$.go("?/rx_erp(1)rd_ip0_insert")';
	$w->tag_button( '查看所有 IP' )['onclick'] = '$.go("?/rx_erp(1)rd_ip1_list")';
	$w->tag_input()->set_attrs([
		'placeholder' => '输入后按 Enter 键开始搜索',
		'style' => 'width:210px',
		'name' => 'search',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_ip0_search(this)'
	]);
	$w->add( 'span', '共找到 ' . $rd_ip0_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rd_ip0_update:
if ( isset( $_GET[2] ) && ( $rd_form_ip0_data = wa::$sql->get_only( 'rx_rd_ip0', 'only', $_GET[2] ) ) )
{
	unset( $rd_form_ip0_editor['ip_prefix_address'] );
	unset( $rd_form_ip0_editor['ip_suffix_start'] );
	unset( $rd_form_ip0_editor['ip_suffix_end'] );
	goto rd_ip0_insert;
};
goto rd_end;

rd_ip0_insert:
$rd_form_ip0_editor['status']['value'] += array_combine( $rd_form_ip1_status, $rd_form_ip1_status );
wa::htt_form_post( $rd_form_ip0_editor, isset( $rd_form_ip0_data ) ? $rd_form_ip0_data : [
	'ip_suffix_start' => 0,
	'ip_suffix_end' => 255,
	'netmask' => '255.255.255.0'
] )->set(function( $q ) use( &$rd_form_ip0_data )
{
	if ( isset( $rd_form_ip0_data ) )
	{
		$q['action'] = '?/rx_erp/ajax(1)rd_ip0_update(2)' . $rd_form_ip0_data['only'];
		$q->table->thead->tr->td = join([
			'IP:' . $rd_form_ip0_data['ip_prefix_address'],
			'.' . $rd_form_ip0_data['ip_suffix_start'],
			'~' . $rd_form_ip0_data['ip_suffix_end']
		]);
		$w = 2;
	}
	else
	{
		$q['action'] = '?/rx_erp/ajax(1)rd_ip0_insert';
		$w = $q->table->tbody->xpath( '//input' );
		$w[2][ 'onfocus' ] = $w[1][ 'onfocus' ] = $w[0][ 'onfocus' ] = 'wa.over_input(this,rd_form_ip0_input)';
		$w = 5;
	};
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
	$q->table->tbody->tr[ $w + 1 ]->td[1]->div->input['style'] = $q->table->tbody->tr[ $w ]->td[1]->div->input['style'] = 'background:#eee';
	$q->table->tbody->tr[ $w + 1 ]->td[1]->div->input['readonly'] = $q->table->tbody->tr[ $w ]->td[1]->div->input['readonly'] = TRUE;
});
goto rd_end;

rd_ip1_list:
$rd_ip1_field = [
	'ip' => FALSE,
	'only' => [ 'asc', 'IP 地址' ],
	'select' => [ TRUE, '使用范围' ],
	'device' => [ TRUE, '设备编号' ],
	'status' => [ TRUE, '状态' ],
	'note' => [ TRUE, '备注' ],
	'last_update_time' => [ TRUE, '最后更新时间' ],
	'last_update_userid' => [ TRUE, '最后更新员工' ]
];
$rd_ip1_filter['ip'] = 'IP 地址';
foreach ( $rd_ip1_field as $q => $w )
{
	is_array( $w ) && $rd_ip1_filter[ $q ] = $w[1];
};
unset( $rd_ip1_filter['only'] );
$rd_ip1_query = wa::get_filter( array_keys( $rd_ip1_filter ), isset( $_GET[2] ) && strlen( $_GET[2] ) == 10
	? [
		'only>=' . wa::$sql->escape( substr( $_GET[2], 0, 8 ) ),
		'only<=' . wa::$sql->escape( substr( $_GET[2], 0, 6 ) . substr( $_GET[2], -2 ) )
	] : [] );
$rd_ip1_count = wa::$sql->get_rows( 'rx_rd_ip1', $rd_ip1_query );
wa::htt_data_table( 'rx_rd_ip1', $rd_ip1_field, function( $q, $w )
{
	$q->add( 'td' )->tag_a( $w['ip'], $w['device'] ? '#' : '?/rx_erp(1)rd_ip1_edit(2)' . $w['only'] );
	$w['select'] === NULL ? $q->add( 'td', '所有' )['style'] = 'color:silver' : $q->add( 'td', $w['select'] );
	$w['device'] === NULL ? $q->add( 'td', '未设置' )['style'] = 'color:silver' : $q->add( 'td', $w['device'] );
	$q->add( 'td', $w['status'] );
	$q->add( 'td', $w['note'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['last_update_time'] ) );
	$q->add( 'td', $w['last_update_userid'] );
}, [
	'merge_query' => $rd_ip1_query,
	'stat_rows' => $rd_ip1_count,
	'page_rows' => 21
] )->set(function( $q ) use( &$rd_ip1_filter, &$rd_form_ip1_status, $rd_ip1_count )
{
	$q['style'] = 'margin:21px auto';
	$e = count( $q->thead->tr->td );
	$w = $q->thead->tr->add_before( 'tr' )->set_attr( 'style', 'background:#fff' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $rd_ip1_filter, [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$w->tag_select(
		[ '' => '所有状态' ] + array_combine( $rd_form_ip1_status, $rd_form_ip1_status ),
		isset( $_GET[7] ) && preg_match( '/status\.eq\.([^\/]+)/', urldecode( $_GET[7] ), $e ) ? $e[1] : NULL
	)->set_attr( 'onchange', 'wa.query_act({7:this.value?"status.eq."+$.urlencode(this.value):null})' );
	$w->add( 'span', '共找到 ' . $rd_ip1_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rd_ip1_edit:
$rd_form_ip1_editor['status']['test'][] = '/^(' . join( '|', $rd_form_ip1_status ) . ')$/';
$rd_form_ip1_editor['select']['value'] += array_combine( $rd_form_device_list_type, $rd_form_device_list_type );
$rd_form_ip1_editor['status']['value'] = array_combine( $rd_form_ip1_status, $rd_form_ip1_status );
isset( $_GET[2] )
&& ( $rd_form_ip1_data = wa::$sql->get_only( 'rx_rd_ip1', 'only', $_GET[2] ) )
&& wa::htt_form_post( $rd_form_ip1_editor, $rd_form_ip1_data )->set(function( $q ) use( &$rd_form_ip1_data, &$rd_form_device_list_type )
{
	$q['action'] = '?/rx_erp/ajax(1)rd_ip1_edit(2)' . $rd_form_ip1_data['only'];
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
	$w = &$q->table->thead->tr->td;
	$e = rd_ip1_only_ip0( $rd_form_ip1_data['only'] );
	$w->add( 'div' )->tag_button( '返回该 IP 所在段的列表页面' )->set_attrs([
		'onclick' => '$.go("?/rx_erp(1)rd_ip1_list(2)' . $e['only'] . substr( 0 . dechex( $e['ip_suffix_end'] ), -2 ) . '")',
		'class' => 'b'
	]);
	$w->add( 'pre', join( "\n", [
		'网络ＩＤ : ' . $e['network_id'],
		'广播地址 : ' . $e['broadcast_address'],
		'ＩＰ地址 : ' . $rd_form_ip1_data['ip'],
		'ＩＰ掩码 : ' . $e['netmask'],
		'ＩＰ网关 : ' . $e['gateway']
	] ) )['style'] = 'padding:8px 42px;font-size:14px;line-height:28px';
	$e = $q->table->tbody->tr[2]->td[1]->div;
	$e->input['placeholder'] = '备注信息最多可以输入64个字符';
	$e->input['list'] = 'rd_staff_list';
	$e = $e->add( 'datalist' );
	$e['id'] = 'rd_staff_list';
	foreach( rx_get_staff( '资源部' ) as $r => $t )
	{
		$y = $e->add( 'option' );
		$y['value'] = $t['name'];
		$y['label'] = $r;
	};
});
goto rd_end;

rd_forcer_device:
$rd_forcer_device_field = [
	'room' => FALSE,
	'ip_main' => FALSE,
	'ip_vice' => FALSE,
	'status' => FALSE,
	0 => '删除',
	'only' => [ FALSE, '设备编号' ],
	'note' => [ FALSE, '机柜编号' ],
	'time' => [ FALSE, '添加时间' ],
	'sb_type' => [ FALSE, '机型' ],
	'hold' => [ FALSE, '产权' ],
	1 => 'IP列表',
	'useu' => [ FALSE, 'U数' ],
	'link' => [ FALSE, '关联编号' ],
	'pw_time' => [ FALSE, '更新时间' ],
	'pw_editid' => [ FALSE, '更新员工' ],
	2 => '操作'
];
$rd_forcer_device_query = wa::get_filter( array_keys( array_filter( $rd_forcer_device_field, 'is_array' )  ) );
$rd_forcer_device_count = wa::$sql->get_rows( 'rx_rd_device', $rd_forcer_device_query );
wa::htt_data_table( 'rx_rd_device', $rd_forcer_device_field, function( $q, $w )
{
	static $bgcolor;
	$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
	if ( $w['status'] === '机柜设备' )
	{
		$q['style'] = 'background:#ffe0e0';
		$window_open = TRUE;
	};

	$e = &$q->td;
	$e->tag_a( '删除', isset( $window_open )
		? '?/rx_erp/ajax(1)rd_device_update(2)' . $w['room'] . '(6)' . $w['only']
		: '?/rx_erp/ajax(1)rd_device_delete(2)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href)';
	$q->td[] = $w['only'];
	$e = &$q->td[];
	$e->tag_a( $w['note'], '?/rx_erp(1)rd_forcer_admin(7)name.eq.' . $w['note'] );
	$q->td[] = date( 'Y-m-d H:i:s', $w['time'] );
	$q->td[] = $w['sb_type'];
	$q->td[] = $w['hold'];
	$e = &$q->td[];
	$e->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['style'] = 'color:red';
	$e->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );

	$q->td[] = $w['useu'];
	$q->td[] = $w['link'];
	$q->td[] = date( 'Y-m-d H:i:s', $w['pw_time'] );
	$q->td[] = $w['pw_editid'];

	$e = &$q->td[];
	if ( isset( $window_open ) )
	{
		$e->tag_a( '更新', '?/rx_erp(1)rd_device_update(2)' . $w['room'] . '(9)' . $w['only'] )['onclick'] = 'return rd_device_add_device(this.href)';
	}
	else
	{
		$e->tag_a( '更新', '?/rx_erp(1)rd_device_update(2)' . $w['only'] );
	};

}, [
	'merge_query' => $rd_forcer_device_query,
	'stat_rows' => $rd_forcer_device_count
] )->set(function( $q ) use(
	&$rd_forcer_device_field,
	&$rd_hold_list,
	&$rd_form_device_list_type,
	$rd_forcer_device_count )
{
	$q['style'] = 'margin:21px auto';
	$w = count( $q->thead->tr->td );
	$e = [];
	foreach ( $rd_forcer_device_field as $r => $t )
	{
		is_array( $t ) && $e[ $r ] = $t[1];
	};

	$r = $q->thead->tr->add_before( 'tr' );
	$t = $r->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );

	$e = $t->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( $e, [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;

	$r = isset( $_GET[7] ) ? urldecode( $_GET[7] ) : '';
	$e->tag_select(
		[ '' => '全部' ] + array_combine( $rd_hold_list, $rd_hold_list ),
		preg_match( '/hold\.eq\.([^\/]+)/', $r, $t ) ? $t[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("hold",this)' );
	$e->tag_select(
		[ '' => '所有设备' ] + array_combine( $rd_form_device_list_type, $rd_form_device_list_type ),
		preg_match( '/sb_type\.eq\.([^\/]+)/', $r, $t ) ? $t[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("sb_type",this)' );
	$e->tag_select( [
		'' => '所有备注设备',
		'LT%' => '所有连体机'
		], preg_match( '/link\.like\.([^\/]+)/', $r, $t ) ? $t[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("link",this,"like")' );


	$e->span = '共找到 ' . $rd_forcer_device_count . ' 个记录';
});
goto rd_end;

rd_forcer_admin:
$rd_forcer_field = [
	'only' => FALSE,
	'max_0u' => FALSE,
	'max_1u' => FALSE,
	0 => '删除',
	//'time' => [ 'desc', '时间' ],
	'name' => [ 'asc', '机柜编号' ],
	'hold' => [ TRUE, '产权' ],
	'room' => [ FALSE, '所在机房' ],
	'status' => [ TRUE, '状态' ],
	'uses' => [ FALSE, '用途' ],
	'sur_0u' => [ FALSE, '刀片机位(剩余)' ],
	'sur_1u' => [ FALSE, '1U机位(剩余)' ],
	

	'note' => [ FALSE, '备注' ],
	'last_update_time' => [ FALSE, '最后更新时间' ],
	'last_update_userid' => [ FALSE, '最后更新员工' ],
	1 => '更新'
];
$rd_forcer_query = wa::get_filter( array_keys( array_filter( $rd_forcer_field, 'is_array' ) ) );
$rd_forcer_count = wa::$sql->get_rows( 'rx_rd_forcer', $rd_forcer_query );
wa::htt_data_table( 'rx_rd_forcer', $rd_forcer_field, function( $q, $w )
{
	$q->add( 'td' )->tag_a( '删除', '?/rx_erp/ajax(1)rd_forcer_delete(2)' . $w['only'] )->set_attrs([
		'data-confirm' => '不能撤消',
		'onclick' => 'return wa.ajax_query(this.href,this.dataset)'
	]);
	//$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) );
	$q->add( 'td', $w['name'] );
	$q->add( 'td', $w['hold'] );
	$q->add( 'td', $w['room'] );

	$q->add( 'td', $w['status'] )['style'] = $w['status'] == '空闲' ? 'color:red' : 'color:green';
	$q->add( 'td', $w['uses'] );

	$q->add( 'td', sprintf( '%d / %d', $w['max_0u'], $w['sur_0u'] ) );
	$q->add( 'td', sprintf( '%d / %d', $w['max_1u'], $w['sur_1u'] ) );

	$q->add( 'td', strlen( $w['note'] ) > 42 ? wa::str_omit( $w['note'] ) : $w['note'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['last_update_time'] ) );
	$q->add( 'td', $w['last_update_userid'] );
	$q->add( 'td' )->tag_a( '更新', '?/rx_erp(1)rd_forcer_update(2)' . $w['only'] );
}, [
	'merge_query' => $rd_forcer_query,
	'stat_rows' => $rd_forcer_count,
	'page_rows' => 21
] )->set(function( $q ) use(
	&$rd_forcer_field,
	&$rd_hold_list,
	&$rd_room_list,
	$rd_forcer_count )
{
	$q['style'] = 'margin:21px auto';
	foreach ( $rd_forcer_field as $w => $e )
	{
		is_array( $e ) && $r[ $w ] = $e[1];
	};
	$e = count( $q->thead->tr->td );
	$w = $q->thead->tr->add_before( 'tr' )->set_attr( 'style', 'background:#fff' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $r, [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$w->tag_button( '添加机柜' )['onclick'] = '$.go("?/rx_erp(1)rd_forcer_insert")';
	$w->tag_button( '机柜设备列表' )['onclick'] = '$.go("?/rx_erp(1)rd_forcer_device")';
	$e = isset( $_GET[7] ) ? urldecode( $_GET[7] ) : '';
	$w->tag_select(
		[ '' => '全部' ] + array_combine( $rd_hold_list, $rd_hold_list ),
		preg_match( '/hold\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("hold",this)' );
	$w->tag_select(
		[ '' => '所有机房' ] + array_combine( $rd_room_list, $rd_room_list ),
		preg_match( '/room\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("room",this)' );
	$w->tag_select(
		[ '' => '所有状态', '空闲' => '空闲', '使用' => '使用' ],
		preg_match( '/status\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("status",this)' );
	$w->add( 'span', '共找到 ' . $rd_forcer_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rd_forcer_update:
if ( isset( $_GET[2] ) && ( $rd_form_forcer_data = wa::$sql->get_only( 'rx_rd_forcer', 'only', $_GET[2] ) ) )
{
	if ( $rd_form_forcer_data['inip'] )
	{
		$rd_form_forcer_data['inip'] = explode( "\n", $rd_form_forcer_data['inip'] );
		$rd_form_forcer_data['inip'] = array_combine( $rd_form_forcer_data['inip'], $rd_form_forcer_data['inip'] );
	}
	else
	{
		$rd_form_forcer_data['inip'] = [];
	};
};

rd_forcer_insert:
$rd_form_forcer_editor['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
$rd_form_forcer_editor['hold']['value'] = array_combine( $rd_hold_list, $rd_hold_list );
$rd_form_forcer_editor['room']['test'][] = '/^(' . join( '|', $rd_room_list ) . ')$/';
$rd_form_forcer_editor['room']['value'] += array_combine( $rd_room_list, $rd_room_list );
wa::htt_form_post( $rd_form_forcer_editor, isset( $rd_form_forcer_data ) ? $rd_form_forcer_data : [ 'c_0u' => 0, 'c_1u' => 0 ] )->set(function( $q ) use( &$rd_form_forcer_data )
{
	$q['action'] = '?/rx_erp/ajax(1)rd_forcer_' . ( isset( $rd_form_forcer_data ) ? 'update(2)' . $rd_form_forcer_data['only'] : 'insert' );
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
	//$q->table->tbody->tr[7]->td[1]->table->thead->tr->td->div->input['placeholder'] = '请输入IP段';
	$q->table->tbody->tr[7]->td[1]->div->textarea['style'] = 'line-height:18px;height:210px';
});
if ( isset( $rd_form_forcer_data ) )
{
	$q = wa::htt_data_table( 'rx_rd_device', [
		'room' => FALSE,
		'ip_main' => FALSE,
		'ip_vice' => FALSE,
		'status' => FALSE,
		0 => '删除',
		'only' => [ FALSE, '设备编号' ],
		'time' => [ 'desc', '添加时间' ],
		'hold' => [ FALSE, '产权' ],
		'sb_type' => [ FALSE, '设备类类型' ],
		'bandwidth' => [ FALSE, '带宽' ],
		1 => 'IP列表',
		'useu' => [ FALSE, 'U数' ],
		'pw_time' => [ FALSE, '更新时间' ],
		'pw_editid' => [ FALSE, '更新员工' ],
		2 => '更新'
	], function( $q, $w )
	{
		if ( $w['status'] === '机柜设备' )
		{
			$q['class'] = 'a_bfee';
			$window_open = TRUE;
		};
		$e = &$q->td;

		$e->tag_a( '删除', isset( $window_open )
			? '?/rx_erp/ajax(1)rd_device_update(2)' . $w['room'] . '(6)' . $w['only']
			: '?/rx_erp/ajax(1)rd_device_delete(2)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href)';
		$q->td[] = $w['only'];
		$q->td[] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['hold'];
		$q->td[] = $w['sb_type'];
		$q->td[] = $w['bandwidth'];
		$e = &$q->td[];
		$e->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['style'] = 'color:red';
		$e->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
		$q->td[] = $w['useu'];
		$q->td[] = date( 'Y-m-d H:i:s', $w['pw_time'] );
		$q->td[] = $w['pw_editid'];
		$e = &$q->td[];
		if ( isset( $window_open ) )
		{
			$e->tag_a( '更新', '?/rx_erp(1)rd_device_update(2)' . $w['room'] . '(9)' . $w['only'] )['onclick'] = 'return rd_device_add_device(this.href)';
		}
		else
		{
			$e->tag_a( '更新', '?/rx_erp(1)rd_device_update(2)' . $w['only'] );
		};
	}, [
		'merge_query' => 'where note="' . $rd_form_forcer_data['name'] . '"',
		'page_rows' => 99
	] );
	$q['class'] = 'wa_grid_table';
	$q['style'] = 'margin:21px auto';
};
goto rd_end;

rd_device_admin:
$rd_device_field = [
	'ip_main' => FALSE,
	'ip_vice' => FALSE,
	'sb_info' => FALSE,
	'pw_userid' => FALSE,
	'pw_team' => FALSE,
	'pw_starts' => TRUE,
	
	'pw_expire' => TRUE,

	'pw_of' => FALSE,
	'pw_be' => FALSE,
	'pw_name' => FALSE,
	'pw_tel' => FALSE,
	'pw_imid' => FALSE,
	'pw_note' => FALSE,
	0 => '删除',
	'time' => [ TRUE, '录入时间' ],
	'only' => [ TRUE, '设备编号' ],
	'sb_type' => [ FALSE, '设备类型' ],
	'hold' => [ FALSE, '产权' ],
	'room' => [ FALSE, '所在机房' ],
	'note' => [ FALSE, '设备备注' ],
	'bandwidth' => [ FALSE, '带宽' ],

	'defense' => [ FALSE, '防御' ],
	'status' => [ FALSE, '状态' ],
	
	'pw_time' => [ 'desc', '更新时间' ],
	'pw_editid' => [ FALSE, '更新员工' ],
	1 => '操作'
];
foreach ( $rd_device_field as $q => $w )
{
	is_array( $w ) && $rd_device_field_filter[ $q ] = $w[1];
};

$rd_device_field_filter['pw_expire'] = '到期时间';
$rd_device_field_filter += [
	'ip_main' => 'IP主',
	'ip_vice' => 'IP副',
	'sb_info' => '设备信息',
	'pw_userid' => '业务员工',
	'pw_team' => '业务团队',
	'pw_starts' => '开通时间',
	//'pw_expire' => '到期时间',
	'pw_of' => '订单编号',
	'pw_be' => '关联信息',
	'pw_name' => '联系名称',
	'pw_tel' => '联系电话',
	'pw_imid' => '联系QQ',
	'pw_note' => '业务备注'
];
if ( isset( $_GET[6] ) )
{
	$q = wa::$sql->escape( '%' . $_GET[6] . '%' );
	$rd_device_query = wa::get_filter( array_keys( $rd_device_field_filter ), [ 'status!="机柜设备"', '(ip_main like ' . $q . ' or ip_vice like ' . $q . ')' ] );
}
else
{
	$rd_device_query = wa::get_filter( array_keys( $rd_device_field_filter ), [ 'status!="机柜设备"' ] );
};
$rd_device_count = wa::$sql->get_rows( 'rx_rd_device', $rd_device_query );
$rd_staff_list = rx_get_staff( '资源部' );
wa::htt_data_table( 'rx_rd_device', $rd_device_field, function( $q, $w ) use( &$rd_staff_list )
{
	static $bgcolor;
	$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
	$e = $q->add( 'td' )->tag_a( '删除', '?/rx_erp/ajax(1)rd_device_delete(2)' . $w['only'] );
	$e['data-confirm'] = '删除这个设备并且释放IP';
	$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) );
	$q->add( 'td', $w['only'] );
	$q->add( 'td', $w['sb_type'] );
	$q->add( 'td', $w['hold'] );
	$q->add( 'td', $w['room'] );
	$q->add( 'td' )->tag_a( $w['note'], '?/rx_erp(1)rd_forcer_admin(7)name.eq.' . $w['note'] );
	$q->add( 'td', $w['bandwidth'] );
	$q->add( 'td', $w['defense'] );
	$q->add( 'td', $w['status'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['pw_time'] ) );
	$q->add( 'td', isset( $rd_staff_list[ $w['pw_editid'] ] ) ? $rd_staff_list[ $w['pw_editid'] ]['name'] : $w['pw_editid'] );

	$e = $q->add( 'td' );
	$e->tag_a( '设备更新', '?/rx_erp(1)rd_device_update(2)' . $w['only'] );
	$e->add( 'span', ' | ' );
	$e->tag_a( '状态更新', '?/rx_erp(1)rd_device_status(2)' . $w['only'] );
	$e->add( 'span', ' | ' );
	$e->tag_a( '提交回收设备任务', '?/rx_erp(1)rd_services_device(2)' . $w['only'] )['onclick'] = 'return !$.window_open(this.href,"rd_services_device",{width:600,height:600})';

	//$e['data-prompt'] = '请输入附加说明!';
	//$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	$e = $q->get_parent()->add( 'tr' );
	$e['style'] = 'line-height:20px;background:' . $bgcolor;
	$r = $e->add( 'td', "IP:\n" );
	$r['colspan'] = 2;
	$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
	$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );

	$r = $e->add( 'td' )->set_attr( 'colspan', 4 );
	$r->pre = '设备信息:';
	rd_json_info_display( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

	$r = [ '状态:' ];
	$w['pw_userid'] && $r[] = '业务员工: ' . ( isset( $rd_staff_list[ $w['pw_userid'] ] ) ? $rd_staff_list[ $w['pw_userid'] ]['name'] : $w['pw_userid'] );
	$w['pw_starts'] && $r[] = '开通时间: ' . date( 'Y-m-d H:i:s', $w['pw_starts'] );
	$w['pw_expire'] && $r[] = '到期时间: ' . date( 'Y-m-d H:i:s', $w['pw_expire'] );
	$w['pw_of'] && $r[] = '订单编号: ' . $w['pw_of'];
	$w['pw_be'] && $r[] = '关联信息: ' . $w['pw_be'];
	$w['pw_name'] && $r[] = '联系名称: ' . $w['pw_name'];
	$w['pw_tel'] && $r[] = '联系电话: ' . $w['pw_tel'];
	$e->add( 'td' )->set_attr( 'colspan', 4 )->add( 'pre', join( "\n", $r ) );
	$e->add( 'td' )->set_attr( 'colspan', 2 )->add( 'pre', "联系QQ:\n" . $w['pw_imid'] );
	$e->add( 'td' )->add( 'pre', "业务备注:\n" . $w['pw_note'] )->set_style( 'width:224px' );
}, [
	'merge_query' => $rd_device_query,
	'stat_rows' => $rd_device_count,
	'page_rows' => 21
] )->set(function( $q ) use(
	&$rd_device_field_filter,
	&$rd_hold_list,
	&$rd_room_list,
	&$rd_form_device_list_type,
	&$rd_form_device_list_status,
	&$rd_device_count )
{
	$q['style'] = 'margin:21px auto';
	$e = count( $q->thead->tr->td );
	$w = $q->thead->tr->add_before( 'tr' )->set_attr( 'style', 'background:#fff' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $rd_device_field_filter, [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$w->tag_button( '添加设备' )['onclick'] = '$.go("?/rx_erp(1)rd_device_insert")';
	$e = $w->add_before( 'div' );
	$e['style'] = 'padding-bottom:8px';
	$e->tag_button( '查测试设备时间最长排列' )->set_attrs([
		'style' => 'margin-right:8px',
		'onclick' => 'return wa.query_act({7:"status.eq.%E6%B5%8B%E8%AF%95",8:"pw_starts.asc"})'
	]);

	$e->tag_button( '打开' )->set_attrs([
		'style' => 'margin-right:8px',
		'onclick' => 'return $.go("?/rx_erp/ajax(1)rd_device_admin(2)0(7)status.eq.%E6%B5%8B%E8%AF%95","send_qq")'
	]);

	$e->tag_button( '查看所有已到期的设备,' )->set_attrs([
		'style' => 'margin-right:8px',
		'onclick' => 'return wa.query_act({7:"pw_expire.le.' . time() . '/status.regexp.%5E%28出租%7C托管%29%24",8:"pw_expire.asc"})'
	]);

	$e->tag_button( '打开' )->set_attrs([
		'style' => 'margin-right:8px',
		'onclick' => 'return $.go("?/rx_erp/ajax(1)rd_device_admin(2)1(7)pw_expire.le.' . mktime( 24, 0, 0 ) . '/status.regexp.%5E%28出租%7C托管%29%24","send_qq")'
	]);

	$e->tag_input()->set_attrs([
		'value' => isset( $_GET[6] ) ? $_GET[6] : '',
		'style' => 'width:160px',
		'placeholder' => '输入后按 Enter 键开始搜索',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);

	$e->tag_input()->set_attrs([
		'style' => 'width:60px;margin-left:8px',
		'placeholder' => 'CPU',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);
	$e->tag_input()->set_attrs([
		'style' => 'width:60px;margin-left:8px',
		'placeholder' => '内存',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);
	$e->tag_input()->set_attrs([
		'style' => 'width:60px;margin-left:8px',
		'placeholder' => '硬盘',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);



	$e = isset( $_GET[7] ) ? urldecode( $_GET[7] ) : '';
	$w->tag_select(
		[ '' => '全部' ] + array_combine( $rd_hold_list, $rd_hold_list ),
		preg_match( '/hold\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("hold",this)' );
	$w->tag_select(
		[ '' => '所有机房' ] + array_combine( $rd_room_list, $rd_room_list ),
		preg_match( '/room\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("room",this)' );
	$w->tag_select(
		[ '' => '所有设备' ] + array_combine( $rd_form_device_list_type, $rd_form_device_list_type ),
		preg_match( '/sb_type\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("sb_type",this)' );
	$w->tag_select(
		[ '' => '所有状态' ] + array_combine( $rd_form_device_list_status, $rd_form_device_list_status ),
		preg_match( '/status\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("status",this)' );
	$w->add( 'span', '共找到 ' . $rd_device_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rd_device_update:
if ( isset( $_GET[2] ) && ( $rd_form_device_data = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) ) )
{
	if ( wa::$sql->q_query( 'select * from rx_rd_task where done is null and pkno=?s limit 1', $_GET[2] )->fetch_assoc() )
	{
		//wa::$headers['Location'] = '?/rx_erp(1)rd_services(7)pkno.eq.' . $q['only'];
		wa::$buffers->write->div['style'] = 'text-align:center;padding:200px 0';
		wa::$buffers->write->div->div['style'] = 'font-size:18px;line-height:64px';
		wa::$buffers->write->div->div = '这个设备正在维护中，如果需要继续修改请在维护设备里（验收）或者（删除）这个设备！';
		$w = &wa::$buffers->write->div->div[];
		$e = $w->tag_button( '返回到这个设备管理' );
		$e['onclick'] = '$.go("?/rx_erp(1)rd_device_admin(7)only.eq.' . $_GET[2] . '")';
		$w->span = ' ';
		$e = $w->tag_button( '跳转到这个维护设备看看' );
		$e['onclick'] = '$.go("?/rx_erp(1)rd_services(7)pkno.eq.' . $_GET[2] . '")';
		exit;
	};
	
	$rd_form_device_data['ip_main'] = trim( $rd_form_device_data['ip_main'] ) ? explode( ' ', $rd_form_device_data['ip_main'] ) : [];
	$rd_form_device_data['ip_main'] = array_combine( $rd_form_device_data['ip_main'], $rd_form_device_data['ip_main'] );
	$rd_form_device_data['ip_vice'] = trim( $rd_form_device_data['ip_vice'] ) ? explode( ' ', $rd_form_device_data['ip_vice'] ) : [];
	$rd_form_device_data['ip_vice'] = array_combine( $rd_form_device_data['ip_vice'], $rd_form_device_data['ip_vice'] );
	goto rd_device_insert;
};
goto rd_end;

rd_device_insert:
if ( isset( $_GET[9] ) )
{
	if ( $_GET[9] && ( $q = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[9] ) ) )
	{
		$rd_form_device_device = $q;
		$rd_form_device_device['ip_main'] = trim( $rd_form_device_device['ip_main'] ) ? explode( ' ', $rd_form_device_device['ip_main'] ): [];
		$rd_form_device_device['ip_main'] = array_combine( $rd_form_device_device['ip_main'], $rd_form_device_device['ip_main'] );
		$rd_form_device_device['ip_vice'] = trim( $rd_form_device_device['ip_vice'] ) ? explode( ' ', $rd_form_device_device['ip_vice'] ) : [];
		$rd_form_device_device['ip_vice'] = array_combine( $rd_form_device_device['ip_vice'], $rd_form_device_device['ip_vice'] );
	};
	goto rd_device_add_device;
};
$rd_form_device_editor['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
$rd_form_device_editor['hold']['value'] = array_combine( $rd_hold_list, $rd_hold_list );
$rd_form_device_editor['room']['test'][] = '/^(' . join( '|', $rd_room_list ) . ')$/';
$rd_form_device_editor['room']['value'] += array_combine( $rd_room_list, $rd_room_list );
$rd_form_device_editor['sb_type']['test'][] = '/^(' . join( '|', $rd_form_device_list_type ) . ')$/';
$rd_form_device_editor['sb_type']['value'] += array_combine( $rd_form_device_list_type, $rd_form_device_list_type );
wa::htt_form_post( $rd_form_device_editor, isset( $rd_form_device_data ) ? $rd_form_device_data : [ 'useu' => 0 ] )->set(function( $q ) use( &$rd_form_device_data )
{
	$q['action'] = '?/rx_erp/ajax(1)rd_device_' . ( isset( $rd_form_device_data ) ? 'update(2)' . $rd_form_device_data['only'] : 'insert' );
	if ( isset( $rd_form_device_data ) )
	{
		rd_oplog( 'select', wa::$sql->get_only( 'rx_rd_device', 'only', $rd_form_device_data['only'] ) );
		$w = $q->table->thead->tr->td->add( 'div' );
		$w->tag_button( '返回 状态更新页面' )->set_attrs([
			'class' => 'b',
			'style' => 'margin:8px',
			'onclick' => '$.go("?/rx_erp(1)rd_device_status(2)' . $rd_form_device_data['only'] . '")'
		]);
		$w->tag_button( '返回 该设备管理页面' )->set_attrs([
			'class' => 'r',
			'style' => 'margin:8px',
			'onclick' => '$.go("?/rx_erp(1)rd_device_admin(7)only.eq.' . $rd_form_device_data['only'] . '")'
		]);
		$w = (object)[
			'index' => 5,
			'url' => 'rd_device_update(4)',
			'ip_main' => '',
			'ip_vice' => ''
		];
		if ( $rd_form_device_data['room'] === "美国 Digital Realty" )
		{
			$w->ip_main = 'rd_device_ip_input_toggle($.get("#ip_main_frame")).value="' . join( '\n', $rd_form_device_data['ip_main'] ) . '"';
			$w->ip_vice = 'rd_device_ip_input_toggle($.get("#ip_vice_frame")).value="' . join( '\n', $rd_form_device_data['ip_vice'] ) . '"';
		};
	}
	else
	{
		$w = (object)[
			'index' => 5,
			'url' => 'rd_device_insert(4)',
			'ip_main' => '',
			'ip_vice' => ''
		];
	};

	$q->table->tbody->tr->td[1]['style'] = 'width:420px';

	if ( isset( $rd_form_device_data ) && $rd_form_device_data['sb_type'] === '机柜' )
	{
		
		unset( $q->table->tbody->tr[5]->td[1]->select );
		$e = &$q->table->tbody->tr[5]->td[1]->div;
		$e['class'] = 'fix';
		$e = &$e->input;
		$e['type'] = 'text';
		$e['name'] = 'sb_type';
		$e['value'] = $rd_form_device_data['sb_type'];
		$e['readonly'] = $q->table->tbody->tr[3]->td[1]->div->input['readonly'] = TRUE;
	}
	else
	{
		$q->table->tbody->tr[3]->td[1]->div->input['placeholder'] = '请输入机柜编号';
		$e = $q->table->tbody->tr[3]->td[1]->div->input;
		$e['onfocus'] = 'wa.over_input(this,rd_form_device_note,this.nextSibling)';
		$e['list'] = 'rd_form_device_note';
		$e = $q->table->tbody->tr[3]->td[1]->div->add( 'datalist' );
		$e['id'] = 'rd_form_device_note';

		$q->table->tbody->tr[ $w->index ]->td[1]->select['onchange'] = 'rd_sb_type_onchange(this)';
		$q->table->tbody->tr[ $w->index ]->td[1]->select['id'] = 'sb_type_load';
	};




	$e = $q->table->tbody->tr[ $w->index + 4 ]->td[1]['id'] = 'ip_main_frame';
	$e = $q->table->tbody->tr[ $w->index + 4 ]->td[1]->table->thead->tr->td->div->input;
	$e['placeholder'] = '请输入主 IP 段';
	$e['data-url'] .= $w->url;


	$q->table->tbody->tr[ $w->index + 4 ]->td[2]->tag_button( '手动/自动' )['onclick'] = 'rd_device_ip_input_toggle(this.parentNode.previousSibling)';

	$e = $q->table->tbody->tr[ $w->index + 5 ]->td[1]['id'] = 'ip_vice_frame';
	$e = $q->table->tbody->tr[ $w->index + 5 ]->td[1]->table->thead->tr->td->div->input;
	$e['placeholder'] = '请输入副 IP 段';
	$e['data-url'] .= $w->url;

	$q->table->tbody->tr[ $w->index + 5 ]->td[2]->tag_button( '手动/自动' )['onclick'] = 'rd_device_ip_input_toggle(this.parentNode.previousSibling)';





	$q->tag_script( 'rd_sb_type_onchange($.get("form").sb_type);' . $w->ip_main . ';' . $w->ip_vice );
});
if ( isset( $rd_form_device_data ) && $rd_form_device_data['sb_type'] === '机柜' )
{
	wa::$buffers->a['name'] = 'sb_type_jg';
	$q = wa::htt_data_table( 'rx_rd_device', [
		'ip_main' => FALSE,
		'ip_vice' => FALSE,
		0 => '删除',
		'only' => [ FALSE, '设备编号' ],
		'time' => [ 'desc', '添加时间' ],
		'hold' => [ FALSE, '产权' ],
		'sb_type' => [ FALSE, '设备类类型' ],
		1 => 'IP列表',
		'useu' => [ FALSE, 'U数' ],
		'pw_time' => [ FALSE, '更新时间' ],
		'pw_editid' => [ FALSE, '更新员工' ],
		2 => ''
	], function( $q, $w )
	{
		$e = &$q->td;
		$e->tag_a( '删除', '?/rx_erp/ajax(1)rd_device_update(2)' . $_GET[2] . '(6)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href)';
		$q->td[] = $w['only'];
		$q->td[] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['hold'];
		$q->td[] = $w['sb_type'];
		$e = &$q->td[];
		$e->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['style'] = 'color:red';
		$e->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
		$q->td[] = $w['useu'];
		$q->td[] = date( 'Y-m-d H:i:s', $w['pw_time'] );
		$q->td[] = $w['pw_editid'];
		$e = &$q->td[];
		$e->tag_a( '更新', '?/rx_erp(1)rd_device_update(2)' . $_GET[2] . '(9)' . $w['only'] )['onclick'] = 'return rd_device_add_device(this.href)';
	}, [
		'merge_query' => 'where status="机柜设备" and room="' . $rd_form_device_data['only'] . '"',
		'page_rows' => 99
	] );
	$q['class'] = 'wa_grid_table';
	$q['style'] = 'margin:21px auto';
	$q['id'] = 'sb_type_jg';
	$q->thead->tr->td[9]->tag_a( '添加', '?/rx_erp(1)rd_device_update(2)' . $_GET[2] . '(9)' )['onclick'] = 'return rd_device_add_device(this.href)';
};
goto rd_end;

rd_device_add_device:
if ( !isset( $_GET[2] ) || !( $q = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) ) )
{
	goto rd_end;
};
$q = explode( ' ', trim( $q['ip_main'] . ' ' . $q['ip_vice'] ) );
$q = array_combine( $q, $q );
wa::$buffers->end['style'] = wa::$buffers->nav['style'] = 'display:none';
unset( wa::$htt->body->div->div[0] );
$rd_form_device_add_device['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
$rd_form_device_add_device['hold']['value'] = array_combine( $rd_hold_list, $rd_hold_list );
$rd_form_device_add_device['sb_type']['test'][] = '/^(' . join( '|', $rd_form_device_list_type ) . ')$/';
$rd_form_device_add_device['sb_type']['value'] += array_combine( $rd_form_device_list_type, $rd_form_device_list_type );
$rd_form_device_add_device['ip_vice']['value'] = $rd_form_device_add_device['ip_main']['value'] = $q;





unset( $rd_form_device_add_device['sb_type']['value']['机柜'] );
wa::htt_form_post( $rd_form_device_add_device, isset( $rd_form_device_device ) ? $rd_form_device_device : [] )->set(function( $q ) use( &$rd_form_device_device )
{
	$q['action'] = '?/rx_erp/ajax(1)rd_device_update(2)' . $_GET[2] . '(9)';
	$q->table->tbody->tr->td[1]['style'] = 'width:420px';
	if ( isset( $rd_form_device_device ) )
	{
		$q['action'] .= $rd_form_device_device['only'];

	}
	else
	{

	};


});


goto rd_end;


rd_device_status:
isset( $_GET[2] ) && wa::$sql->q_query( 'select * from rx_rd_device where only=?s limit 1', $_GET[2] )->fetch_callback(function( $q ) use(
	&$rd_form_device_status,
	&$rd_form_device_list_status )
{
	if ( wa::$sql->q_query( 'select * from rx_rd_task where done is null and pkno=?s limit 1', $q['only'] )->fetch_assoc() )
	{
		//wa::$headers['Location'] = '?/rx_erp(1)rd_services(7)pkno.eq.' . $q['only'];
		wa::$buffers->write->div['style'] = 'text-align:center;padding:200px 0';
		wa::$buffers->write->div->div['style'] = 'font-size:18px;line-height:64px';
		wa::$buffers->write->div->div = '这个设备正在维护中，如果需要继续修改请在维护设备里（验收）或者（删除）这个设备！';
		$w = &wa::$buffers->write->div->div[];
		$e = $w->tag_button( '返回到这个设备管理' );
		$e['onclick'] = '$.go("?/rx_erp(1)rd_device_admin(7)only.eq.' . $q['only'] . '")';
		$w->span = ' ';
		$e = $w->tag_button( '跳转到这个维护设备看看' );
		$e['onclick'] = '$.go("?/rx_erp(1)rd_services(7)pkno.eq.' . $q['only'] . '")';
		exit;
	};
	$rd_form_device_status['status']['test'][] = $q['hold'] == '锐讯'
		? array_slice( $rd_form_device_list_status, 1, 4 )
		: [ $rd_form_device_list_status[1], $rd_form_device_list_status[4], end( $rd_form_device_list_status ) ];
	$rd_form_device_status['status']['value'] = array_combine( $rd_form_device_status['status']['test'][2], $rd_form_device_status['status']['test'][2] );
	$rd_form_device_status['status']['test'][2] = '/^(' . join( '|', $rd_form_device_status['status']['test'][2] ) . ')$/';

	$q['pw_userid'] || $q['pw_userid'] = wa::$user['name'];
	$q['pw_team'] || $q['pw_team'] = wa::$user['rx_team'];
	$q['pw_starts'] || $q['pw_starts'] = time();
	$w = wa::htt_form_post( $rd_form_device_status, $q );
	$w['action'] = '?/rx_erp/ajax(1)rd_device_status(2)' . $q['only'];
	$w->table->tbody->tr->td[1]['style'] = 'width:280px';
	$e = $w->table->thead->tr->td->add( 'div' );
	$e->tag_button( '返回 设备更新页面' )->set_attrs([
			'class' => 'b',
			'style' => 'margin-right:8px',
			'onclick' => '$.go("?/rx_erp(1)rd_device_update(2)' . $q['only'] . '")'
	]);
	$e->tag_button( '返回 该设备管理页面' )->set_attrs([
			'class' => 'r',
			'style' => 'margin:8px',
			'onclick' => '$.go("?/rx_erp(1)rd_device_admin(7)only.eq.' . $q['only'] . '")'
	]);
	$e->tag_button( '清空状态' )->set_attrs([
			'class' => 'r',
			'style' => 'margin:8px',
			'data-confirm' => '该设备状态信息将会被清空!',
			'onclick' => 'wa.ajax_query("?/rx_erp/ajax(1)rd_device_emptys(2)' . $q['only'] . '",this.dataset)'
	]);
	$r = join( "\n", [
		'设备编号: ' . $q['only'],
		'所属产权: ' . $q['hold'],
		'所在位置: ' . $q['room'],
		'备注: ' . $q['note'],
		'带宽: ' . $q['bandwidth'] . ', 防御: ' . $q['defense'],
		'设备类型: ' . $q['sb_type']
	] );
	if ( $q['sb_type'] === '服务器' || $q['sb_type'] === '云设备' )
	{
		$q['sb_info'] = json_decode( $q['sb_info'], TRUE );
		isset( $q['sb_info']['账号'] ) || $q['sb_info']['账号'] = '';
		isset( $q['sb_info']['密码'] ) || $q['sb_info']['密码'] = '';


		$r .= "\n设备账号: " . $q['sb_info']['账号'] . "\n设备密码: " . $q['sb_info']['密码'];
	};

	$e->add( 'pre', $r )->set_style( 'line-height:28px;font-size:14px;white-space:pre-wrap;letter-spacing:1px' );

	$r = '';
	$q['ip_main'] && $r .= '主: ' . $q['ip_main'];
	$q['ip_vice'] && $r .= ( $r ? ' ' : '' ) . '副: ' . $q['ip_vice'];
	$e->add( 'textarea', strtr( $r, ' ', "\n" ) )->set_style( 'width:390px;height:140px' )->set_attr( 'disabled', TRUE );
	$e = $w->table->tbody->tr[1]->td[1]->div->input;
	$e['list'] = 'rd_form_device_status_userid';
	$e['onfocus'] = 'wa.over_input(this,rd_form_device_userid,this.nextSibling)';
	$e = $w->table->tbody->tr[1]->td[1]->div->add( 'datalist' );
	$e['id'] = 'rd_form_device_status_userid';

	// foreach ( rx_get_staff( '资源部' ) as $r => $t )
	// {
	// 	$y = $e->add( 'option' );
	// 	$y['value'] = $r;
	// 	$y['label'] = $t['name'];
	// };
	$w->table->tbody->tr[4]->td[1]->div->input['onfocus'] = $w->table->tbody->tr[3]->td[1]->div->input['onfocus'] = 'wa.input_time(this,"U")';

});
goto rd_end;

rd_services_device:
wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';
if ( isset( $_GET[2] ) && wa::$sql->q_query( 'select * from rx_rd_task where done is null and pkno=?s limit 1', $_GET[2] )->fetch_assoc() )
{
	wa::$buffers->write->div['style'] = 'text-align:center;padding:200px 0';
	wa::$buffers->write->div->div['style'] = 'font-size:18px;line-height:64px';
	wa::$buffers->write->div->div = '这个设备正在维护中！！！';
	$w = &wa::$buffers->write->div->div[];
	$e = $w->tag_button( '跳转到这个维护设备看看' );
	$e['onclick'] = 'window.opener.location.href="?/rx_erp(1)rd_services(7)pkno.eq.' . $_GET[2] . '";window.close()';
	goto rd_end;
};
isset( $_GET[2] ) && wa::$sql->q_query( 'select * from rx_rd_device where only=?s limit 1', $_GET[2] )->fetch_callback(function( $q ) use( &$rd_services_editor )
{
	$w = wa::htt_form_post( $rd_services_editor );
	$w['action'] = '?/rx_erp/ajax(1)rd_services_device(2)' . $q['only'];
	$w->table->tbody->tr->td[1]['style'] = 'width:320px';
	$e = $w->table->thead->add( 'tr' )->add( 'td' );
	$e['colspan'] = 3;
	$e->add( 'pre', join( "\n", [
		'设备编号: ' . $q['only'],
		'所属产权: ' . $q['hold'],
		'所在位置: ' . $q['room'],
		'备注: ' . $q['note'],
		'带宽: ' . $q['bandwidth'] . ', 防御: ' . $q['defense'],
		'设备类型: ' . $q['sb_type']
	] ) )->set_style( 'line-height:28px;font-size:14px;white-space:pre-wrap;letter-spacing:1px' );
	$e = $w->table->tbody->tr[1]->td[1]->div;
	$e->input['value'] = '回收设备';
	$e->input['readonly'] = TRUE;
	// $e->input['placeholder'] = '简单的描述问题以便工作人员更快的进行服务';
	// $e->input['value'] = wa::$user['name'];
	// $e->input['list'] = 'rd_services_describe';
	// $e = $e->add( 'datalist' );
	// $e['id'] = 'rd_services_describe';
	// $e->add( 'option' )['value'] = '回收设备';
	// $e->add( 'option' )['value'] = '重新启动';
	// $e->add( 'option' )['value'] = '加内存扩展设备';
	// $e->add( 'option' )['value'] = '破解系统账号密码';
	// $e->add( 'option' )['value'] = '重新安装操作系统';
	// $e->add( 'option' )['value'] = '其他维护';

	
	$w->table->tbody->tr[2]->td[1]->div->textarea['style'] = 'height:160px';


});
goto rd_end;

rd_delete_record:
$rd_device_field = [
	'ip_main' => FALSE,
	'ip_vice' => FALSE,
	'sb_info' => FALSE,
	'pw_userid' => FALSE,
	'pw_team' => FALSE,
	'pw_starts' => TRUE,
	
	'pw_expire' => TRUE,

	'pw_of' => FALSE,
	'pw_be' => FALSE,
	'pw_name' => FALSE,
	'pw_tel' => FALSE,
	'pw_imid' => FALSE,
	'pw_note' => FALSE,
	'time' => [ TRUE, '录入时间' ],
	'only' => [ TRUE, '设备编号' ],
	'sb_type' => [ FALSE, '设备类型' ],
	'hold' => [ FALSE, '产权' ],
	'room' => [ FALSE, '所在机房' ],
	'note' => [ FALSE, '设备备注' ],
	'bandwidth' => [ FALSE, '带宽' ],

	'defense' => [ FALSE, '防御' ],
	'status' => [ FALSE, '状态' ],
	
	'pw_time' => [ 'desc', '更新时间' ],
	'pw_editid' => [ FALSE, '更新员工' ]
];
foreach ( $rd_device_field as $q => $w )
{
	is_array( $w ) && $rd_device_field_filter[ $q ] = $w[1];
};

$rd_device_field_filter['pw_expire'] = '到期时间';
$rd_device_field_filter += [
	'ip_main' => 'IP主',
	'ip_vice' => 'IP副',
	'sb_info' => '设备信息',
	'pw_userid' => '业务员工',
	'pw_team' => '业务团队',
	'pw_starts' => '开通时间',
	//'pw_expire' => '到期时间',
	'pw_of' => '订单编号',
	'pw_be' => '关联信息',
	'pw_name' => '联系名称',
	'pw_tel' => '联系电话',
	'pw_imid' => '联系QQ',
	'pw_note' => '业务备注'
];
if ( isset( $_GET[6] ) )
{
	$q = wa::$sql->escape( '%' . $_GET[6] . '%' );
	$rd_device_query = wa::get_filter( array_keys( $rd_device_field_filter ), [ 'status!="机柜设备"', '(ip_main like ' . $q . ' or ip_vice like ' . $q . ')' ] );
}
else
{
	$rd_device_query = wa::get_filter( array_keys( $rd_device_field_filter ), [ 'status!="机柜设备"' ] );
};
$rd_device_count = wa::$sql->get_rows( 'rx_rd_delete', $rd_device_query );
$rd_staff_list = rx_get_staff( '资源部' );
wa::htt_data_table( 'rx_rd_delete', $rd_device_field, function( $q, $w ) use( &$rd_staff_list )
{
	static $bgcolor;
	$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );

	$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) );
	$q->add( 'td', $w['only'] );
	$q->add( 'td', $w['sb_type'] );
	$q->add( 'td', $w['hold'] );
	$q->add( 'td', $w['room'] );
	$q->add( 'td', $w['note'] );
	$q->add( 'td', $w['bandwidth'] );
	$q->add( 'td', $w['defense'] );
	$q->add( 'td', $w['status'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['pw_time'] ) );
	$q->add( 'td', isset( $rd_staff_list[ $w['pw_editid'] ] ) ? $rd_staff_list[ $w['pw_editid'] ]['name'] : $w['pw_editid'] );


	//$e['data-prompt'] = '请输入附加说明!';
	//$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	$e = $q->get_parent()->add( 'tr' );
	$e['style'] = 'line-height:20px;background:' . $bgcolor;
	$r = $e->add( 'td', "IP:\n" );
	$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
	$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );

	$r = $e->add( 'td' )->set_attr( 'colspan', 3 );
	$r->pre = '设备信息:';
	rd_json_info_display( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

	$r = [ '状态:' ];
	$w['pw_userid'] && $r[] = '业务员工: ' . ( isset( $rd_staff_list[ $w['pw_userid'] ] ) ? $rd_staff_list[ $w['pw_userid'] ]['name'] : $w['pw_userid'] );
	$w['pw_starts'] && $r[] = '开通时间: ' . date( 'Y-m-d H:i:s', $w['pw_starts'] );
	$w['pw_expire'] && $r[] = '到期时间: ' . date( 'Y-m-d H:i:s', $w['pw_expire'] );
	$w['pw_of'] && $r[] = '订单编号: ' . $w['pw_of'];
	$w['pw_be'] && $r[] = '关联信息: ' . $w['pw_be'];
	$w['pw_name'] && $r[] = '联系名称: ' . $w['pw_name'];
	$w['pw_tel'] && $r[] = '联系电话: ' . $w['pw_tel'];
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', join( "\n", $r ) );
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', "联系QQ:\n" . $w['pw_imid'] );
	$e->add( 'td' )->add( 'pre', "业务备注:\n" . $w['pw_note'] )->set_style( 'width:224px' );
}, [
	'merge_query' => $rd_device_query,
	'stat_rows' => $rd_device_count,
	'page_rows' => 21
] )->set(function( $q ) use(
	&$rd_device_field_filter,
	&$rd_hold_list,
	&$rd_room_list,
	&$rd_form_device_list_type,
	&$rd_form_device_list_status,
	&$rd_device_count )
{
	$q['style'] = 'margin:21px auto';
	$e = count( $q->thead->tr->td );
	$w = $q->thead->tr->add_before( 'tr' )->set_attr( 'style', 'background:#fff' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $rd_device_field_filter, [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$w->tag_button( '添加设备' )['onclick'] = '$.go("?/rx_erp(1)rd_device_insert")';
	$e = $w->add_before( 'div' );
	$e['style'] = 'padding-bottom:8px';
	// $e->tag_button( '查测试设备时间最长排列' )->set_attrs([
	// 	'style' => 'margin-right:8px',
	// 	'onclick' => 'return wa.query_act({7:"status.eq.%E6%B5%8B%E8%AF%95",8:"pw_starts.asc"})'
	// ]);

	// $e->tag_button( '打开' )->set_attrs([
	// 	'style' => 'margin-right:8px',
	// 	'onclick' => 'return $.go("?/rx_erp/ajax(1)rd_device_admin(2)0(7)status.eq.%E6%B5%8B%E8%AF%95","send_qq")'
	// ]);

	// $e->tag_button( '查看所有已到期的设备,' )->set_attrs([
	// 	'style' => 'margin-right:8px',
	// 	'onclick' => 'return wa.query_act({7:"pw_expire.le.' . time() . '/status.regexp.%5E%28出租%7C托管%29%24",8:"pw_expire.asc"})'
	// ]);

	// $e->tag_button( '打开' )->set_attrs([
	// 	'style' => 'margin-right:8px',
	// 	'onclick' => 'return $.go("?/rx_erp/ajax(1)rd_device_admin(2)1(7)pw_expire.le.' . time() . '/status.regexp.%5E%28出租%7C托管%29%24","send_qq")'
	// ]);

	$e->tag_input()->set_attrs([
		'value' => isset( $_GET[6] ) ? $_GET[6] : '',
		'style' => 'width:160px',
		'placeholder' => '输入后按 Enter 键开始搜索',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);

	$e->tag_input()->set_attrs([
		'style' => 'width:60px;margin-left:8px',
		'placeholder' => 'CPU',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);
	$e->tag_input()->set_attrs([
		'style' => 'width:60px;margin-left:8px',
		'placeholder' => '内存',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);
	$e->tag_input()->set_attrs([
		'style' => 'width:60px;margin-left:8px',
		'placeholder' => '硬盘',
		'onkeydown' => 'if(event.keyCode==13)return rd_form_device_search($.query("input",this.parentNode))'
	]);



	$e = isset( $_GET[7] ) ? urldecode( $_GET[7] ) : '';
	$w->tag_select(
		[ '' => '全部' ] + array_combine( $rd_hold_list, $rd_hold_list ),
		preg_match( '/hold\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("hold",this)' );
	$w->tag_select(
		[ '' => '所有机房' ] + array_combine( $rd_room_list, $rd_room_list ),
		preg_match( '/room\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("room",this)' );
	$w->tag_select(
		[ '' => '所有设备' ] + array_combine( $rd_form_device_list_type, $rd_form_device_list_type ),
		preg_match( '/sb_type\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("sb_type",this)' );
	$w->tag_select(
		[ '' => '所有状态' ] + array_combine( $rd_form_device_list_status, $rd_form_device_list_status ),
		preg_match( '/status\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("status",this)' );
	$w->add( 'span', '共找到 ' . $rd_device_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rd_action_record:
$rd_action_query = wa::get_filter( [ 'userid', 'query', 'json_data0' ] );
$rd_action_count = wa::$sql->get_rows( 'rx_rd_oplog', $rd_action_query );
$rd_action_querys = [
	'select' => '查看',
	'insert' => '插入',
	'update' => '更新',
	'delete' => '删除'
];
$rd_staff_list = rx_get_staff( '资源部' );
$rd_mark_today = mktime( 0, 0, 0 );
wa::htt_data_table( 'rx_rd_oplog', [
	'json_data0' => FALSE,
	'json_data1' => FALSE,
	'only' => [ FALSE, '记录唯一（数据对比）' ],
	'time' => [ 'desc', '操作时间' ],
	'userid' => [ FALSE, '操作用户' ],
	'action' => [ FALSE, '权限动作' ],
	'query' => [ FALSE, '请求' ],
	0 => '数据标识',
	'mark_time' => [ FALSE, '标记' ]
], function( $q, $w ) use( &$rd_staff_list, &$rd_action_querys, &$rd_mark_today )
{
	static $bgcolor;
	$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
	$w['json_data0'] = json_decode( $w['json_data0'], TRUE );
	$w['json_data1'] = json_decode( $w['json_data1'], TRUE );
	$q->td = $w['only'];
	$q->td[] = date( 'Y-m-d H:i:s', $w['time'] );
	$q->td[] = isset( $rd_staff_list[ $w['userid'] ] ) ? $rd_staff_list[ $w['userid'] ]['name'] : $w['userid'];
	$q->td[] = $w['action'];
	$q->td[] = $rd_action_querys[ $w['query'] ];
	$e = &$q->td[];
	if ( isset( $w['json_data0']['pw_time'] ) )
	{
		$e->tag_a( $w['json_data0']['only'], '?/rx_erp(1)rd_device_admin(7)only.eq.' . $w['json_data0']['only'] );

		$w['json_data0']['time'] = date( 'Y-m-d H:i:s', $w['json_data0']['time'] );
		$w['json_data0']['sb_info'] && $w['json_data0']['sb_info'] = json_decode( $w['json_data0']['sb_info'], TRUE );
		if ( isset( $w['json_data0']['sb_info']['密码'] ) )
		{
			unset( $w['json_data0']['sb_info']['密码'] );
		};
		$w['json_data0']['sb_info'] && $w['json_data0']['sb_info'] = json_encode( $w['json_data0']['sb_info'], JSON_UNESCAPED_UNICODE );




		$w['json_data0']['pw_time'] = date( 'Y-m-d H:i:s', $w['json_data0']['pw_time'] );
		isset( $w['json_data0']['pw_starts'] ) && $w['json_data0']['pw_starts'] && $w['json_data0']['pw_starts'] = date( 'Y-m-d H:i:s', $w['json_data0']['pw_starts'] );
		isset( $w['json_data0']['pw_expire'] ) && $w['json_data0']['pw_expire'] && $w['json_data0']['pw_expire'] = date( 'Y-m-d H:i:s', $w['json_data0']['pw_expire'] );




		isset( $w['json_data1']['time'] )
			&& $w['json_data1']['time']
			&& $w['json_data1']['time'] = date( 'Y-m-d H:i:s', $w['json_data1']['time'] );
		if ( isset( $w['json_data1']['sb_info'] ) )
		{
			$w['json_data1']['sb_info'] && $w['json_data1']['sb_info'] = json_decode( $w['json_data1']['sb_info'], TRUE );
			if ( isset( $w['json_data1']['sb_info']['密码'] ) )
			{
				unset( $w['json_data1']['sb_info']['密码'] );
			};
			$w['json_data1']['sb_info'] && $w['json_data1']['sb_info'] = json_encode( $w['json_data1']['sb_info'], JSON_UNESCAPED_UNICODE );
		};
		isset( $w['json_data1']['pw_time'] )
			&& $w['json_data1']['pw_time']
			&& $w['json_data1']['pw_time'] = date( 'Y-m-d H:i:s', $w['json_data1']['pw_time'] );
		isset( $w['json_data1']['pw_starts'] )
			&& $w['json_data1']['pw_starts']
			&& $w['json_data1']['pw_starts'] = date( 'Y-m-d H:i:s', $w['json_data1']['pw_starts'] );
		isset( $w['json_data1']['pw_expire'] )
			&& $w['json_data1']['pw_expire']
			&& $w['json_data1']['pw_expire'] = date( 'Y-m-d H:i:s', $w['json_data1']['pw_expire'] );

	}
	else
	{
		$e[0] = '未知数据';
	};
	$q->td[] = $w['mark_time'] < $rd_mark_today ? '过期' : '今天';
	$e = $q->get_parent();
	$e = &$e->tr[];
	$e['style'] = 'background:' . $bgcolor;
	$e = &$e->td;
	$e['colspan'] = 7;
	rd_data_diff( $e, $w['json_data0'], $w['json_data1'] );
}, [
	'merge_query' => $rd_action_query,
	'stat_rows' => $rd_action_count,
	'page_rows' => 21
] )->set(function( $q ) use( &$rd_action_count )
{
	$q['style'] = 'margin:21px auto';
	$e = count( $q->thead->tr->td );
	$w = $q->thead->tr->add_before( 'tr' )->set_attr( 'style', 'background:#fff' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( [
		'userid' => '操作用户',
		'query' => '操作请求',
		'json_data0' => '数据快照',
	] )->dl->dt;
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$e = isset( $_GET[7] ) ? urldecode( $_GET[7] ) : '';
	$w->tag_select(
		[ '' => '所有请求', 'select' => '查看', 'insert' => '插入', 'update' => '更新', 'delete' => '删除' ],
		preg_match( '/query\.eq\.([^\/]+)/', $e, $r ) ? $r[1] : NULL
	)->set_attr( 'onchange', 'rd_device_select_filter("query",this)' );
	$w->add( 'span', '共找到 ' . $rd_action_count . ' 个记录' )->set_class( 'a_c000' );
});
goto rd_end;

rx_notice_insert:
rx_notice_insert();
goto rd_end;

rd_staff_update:
rx_staff_update( '资源部', [
	'rx_team' => [ '' => '不分配' ],
	'rx_action' => [
		'rd_staff_update' => '允许部门员工更新（重要）',
		'rx_pack' => '允许处理数据包',
		'rx_notice_insert' => '允许发布通知',
		'rd_services_device' => '允许维护设备',
		'rd_services' => '允许使用维护回收（必须允许维护设备）',
		'rd_serverrd' => '允许使用维护其他（必须允许维护设备）',
		'rd_task_delete' => '允许删除维护中的',
		'rd_task_done' => '允许验收维护中的',
		'rd_ip0_admin' => '允许查看IP管理',
		'rd_ip0_insert' => '允许增加IP段',
		'rd_ip0_update' => '允许更新IP段',
		'rd_ip0_delete' => '允许删除IP段',
		'rd_ip1_list' => '允许查看IP地址',
		'rd_ip1_edit' => '允许更新IP地址',
		'rd_forcer_admin' => '允许查看机柜管理',
		'rd_forcer_device' => '允许查看机柜设备列表',
		'rd_forcer_insert' => '允许增加机柜',
		'rd_forcer_update' => '允许更新机柜',
		'rd_forcer_delete' => '允许删除机柜',
		'rd_device_admin' => '允许查看设备管理',
		'rd_device_insert' => '允许添加设备',
		'rd_device_update' => '允许更新设备',
		'rd_device_status' => '允许修改设备状态',
		'rd_device_emptys' => '允许清空设备状态',
		'rd_device_delete' => '允许删除设备',
		'rd_delete_record' => '允许查看删除记录',
		'rd_action_record' => '允许查看操作记录'
	]
] );
goto rd_end;

rx_notice:
rx_notice_record( '资源部' );

rd_end:
//2017.01.04 加载一个iframe 

switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	
	case 'rx_pack':				//数据包	
	case 'rd_services':			//维护回收
	case 'rd_serverrd':			//维护其他	
	case 'rd_ip0_admin':		//ＩＰ管理	
	case 'rd_forcer_admin':		//机柜管理
	case 'rd_device_admin':		//设备管理
	case 'rd_delete_record':	//删除记录
	case 'rd_action_record':	//操作记录
	case 'rd_staff_update':	    //部门员工
	case 'rx_notice_insert':	//发布通知
			wa::$htt->body->add('iframe')
					->set_attr("src","tishi.html")
					->set_attr("frameborder ","0")//没有边框
					->set_attr("scrolling  ","no")//没有滚动条
					->set_attr("id  ","ifrmid")//ID
					->set_attr("style","width: 366px;position: fixed;bottom: 20px;right: 25px;font-size: 0;line-height: 0;z-index: 100;")
			;
 	default:					
};
	
exit;
?>
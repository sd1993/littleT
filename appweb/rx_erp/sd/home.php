<?php
wa::$buffers->js[] = 'appweb/rx_erp/sd/script.js';
wa::$buffers->style = '*{font-size:14px}';
foreach ( $sd_service_qq as $q => $w )
{
	$sd_option_qq[] = [ $w . $q, 'javascript:$.setcookie("rx_sd_qq","' . $q . '"),$.go();' ];
};
wa::htt_nav( $sd_current_qq ? $sd_service_qq[ $sd_current_qq ] . $sd_current_qq : '请选择QQ', $sd_option_qq, 1 );
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'rx_pack':				goto rx_pack;
	case 'sd_rd_device':		goto sd_rd_device;
	case 'sd_task_insert':		goto sd_task_insert;
	case 'sd_task_wait_my':		goto sd_task_wait_my;
	case 'sd_task_wait':		goto sd_task_wait;
	case 'sd_task_record':		goto sd_task_record;
	case 'sd_staff_update':		goto sd_staff_update;
	case 'rx_notice_insert':	goto rx_notice_insert;
 	default:					goto rx_notice;
};

rx_pack:
rx_pack_record( '客服部' );
goto sd_end;

sd_rd_device:
$q = [
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

	//'time' => [ TRUE, '录入时间' ],
	'only' => [ TRUE, '设备编号' ],
	'hold' => [ FALSE, '产权' ],

	'room' => [ FALSE, '所在机房' ],
	'note' => [ FALSE, '设备备注' ],
	'sb_type' => [ FALSE, '设备类型' ],
	'bandwidth' => [ FALSE, '带宽' ],
	'defense' => [ FALSE, '防御' ],
	'status' => [ FALSE, '状态' ],
	'pw_time' => [ 'desc', '更新时间' ],
	'pw_editid' => [ FALSE, '更新员工' ],
	0 => '设备维护'
];

$w = [ 'status!="机柜设备"' ];

if ( isset( $_GET[6] ) )
{
	$e = wa::$sql->escape( '%' . $_GET[6] . ' %' );
	$w[] = '(concat(ip_main," ") like ' . $e . ' or concat(ip_vice," ") like ' . $e . ')';
};

$w = wa::get_filter( [ 'only', 'note', 'pw_userid', 'pw_of', 'pw_be', 'pw_imid' ], $w );

$e = wa::$sql->get_rows( 'rx_rd_device', $w );

	//if($_GET[7]){
	//	var_dump($e);
	//}


if($e == 0){//2016.10.9
	$w = [ 'status!="机柜设备"' ];

	if ( isset( $_GET[6] ) )
	{
		$e = wa::$sql->escape( '%' . $_GET[6] . '%' );
		$w[] = '(concat(ip_main," ") like ' . $e . ' or concat(ip_vice," ") like ' . $e . ')';
	};

	$w = wa::get_filter( [ 'only', 'note', 'pw_userid', 'pw_of', 'pw_be', 'pw_imid' ], $w );

	$e = wa::$sql->get_rows( 'rx_rd_device', $w );
}


	
$q = wa::htt_data_table( 'rx_rd_device', $q, function( $q, $w )
{
	static $bgcolor;
	$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
	//$q->td[] = date( 'Y-m-d H:i:s', $w['time'] );
	//$q->td[] = $w['only'];

	$e = &$q->td[];
	$e[0] = $w['only'];
	substr( $w['only'], 0, 3 ) === 'LA-' && $e['style'] = 'color:red';
	
	$e = &$q->td[];
	$e[0] = $w['hold'];
	$w['hold'] == '锐讯' || $e['style'] = 'color:red';

	$e = &$q->td[];
	$e[0] = $w['room'];
	$w['room'] == '佛山德胜机房' || $e['style'] = 'color:red';


	$q->td[] = $w['note'];
	$q->td[] = $w['sb_type'];
	$q->td[] = $w['bandwidth'];
	$q->td[] = $w['defense'];

	$e = &$q->td[];
	$e[0] = $w['status'];
	strpos( '出租托管变更', $w['status'] ) === FALSE && $e['style'] = 'color:red';


	$q->td[] = date( 'Y-m-d H:i:s', $w['pw_time'] );
	$q->td[] = $w['pw_editid'];

	$e = &$q->td[];
	$e->tag_a( '设备维护' )['onclick'] = 'return !$.window_open("?/rx_erp(1)sd_task_insert","sd_task_insert",{width:600,height:640})';

	$e = $q->get_parent()->add( 'tr' );
	$e['style'] = 'background:' . $bgcolor;
	$r = $e->add( 'td', "IP:\n" );
	$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
	$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
	$r = $e->add( 'td' )->set_attr( 'colspan', 3 );
	sd_json_info_display( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

	$r = [ '状态:' ];
	$w['pw_userid'] && $r[] = '业务员工: ' . $w['pw_userid'];
	$w['pw_starts'] && $r[] = '开通时间: ' . date( 'Y-m-d H:i:s', $w['pw_starts'] );
	$w['pw_expire'] && $r[] = '到期时间: ' . date( 'Y-m-d H:i:s', $w['pw_expire'] );
	$w['pw_of'] && $r[] = '订单编号: ' . $w['pw_of'];
	$w['pw_be'] && $r[] = '关联信息: ' . $w['pw_be'];
	$w['pw_name'] && $r[] = '联系名称: ' . $w['pw_name'];
	$w['pw_tel'] && $r[] = '联系电话: ' . $w['pw_tel'];
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', join( "\n", $r ) );

	$e->add( 'td' )->set_attr( 'colspan', 2 )->add( 'pre', "联系QQ:\n" . $w['pw_imid'] );

	$e->add( 'td' )->set_attr( 'colspan', 2 )->add( 'pre', "业务备注:\n" . $w['pw_note'] )->set_style( 'width:224px' );




}, [
	'merge_query' => $w,
	'stat_rows' => $e,
	'page_rows' => 21
]);
$q['style'] = 'margin:21px auto';
$w = count( $q->thead->tr->td );
$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );

$w = $q->thead->tr->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( [ 'only' => '设备编号', 'note' => '设备备注', 'pw_userid' => '业务员工', 'pw_of' => '订单编号', 'pw_be' => '客户编号', 'pw_imid' => '联系QQ' ], [
	//'onfocus' => "rd_filter_callback(this)"
] )->dl->dt;

$w->tag_input()->set_attrs([
	'value' => isset( $_GET[6] ) ? $_GET[6] : '',
	'style' => 'width:240px',
	'placeholder' => '输入 IP 或者 设备编号 按 Enter 搜索',
	'onkeydown' => 'if(event.keyCode==13)return sd_device_filter($.query("input",this.parentNode))'
]);



$w->span[] = '共找到 ' . $e . ' 个记录';




goto sd_end;

sd_task_insert:
wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';
wa::htt_form_post( $sd_services_editor )->set(function( $q ) use( &$sd_service_qq, $sd_current_qq )
{
	$q['action'] = '?/rx_erp/ajax(1)sd_task_insert';
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
	$sd_current_qq && $q->table->tbody->tr[2]->td[1]->div->input['value'] = $sd_service_qq[ $sd_current_qq ] . $sd_current_qq;
	$w = $q->xpath( '//textarea[@name]' );
	$w[0]['readonly'] = TRUE;
	$w[0]['style'] = $w[1]['style'] = 'height:180px;overflow-y:auto';
	$w[1]['placeholder'] = '任务说明IP将自动匹配并且异步获取';
	$w[1]['onfocus'] = 'wa.over_input(this,sd_ajax_decive,this.form)';
	$q->tag_input( 'hidden' )['name'] = 'pkip';
	$q->tag_input( 'hidden' )['name'] = 'pkno';
	$q->tag_input( 'hidden' )['name'] = 'sbqq';
	$q->tag_input( 'hidden' )['name'] = 'sbstatus';
	$q->tag_input( 'hidden' )['name'] = 'user_name';
});
goto sd_end;

sd_task_wait_my:
$sd_task_wait = [ 'done is null', 'userid=' . wa::$user['username'] ];
$sd_task_wait_to = TRUE;
$sd_task_path ='sd_task_wait';
goto sd_task_record;

sd_task_wait:
$sd_task_wait = [ 'done is null' ];
$sd_task_path ='sd_task_wait';

sd_task_record:
isset( $sd_task_wait ) || $sd_task_wait = [ 'done is not null' ];
isset( $sd_task_path ) || $sd_task_path = 'sd_task_record';
$sd_task_field = [
	'only' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'done' => FALSE,
	0 => '删除',
	'time' => [ 'desc', '提交时间' ],
	'user_name' => [ FALSE, '业务员' ],
	'pkip' => [ FALSE, '设备IP' ],
	'pkno' => [ FALSE, '设备编号' ],
	'userid' => [ FALSE, '提交员工' ],
	'riqq' => [ FALSE, '报障QQ' ],
	'imqq' => [ FALSE, '提交QQ' ],
	'start' => [ FALSE, '分配时间' ],
	'name' => [ FALSE, '跟进员工' ],
	'over' => [ FALSE, '完成时间' ],
	1 => '用时',
	2 => '验收'
];

$sd_task_query = wa::get_filter( array_merge( array_keys( array_filter( $sd_task_field, 'is_array' ) ), [ 'note', 'done' ] ), $sd_task_wait );
$sd_task_count = wa::$sql->get_rows( 'rx_sd_task', $sd_task_query );
$sd_staff = rx_get_staff( '客服部' );
wa::$buffers->script = 'rx_ws_init("/' . $sd_task_path . '")';
wa::htt_data_table( 'rx_sd_task', $sd_task_field, function( $q, $w ) use( $sd_service_qq, &$sd_staff )
{
	$e = &$q->td[];
	$e = $e->tag_a( '删除', '?/rx_erp/ajax(1)sd_task_delete(2)' . $w['only'] );
	$e['data-confirm'] = '不能撤消';
	$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	$e = &$q->td[];
	$e['onclick'] = 'rx_tr_resh(this.parentNode)';
	$e['style'] = 'cursor:pointer;text-decoration:underline';
	$e[0] = date( 'Y-m-d H:i:s', $w['time'] );
	$q->td[] = $w['user_name'];
	$q->td[] = $w['pkip'];
	$q->td[] = $w['pkno'];
	$q->td[] = isset( $sd_staff[ $w['userid'] ] ) ? $sd_staff[ $w['userid'] ]['name'] : $w['userid'];
	$e = &$q->td[];
	$e->tag_a( $w['riqq'], 'tencent://message/?uin=' . $w['riqq'] );
	$e = &$q->td[];
	$e->tag_a( $sd_service_qq[ $w['imqq'] ] . $w['imqq'], 'tencent://message/?uin=' . $w['imqq'] );
	$e = &$q->td[];
	if ( $w['start'] )
	{
		$e['style'] = 'color:green';
		$e[0] = date( 'H:i:s', $w['start'] );
	}
	else
	{
		$e['style'] = 'color:red';
		$e[0] = '等待分配';
	};
	$q->td[] = $w['name'];
	$e = &$q->td[];
	if ( $w['over'] > 4 )
	{
		$e['style'] = 'color:green';
		$e[0] = date( 'H:i:s', $w['over'] );
		$q->td[] = gmstrftime( '%H:%M:%S', $w['over'] - $w['time'] );
	}
	else
	{
		$e['style'] = 'color:red';
		$e[0] = $w['over'] === NULL ? '等待中...' : [ '不确定...', '约六分钟', '约一刻钟', '约半小时', '约一小时' ][ $w['over'] ];
		$q->td[] = NULL;
	};
	$e = &$q->td[];
	if ( $w['done'] )
	{
		$e[0] = date( 'Y-m-d H:i:s', $w['done'] );
	}
	else
	{
		$e = $e->tag_a( '验收', '?/rx_erp/ajax(1)sd_task_done(2)' . $w['only'] );
		$e['data-confirm'] = '约吗？';
		$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	};
	$e = &$q->get_parent()->tr[];
	$e['style'] = 'display:none';
	$e->td['colspan'] = 4;
	$e->td->pre = $w['sbxx'];
	$e->td[]['colspan'] = 9;
	$e->td[1]->pre = $w['note'];

}, [
	'merge_query' => $sd_task_query,
	'stat_rows' => $sd_task_count
])->set(function( $q ) use( &$sd_task_field, &$sd_service_qq, $sd_task_count, &$sd_task_wait_to )
{
	$q['style'] = 'margin: 21px auto';
	$w = count( $q->thead->tr->td );
	$e = $q->thead->tr->add_before( 'tr' );
	$e->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );
	$w = $e->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w );
	foreach ( $sd_task_field as $e => $r )
	{
		is_array( $r ) && $t[ $e ] = $r[1];
	};
	$t['note'] = '提交内容';
	$w = $w->ins_filter( $t, [
		'onfocus' => "sd_filter_callback(this)"
	] );
	$e = [ '' => '所有售后QQ' ];
	foreach ( $sd_service_qq as $r => $t )
	{
		$e[ $r ] = $t . $r;
	};
	$w->dl->dt->tag_select( $e, isset( $_GET[7] )
		&& preg_match( '/imqq\.eq\.([^\/]+)/', $_GET[7], $r ) ? $r[1] : NULL )['onchange'] = 'wa.query_act({7:this.value?"imqq.eq."+this.value:null})';
	if ( $sd_task_wait_to )
	{
		$e = &$w->dl->dt->select[];
		$e['data-confirm'] = '确认转移';
		$e['onchange'] = 'wa.ajax_query("?/rx_erp/ajax(1)sd_task_wait_to(2)"+this.value,this.dataset)';
		$e->option = '转移给';
		foreach ( wa::$sql->q_query( 'select username,name from rx_hr_staff where `group`="客服部" and rx_action like "%sd_task_wait_my%"' ) as $r )
		{
			$t = &$e->option[];
			$t['value'] = $r['username'];
			$t[0] = $r['name'];
		};
	};
	$w->dl->dt->tag_input()->set_attrs([
		'placeholder' => '输入后按 Enter 键开始搜索',
		'style' => 'width:210px',
//		'name' => 'search',
		'onkeydown' => 'if(event.keyCode==13)return sd_task_search(this.value)'
	]);
	// $sd_task_wait_to && $w->dl->dt->tag_button( '转移我的维护中给' )->set_attrs([
	// 	'data-prompt' => '甩包袱给(工号)',
	// 	'onclick' => 'wa.ajax_query("?/rx_erp/ajax(1)sd_task_wait_to",this.dataset)'
	// ]);
	$w->dl->dt->span = '共找到 ' . $sd_task_count . ' 个记录';
});
goto sd_end;

rx_notice_insert:
rx_notice_insert();
goto sd_end;

sd_staff_update:
rx_staff_update( '客服部', [
	'rx_team' => [ '' => '不分配' ],
	'rx_action' => [
		'sd_staff_update' => '允许员工管理和更新员工（重要）',
		'rx_pack' => '允许处理数据包',
		'sd_rd_device' => '允许访问设备查询',
		'sd_task_insert' => '允许访问设备维护',
		'sd_task_wait_my' => '允许访问我的维护中',
		'sd_task_wait_to' => '允许转移我的维护中',
		'sd_task_done' => '允许验收等待维护中',
		'sd_task_wait' => '允许访问等待维护中',
		'sd_task_delete' => '允许删除等待维护记录',
		'sd_task_record' => '允许访问设备维护记录'
	]
] );
goto sd_end;

rx_notice:
rx_notice_record( '客服部' );

sd_end:
exit;
?>
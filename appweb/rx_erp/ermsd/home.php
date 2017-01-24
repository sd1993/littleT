<?php
wa::$buffers->js[] = 'appweb/rx_erp/ermsd/script.js';
wa::$buffers->style = '*{font-size:14px}';
//wa::$htt->body->div->div[1]['style'] = 'height:1000px';
//wa::$htt->body->div['style'] = 'background:url("./appweb/rx_erp/ermsd/bg_lol_qv1.jpg")no-repeat scroll 0 0';
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'rx_pack':				goto rx_pack;
	case 'ermsd_task_insert':	goto ermsd_task_insert;
	case 'ermsd_task_update':	goto ermsd_task_update;
 	case 'ermsd_task_work':		goto ermsd_task_work;
 	case 'ermsd_task_done':		goto ermsd_task_done;
 	case 'ermsd_task_stat':		goto ermsd_task_stat;

 	case 'ermsd_rd_device':		goto ermsd_rd_device;

 	case 'ermsd_task_my':		goto ermsd_task_my;
 	case 'ermsd_task_record_my':goto ermsd_task_record_my;
 	case 'ermsd_task_record':	goto ermsd_task_record;

 	case 'ermsd_staff_online': goto ermsd_staff_online;

 	case 'ermsd_relief_insert':	goto ermsd_relief_insert;
 	case 'ermsd_relief_record':	goto ermsd_relief_record;

 	case 'ermsd_staff_update':	goto ermsd_staff_update;
 	case 'rx_notice_insert':	goto rx_notice_insert;
 	default:					goto rx_notice;
};

rx_pack:
ermsd_menu_light(0);
rx_pack_record( '德胜机房运维中心' );
goto ermsd_end;

ermsd_task_update:
$ermsd_form_task_data = wa::$sql->get_only( 'rx_ermsd_task', 'md5', $_GET[2] );

ermsd_task_insert:
wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';
if ( !isset( $ermsd_form_task_data ) )
{
	ermsd_menu_light(1);
	$ermsd_form_task_data = [ 'pkip' => '0.0.0.0' ];
};
array_walk( $ermsd_form_task_data, function( $q, $w ) use( &$ermsd_form_task_data )
{
	$ermsd_form_task_data[ $w ] = $q;
});
wa::htt_form_post( $ermsd_form_task_editor, $ermsd_form_task_data )->set(function( $q ) use( &$ermsd_form_task_data )
{
	if ( isset( $ermsd_form_task_data['md5'] ) )
	{
		$q['action'] = '?/rx_erp/ajax(1)ermsd_task_update(2)' . $ermsd_form_task_data['md5'];
		$q->table->thead->tr->td = '修改任务IP：' . $ermsd_form_task_data['pkip'];
	}
	else
	{
		$q['action'] = '?/rx_erp/ajax(1)ermsd_task_insert';
		$q->table->thead->tr->td = '新创建任务';
	};
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
	$w = $q->xpath( '//textarea[@name]' );
	$w[0]['style'] = $w[1]['style'] = 'height:119px;overflow-y:auto';
	$w[1]['placeholder'] = '任务说明IP将自动匹配并且异步获取';
	$w[1]['onfocus'] = 'wa.over_input(this,ermsd_ajax_json)';
	$w = $q->xpath( '//input' );
	$w[1]['onfocus'] = $w[0]['onfocus'] = 'wa.over_input(this,ermsd_ajax_json)';
	$w[0]['data-old'] = '0.0.0.0';
});
goto ermsd_end;

ermsd_task_work:
ermsd_menu_light(2);
wa::$buffers->script = 'var ermsd_task_type=' . json_encode( $ermsd_task_type )
	. ',ermsd_staff_list=' . ermsd_json_staff()
	. ';rx_ws_init("/ermsd_task_work"),ermsd_task_runtime_init(' . $_SERVER['REQUEST_TIME'] . '),$(ermsd_task_work_init)';
wa::htt_data_table( 'rx_ermsd_task', [
	'md5' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'0' => '修改操作',
	'time' => [ 'asc', '创建时间（详细）' ],
	'type' => [ TRUE, '类型' ],
	'd_userid' => [ TRUE, '分配给' ],
	'start' => [ TRUE, '开始时间' ],
	'1' => '剩余时间',
	'team' => [ TRUE, '完成团队' ],
	'mark' => [ TRUE, '标记' ],
	'pkip' => [ TRUE, '设备IP' ],
	'pkno' => [ TRUE, '设备编号' ],
	'c_userid' => [ TRUE, '创建人' ],
	'2' => '完成'
], function( $q, $w )
{
	$q['data-md5'] = $w['md5'];
	$e = $q->add( 'td' );
	$e->tag_a( '删除', '?/rx_erp/ajax(1)ermsd_task_delete' )->set_attrs([
		'data-prompt' => '请输入当前登录员工的密码',
		'onclick' => 'return wa.ajax_query(this.href+"(2)"+this.parentNode.parentNode.dataset.md5,this.dataset)'
	]);
	$e->add( 'span', ' | ' );
	$e->tag_a( '修改', '?/rx_erp(1)ermsd_task_update(2)' . $w['md5'] )['onclick'] = 'return !$.window_open(this.href,"ermsd_task_insert",{width:600,height:600})';
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) )->set_attrs([
		'onclick' => 'rx_tr_resh(this.parentNode)',
		'style' => 'cursor:pointer;text-decoration:underline'
	]);

	


	$e = $q->add( 'td' );
	$e['data-type'] = $w['type'];
	$e['style'] = 'padding:3px';
	$e->tag_select( [
		'a' => 'Ａ计时六分钟',
		'b' => 'Ｂ计时一刻钟',
		'c' => 'Ｃ计时半小时',
		'd' => 'Ｄ计时一小时',
		'e' => 'Ｅ计时20分钟',
		'f' => 'Ｆ计时45分钟',
		'k' => 'Ｋ不限制计时'
	], $w['type'] )->set_attrs([
		'style' => 'padding:2px;border:0;box-shadow:none',
		'onchange' => 'wa.ajax_query("?/rx_erp/ajax(1)ermsd_task_work(2)"+this.parentNode.parentNode.dataset.md5+"(5)"+this.value)'
	]);
	// $e['style'] = 'cursor:pointer';
	// $e['onclick'] = 'wa.ajax_query("?/rx_erp/ajax(1)ermsd_task_work(2)"+this.parentNode.dataset.md5,this.dataset)';
	// $e['data-prompt'] = '请输入标记';
	// $e['data-value'] = $w['type'];


	$e = $q->add( 'td' );
	$e['style'] = 'padding:3px';
	if ( $w['start'] )
	{
		$e['data-start'] = $w['start'];
		$e['data-d_userid'] = $w['d_userid'];
	};
	$q->add( 'td' );
	$q->add( 'td' );
	$q->add( 'td', $w['team'] );
	$e = $q->add( 'td', $w['mark'] );
	$e['style'] = 'cursor:pointer';
	$e['onclick'] = 'wa.ajax_query("?/rx_erp/ajax(1)ermsd_task_mark(2)"+this.parentNode.dataset.md5,this.dataset)';
	$e['data-prompt'] = '请输入标记';
	$e['data-value'] = $w['mark'];
	$q->add( 'td', $w['pkip'] );
	$q->add( 'td', $w['pkno'] );
	$q->add( 'td', $w['c_userid'] );
	$e = $q->add( 'td' )->tag_a( '完成', '?/rx_erp/ajax(1)ermsd_task_over(2)' . $w['md5'] );
	$e['data-confirm'] = '约吗?';
	$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	$e = $q->get_parent()->add( 'tr' )->set_style( 'display:none;line-height:18px' );
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', ermsd_task_sbxx_trim( $w['sbxx'] ) );
	$e->add( 'td' )->set_attr( 'colspan', 9 )->add( 'pre', $w['note'] );

}, [
	'merge_query' => wa::get_filter( [ 'md5' ], [ 'over is null' ] ),
	'page_rows' => 42
] )->set(function( $q )
{
	$q['style'] = 'margin:21px auto';
	$q['id'] = 'ermsd_task_work_list';
});
goto ermsd_end;

ermsd_task_done:
ermsd_menu_light(3);
wa::$buffers->script = 'rx_ws_init("/ermsd_task_done")';
$ermsd_staff_list = rx_get_staff( '德胜机房运维中心' );
wa::htt_data_table( 'rx_ermsd_task', [
	'md5' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'time' => [ 'asc', '创建时间（详细）' ],
	'type' => [ TRUE, '类型' ],
	'd_userid' => [ TRUE, '完成人' ],
	'start' => [ TRUE, '开始时间' ],
	'over' => [ TRUE, '剩余时间' ],
	'team' => [ TRUE, '完成团队' ],
	'mark' => [ TRUE, '标记' ],
	'pkip' => [ TRUE, '设备IP' ],
	'pkno' => [ TRUE, '设备编号' ],
	'c_userid' => [ TRUE, '创建人' ],
	'1' => '验收'
], function( $q, $w ) use( &$ermsd_staff_list, &$ermsd_task_score )
{
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) )->set_attrs([
		'onclick' => 'rx_tr_resh(this.parentNode)',
		'style' => 'cursor:pointer;text-decoration:underline'
	]);
	$q->add( 'td', $w['type'] );
	$q->add( 'td', isset( $ermsd_staff_list[ $w['d_userid'] ] ) ? $ermsd_staff_list[ $w['d_userid'] ]['name'] : $w['d_userid'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['start'] ) );
	$q->add( 'td', ermsd_task_over_time( $w['over'] ) )[ 'style' ] = $w['over'] < 0 ? 'color:red' : 'color:green';
	$q->add( 'td', $w['team'] );
	$e = $q->add( 'td', $w['mark'] );
	$e['style'] = 'cursor:pointer';
	$e['onclick'] = 'wa.ajax_query("?/rx_erp/ajax(1)ermsd_task_mark(2)' . $w['md5'] . '(3)done",this.dataset)';
	$e['data-prompt'] = '请输入标记';
	$e['data-value'] = $w['mark'];

	$q->add( 'td', $w['pkip'] );
	$q->add( 'td', $w['pkno'] );
	$q->add( 'td', isset( $ermsd_staff_list[ $w['c_userid'] ] ) ? $ermsd_staff_list[ $w['c_userid'] ]['name'] : $w['c_userid'] );
	$q->add( 'td' )->tag_a( '验收', '?/rx_erp/ajax(1)ermsd_task_done(2)' . $w['md5'] )->set_attrs([
		'data-prompt' => '请输入得分',
		'data-value' => $w['type'] == 'k' ? '' : $ermsd_task_score[ $w['type'] ],
		'onclick' => 'return wa.ajax_query(this.href,this.dataset)'
	]);
	$e = $q->get_parent()->add( 'tr' )->set_style( 'display:none;line-height:18px' );
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', ermsd_task_sbxx_trim( $w['sbxx'] ) );
	$e->add( 'td' )->set_attr( 'colspan', 8 )->add( 'pre', $w['note'] );
}, [
	'merge_query' => 'where over is not null and done is null',
	'page_rows' => 42
] )['style'] = 'margin:21px auto';
goto ermsd_end;

ermsd_task_stat:
ermsd_menu_light(4);
wa::$buffers->write->ins_filter(
	[ 'team' => '完成团队', 'start' => '开始时间' ],
	[ 'onfocus' => 'ermsd_filter_callback(this)' ]
)->set_style( 'width:640px;margin:0 auto;padding-top:21px' );
wa::$buffers->write->add( 'div' )->set(function( $q ) use( &$ermsd_team_list )
{
	$q->set_css([
		'width' => '649px',
		'padding-top' => '21px',
		'margin' => '0 auto'
	])->set_class( 'spacing_each' );
	$q->tag_select(
		[ '' => '请选择' ] + array_combine( $ermsd_team_list, $ermsd_team_list ),
		isset( $_GET[7] ) && preg_match( '/^team\.eq\.(' . join( '|', $ermsd_team_list ) . ')/', urldecode( $_GET[7] ), $w ) ? $w[1] : NULL
	);
	$q->tag_button( '今天09:00-今天17:00' )->set_attrs([
		'data-start_time' => mktime( 9, 0, 0 ),
		'data-end_time' => mktime( 17, 0, 0 ),
		'onclick' => 'ermsd_task_stat(this)',
	]);
	$q->tag_button( '今天16:00-明天09:00' )->set_attrs([
		'data-start_time' => mktime( 16, 0, 0 ),
		'data-end_time' => mktime( 9, 0, 0 ) + 86400,
		'onclick' => 'ermsd_task_stat(this)',
	]);
	$q->tag_button( '昨天16:00-今天09:00' )->set_attrs([
		'data-start_time' => mktime( 16, 0, 0 ) - 86400,
		'data-end_time' => mktime( 9, 0, 0 ),
		'onclick' => 'ermsd_task_stat(this)',
	]);
	$q->tag_button( '打印页' )->set_attrs([
		'class' => 'b',
		'onclick' => '$.go("?/rx_erp/ajax(1)ermsd_task_stat(7)"+this.dataset.query,"print")',
		'data-query' => isset( $_GET[7] ) ? $_GET[7] : ''
	]);
});
isset( $_GET[7] )
&& preg_match( '/^team\.eq\.(' . join( '|', $ermsd_team_list ) . ')\/start\.ge\.(\d{10})\/start\.le\.(\d{10})$/', urldecode( $_GET[7] ), $ermsd_task_stat_query )
&& ( $ermsd_task_stat_query[3] - $ermsd_task_stat_query[2] ) < 31968000
? wa::$buffers->write->tag_table(function( $q ) use( &$ermsd_team_list, &$ermsd_task_stat_query )
{
	$q['class'] = 'wa_grid_table';
	$q['style'] = 'background:white;margin:21px auto;text-align:right';
	$w = $q->thead->add( 'tr' );
	foreach ( [ '排名', '员工姓名', '累计超时', '累计节省', '总体时差', 'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ', 'Ｆ', 'Ｋ', '得分' ] as $e )
	{
		$w->add( 'td', $e );
	};
	$q->thead->tr->td[5]['style'] = 'background:#ffe0e0';
	$q->thead->tr->td[6]['style'] = 'background:#e0ffe0';
	$q->thead->tr->td[7]['style'] = 'background:#e0e0ff';
	$q->thead->tr->td[8]['style'] = 'background:#ffffe0';
	$q->thead->tr->td[10]['style'] = 'background:#ffe0e0';
	$q->thead->tr->td[11]['style'] = 'background:#e0ffe0';
	$w = 0;
	foreach ( ermsd_task_stat( $ermsd_task_stat_query[1], $ermsd_task_stat_query[2], $ermsd_task_stat_query[3] ) as $e )
	{
		$r = $q->tbody->add( 'tr' );
		$r->add( 'td', ++$w );
		$r->add( 'td', $e['name'] );
		$r->add( 'td', number_format( $e['past'] ) . '/s' );
		$r->add( 'td', number_format( $e['push'] ) . '/s' );
		$r->add( 'td', number_format( $e['push'] + $e['past'] ) . '/s' );
		$r->add( 'td', $e['a'] )['style'] = 'background:#ffe0e0';
		$r->add( 'td', $e['b'] )['style'] = 'background:#e0ffe0';
		$r->add( 'td', $e['c'] )['style'] = 'background:#e0e0ff';
		$r->add( 'td', $e['d'] )['style'] = 'background:#ffffe0';
		$r->add( 'td', $e['e'] );
		$r->add( 'td', $e['f'] )['style'] = 'background:#ffe0e0';
		$r->add( 'td', $e['k'] )['style'] = 'background:#e0ffe0';
		$r->add( 'td', $e['score'] );
	};
})
: wa::$buffers->write->add( 'div', '请选择队伍后在选择时间段来查看统计' )->set_css([
	'padding' => '168px',
	'font-size' => '32px',
	'text-align' => 'center'
]);
goto ermsd_end;

ermsd_rd_device:
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
	'pw_editid' => [ FALSE, '更新员工' ]
];

$w = [ 'status!="机柜设备"' ];

if ( isset( $_GET[6] ) )
{
	$e = wa::$sql->escape( '%' . $_GET[6] . ' %' );
	$w[] = '(concat(ip_main," ") like ' . $e . ' or concat(ip_vice," ") like ' . $e . ')';
};

$w = wa::get_filter( [ 'only', 'pw_userid', 'pw_imid' ], $w );
$e = wa::$sql->get_rows( 'rx_rd_device', $w );


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
	$q->td[] = $w['only'];
	$q->td[] = $w['hold'];
	$q->td[] = $w['room'];
	$q->td[] = $w['note'];
	$q->td[] = $w['sb_type'];
	$q->td[] = $w['bandwidth'];
	$q->td[] = $w['defense'];
	$q->td[] = $w['status'];
	$q->td[] = date( 'Y-m-d H:i:s', $w['pw_time'] );
	$q->td[] = $w['pw_editid'];

	$e = $q->get_parent()->add( 'tr' );
	$e['style'] = 'background:' . $bgcolor;
	$r = $e->add( 'td', "IP:\n" );
	$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
	$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
	$r = $e->add( 'td' )->set_attr( 'colspan', 3 );
	ermsd_json_info_display( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

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

	$e->add( 'td' )->add( 'pre', "业务备注:\n" . $w['pw_note'] )->set_style( 'width:224px' );

}, [
	'merge_query' => $w,
	'stat_rows' => $e,
	'page_rows' => 21
]);
$q['style'] = 'margin:21px auto';
$w = count( $q->thead->tr->td );
$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );

$w = $q->thead->tr->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( [ 'only' => '设备编号', 'pw_userid' => '业务员工', 'pw_imid' => '联系QQ' ], [
	//'onfocus' => "rd_filter_callback(this)"
] )->dl->dt;

$w->tag_input()->set_attrs([
	'value' => isset( $_GET[6] ) ? $_GET[6] : '',
	'style' => 'width:240px',
	'placeholder' => '输入 IP 或者 设备编号 按 Enter 搜索',
	'onkeydown' => 'if(event.keyCode==13)return ermsd_device_filter($.query("input",this.parentNode))'
]);

$w->span[] = '共找到 ' . $e . ' 个记录';
goto ermsd_end;

ermsd_task_my:
ermsd_menu_light(5);
wa::$buffers->script = 'var ermsd_task_type=' . json_encode( $ermsd_task_type )
	. ';$(ermsd_task_work_init_my);ermsd_task_runtime_init(' . $_SERVER['REQUEST_TIME'] . ')';
$ermsd_staff_list = rx_get_staff( '德胜机房运维中心' );
wa::htt_data_table( 'rx_ermsd_task', [
	'md5' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'time' => [ 'asc', '创建时间（详细）' ],
	'type' => [ TRUE, '类型' ],
	'd_userid' => [ TRUE, '分配给' ],
	'start' => [ TRUE, '开始时间' ],
	'1' => '剩余时间',
	'team' => [ TRUE, '完成团队' ],
	'mark' => [ TRUE, '标记' ],
	'pkip' => [ TRUE, '设备IP' ],
	'pkno' => [ TRUE, '设备编号' ],
	'c_userid' => [ TRUE, '创建人' ],
	'2' => '完成'
], function( $q, $w ) use( &$ermsd_staff_list )
{
	$q['data-md5'] = $w['md5'];
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) )->set_attrs([
		'onclick' => 'rx_tr_resh(this.parentNode)',
		'style' => 'cursor:pointer;text-decoration:underline'
	]);
	$q->add( 'td', $w['type'] );
	if ( $w['start'] )
	{
		$q->add( 'td', isset( $ermsd_staff_list[ $w['d_userid'] ] ) ? $ermsd_staff_list[ $w['d_userid'] ]['name'] : $w['d_userid'] );
		$q->add( 'td', date( 'Y-m-d H:i:s', $w['start'] ) );
		$q->add( 'td' )['data-start'] = $w['start'];
	}
	else
	{
		$q->add( 'td' )->tag_a( '接工单', '?/rx_erp/ajax(1)ermsd_task_work_my(2)' . $w['md5'] )->set_attr( 'onclick', 'return wa.ajax_query(this.href)' );
		$q->add( 'td' );
		$q->add( 'td' );
	};
	$q->add( 'td', $w['team'] );
	$e = $q->add( 'td', $w['mark'] );
	$e['style'] = 'cursor:pointer';
	$e['onclick'] = 'wa.ajax_query("?/rx_erp/ajax(1)ermsd_task_mark(2)"+this.parentNode.dataset.md5,this.dataset)';
	$e['data-prompt'] = '请输入标记';
	$e['data-value'] = $w['mark'];
	$q->add( 'td', $w['pkip'] );
	$q->add( 'td', $w['pkno'] );
	$q->add( 'td', isset( $ermsd_staff_list[ $w['c_userid'] ] ) ? $ermsd_staff_list[ $w['c_userid'] ]['name'] : $w['c_userid'] );
	$e = $q->add( 'td' )->tag_a( '完成', '?/rx_erp/ajax(1)ermsd_task_over_my(2)' . $w['md5'] );
	$e['onclick'] = 'return wa.ajax_query(this.href)';
	$e = $q->get_parent()->add( 'tr' )->set_style( 'display:none;line-height:18px' );
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', ermsd_task_sbxx_trim( $w['sbxx'] ) );
	$e->add( 'td' )->set_attr( 'colspan', 8 )->add( 'pre', $w['note'] );
}, [
	'merge_query' => 'where (over is null and d_userid=' . wa::$user['username'] . ') or d_userid is null',
	'page_rows' => 42
] )->set(function( $q )
{
	$q['style'] = 'margin:21px auto';
	$q['id'] = 'ermsd_task_work_list';
});
goto ermsd_end;

ermsd_task_record_my:
ermsd_menu_light(6);
$ermsd_task_query = [ 'd_userid="' . wa::$user['username'] . '"' ];

ermsd_task_record:
isset( $ermsd_task_query ) || $ermsd_task_query = [];
$ermsd_task_query[] = 'done is not null';
$ermsd_staff_list = rx_get_staff( '德胜机房运维中心' );
$ermsd_task_field = [
	'md5' => FALSE,
	'sbxx' => FALSE,
	'note' => FALSE,
	'time' => [ 'desc', '创建时间' ],
	'type' => [ TRUE, '类型' ],
	'd_userid' => [ TRUE, '完成人' ],
	'start' => [ TRUE, '开始时间' ],
	'over' => [ TRUE, '剩余时间' ],
	'score' => [ TRUE, '得分' ],
	'team' => [ TRUE, '完成团队' ],
	'mark' => [ TRUE, '标记' ],
	'pkip' => [ TRUE, '设备IP' ],
	'pkno' => [ TRUE, '设备编号' ],
	'c_userid' => [ TRUE, '创建人' ]
];
$ermsd_task_query = wa::get_filter( array_merge( array_keys( array_filter( $ermsd_task_field, 'is_array' ) ), [ 'sbxx', 'note' ] ), $ermsd_task_query );
$ermsd_task_count = wa::$sql->get_rows( 'rx_ermsd_task_record', $ermsd_task_query );
wa::htt_data_table( 'rx_ermsd_task_record', $ermsd_task_field, function( $q, $w ) use( &$ermsd_staff_list )
{
	$q->set_attrs([
		'onclick' => 'rx_tr_resh(this)',
		'style' => 'cursor:pointer'
	]);
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) );
	$q->add( 'td', $w['type'] );
	$q->add( 'td', isset( $ermsd_staff_list[ $w['d_userid'] ] ) ? $ermsd_staff_list[ $w['d_userid'] ]['name'] : $w['d_userid'] );
	$q->add( 'td', date( 'Y-m-d H:i:s', $w['start'] ) );
	$q->add( 'td', ermsd_task_over_time( $w['over'] ) )[ 'style' ] = $w['over'] < 0 ? 'color:red' : 'color:green';
	$q->add( 'td', $w['score'] );
	$q->add( 'td', $w['team'] );
	$q->add( 'td', $w['mark'] );
	$q->add( 'td', $w['pkip'] );
	$q->add( 'td', $w['pkno'] );
	$q->add( 'td', isset( $ermsd_staff_list[ $w['c_userid'] ] ) ? $ermsd_staff_list[ $w['c_userid'] ]['name'] : $w['c_userid'] );
	$e = $q->get_parent()->add( 'tr' )->set_style( 'display:none;line-height:18px' );
	$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', ermsd_task_sbxx_trim( $w['sbxx'] ) );
	$e->add( 'td' )->set_attr( 'colspan', 8 )->add( 'pre', $w['note'] );
}, [
	'merge_query' => $ermsd_task_query,
	'stat_rows' => $ermsd_task_count,
	'page_rows' => 21
] )->set(function( $q ) use( &$ermsd_task_field, &$ermsd_team_list, $ermsd_task_count )
{
	$q['style'] = 'margin:21px auto';
	$w = [];
	foreach ( $ermsd_task_field as $e => $r )
	{
		is_array( $r ) && $w[ $e ] = $r[1];
	}; 
	$e = count( $q->thead->tr->td );
	$q->thead->tr->add_before( 'tr' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $w + [
		'sbxx' => '设备信息',
		'note' => '任务信息'
	], [
		'onfocus' => "ermsd_filter_callback(this)"
	] );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$e = &$q->thead->tr[1]->td->form->dl->dt;
	$e->tag_select(
		[ '' => '所有团队' ] + array_combine( $ermsd_team_list, $ermsd_team_list ),
		isset( $_GET[7] ) && preg_match( '/team\.eq\.([^\/]+)/', urldecode( $_GET[7] ), $w ) ? $w[1] : NULL
	)->set_attr( 'onchange', 'wa.query_act({7:this.value?"team.eq."+$.urlencode(this.value):null})' );
	$e->add( 'span', '共找到 ' . $ermsd_task_count . ' 个记录' )->set_class( 'a_c000' );
});
goto ermsd_end;

ermsd_staff_online:
wa::htt_data_table( 'rx_hr_staff', [
	'username' => [ FALSE, '工号' ],
	'name' => [ FALSE, '姓名' ],
	'rx_team' => [ TRUE, '团队' ],
	'online' => [ TRUE, '状态' ]
], function( $q, $w )
{
	$q['style'] = 'background:' . ( $w['online'] ? 'PaleGreen' : 'GhostWhite' );
	$q->td = $w['username'];
	$q->td[] = $w['name'];
	$q->td[] = $w['rx_team'];
	$e = &$q->td[];
	$e->tag_a( '切换', '?/rx_erp/ajax(1)ermsd_staff_online(2)' . $w['username'] )['onclick'] = 'return wa.ajax_query(this.href)';
}, [
	'merge_query' => 'where `group`="德胜机房运维中心" and resign=0',
	'page_rows' => 200
] )['style'] = 'margin:21px auto';
goto ermsd_end;

ermsd_relief_insert:
$ermsd_relief_value = [
	'ud0' => 0,
	'ud1' => 0,
	'cdrom' => 0,
	'screwdriver0' => 0,
	'screwdriver1' => 0,
	'pliers' => 0,
	'cpu' => 0,
	'ram2' => 0,
	'ram3' => 0,
	'nic' => 0,
	'ps' => 0,
	'interphone' => 0,
	'phone' => 0,
	'hd' => 0,
	'cable' => 0,
	'keyboard' => 0
];
if ( $q = wa::$sql->q_query( 'select * from rx_ermsd_relief where c_time is not null order by time desc limit 1' )->fetch_assoc() )
{
	$ermsd_relief_value = $q;
	unset( $ermsd_relief_value['note'] );
};
wa::htt_form_post( $ermsd_relief_insert, $ermsd_relief_value )->set(function( $q )
{
	$q['action'] = '?/rx_erp/ajax(1)ermsd_relief_insert';
	$q->table->tbody->tr->td[1]['style'] = 'width:320px';
});
goto ermsd_end;

ermsd_relief_record:
$ermsd_staff_list = rx_get_staff( '德胜机房运维中心' );
wa::htt_data_table( 'rx_ermsd_relief', [
	'only' => FALSE,
	'note' => FALSE,
	'time' => [ 'desc', '创建时间（详细）' ],
	'user' => [ FALSE, '创建工号' ],
	'ud0' => FALSE,
	'ud1' => [ FALSE, 'U盘铁/塑料' ],
	'cdrom' => [ FALSE, '光驱' ],
	'screwdriver0' => FALSE,
	'screwdriver1' => [ FALSE, '螺丝刀十/一字' ],
	'pliers' => [ FALSE, '剪线钳' ],
	'cpu' => [ FALSE, 'CPU' ],
	'ram2' => FALSE,
	'ram3' => [ FALSE, '内存2/3代(备用)' ],
	
	'nic' => [ FALSE, '网卡(备用)' ],
	'ps' => [ FALSE, '电源(备用)' ],
	'interphone' => [ FALSE, '对讲机' ],
	'phone' => [ FALSE, '手机' ],
	'hd' => [ FALSE, '硬盘(备用)' ],
	'cable' => [ FALSE, '视频线(备用)' ],
	'keyboard' => [ FALSE, '键盘' ],
	'c_time' => [ FALSE, '确认时间' ],
	'c_user' => [ FALSE, '确认工号' ]
], function( $q, $w ) use( &$ermsd_staff_list )
{
	$e = &$q->td;
	$e['onclick'] = 'rx_tr_resh(this.parentNode)';
	$e['style'] = 'cursor:pointer';
	$e[0] = date( 'Y-m-d H:i:s', $w['time'] );
	$q->td[] = isset( $ermsd_staff_list[ $w['user'] ] ) ? $ermsd_staff_list[ $w['user'] ]['name'] : $w['user'];
	$q->td[] = $w['ud0'] . ' / ' . $w['ud1'];
	$q->td[] = $w['cdrom'];
	$q->td[] = $w['screwdriver0'] . ' / ' . $w['screwdriver1'];
	$q->td[] = $w['pliers'];
	$q->td[] = $w['cpu'];
	$q->td[] = $w['ram2'] . ' / ' . $w['ram3'];
	$q->td[] = $w['nic'];
	$q->td[] = $w['ps'];
	$q->td[] = $w['interphone'];
	$q->td[] = $w['phone'];
	$q->td[] = $w['hd'];
	$q->td[] = $w['cable'];
	$q->td[] = $w['keyboard'];
	if ( $w['c_time'] )
	{
		$q->td[] = date( 'Y-m-d H:i:s', $w['c_time'] );
		$q->td[] = isset( $ermsd_staff_list[ $w['c_user'] ] ) ? $ermsd_staff_list[ $w['c_user'] ]['name'] : $w['c_user'];
	}
	else
	{
		$q->td[] = '等待确认';
		$e = &$q->td[];
		$e = $e->tag_a( '我来确认', '?/rx_erp/ajax(1)ermsd_relief_record(2)'. $w['only'] );
		$e['data-confirm'] = '确认核实吗？';
		$e['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
	};
	$q->get_parent()->add( 'tr' )->set_style( 'display:none;line-height:18px' )->add( 'td' )->set_attr( 'colspan', 17 )->add( 'pre', $w['note'] );
}, [
	// 'merge_query' => $ermsd_task_query,
	// 'stat_rows' => $ermsd_task_count,
	'page_rows' => 21
] )->set(function( $q )
{
	$q['style'] = 'margin:21px auto';
});
goto ermsd_end;

ermsd_staff_update:
rx_staff_update( '德胜机房运维中心', [
	'rx_team' => [ '' => '不分配', '火箭队' => '火箭队', '光纤队' => '光纤队' ],
	'rx_action' => [
		'ermsd_staff_update' => '允许部门员工更新（重要）',
		'rx_pack' => '允许处理数据包',
		'rx_notice_insert' => '允许发布通知',
		'ermsd_task_work' => '允许访问和分配等待或者进行的任务',
		'ermsd_task_insert' => '允许创建任务',
		'ermsd_task_update' => '允许更新任务',
		'ermsd_task_delete' => '允许删除任务',
		'ermsd_task_mark' => '允许标记任务',
		'ermsd_task_over' => '允许完成任务',
		'ermsd_task_done' => '允许访问和验收任务',
		'ermsd_task_record' => '允许访问任务记录',
		'ermsd_task_stat' => '允许访问报表统计和打印页',
		'ermsd_rd_device' => '允许访问设备查询',
		'ermsd_task_my' => '允许访问自己的工单任务',
		'ermsd_task_work_my' => '允许使用自己抢工单任务',
		'ermsd_task_over_my' => '允许使用自己完成工单任务',
		'ermsd_task_record_my' => '允许访问自己的工单记录',
		'ermsd_staff_online' => '允许设置员工在线状态',
		'ermsd_relief_insert' => '允许访问换班提交',
		'ermsd_relief_record' => '允许访问换班记录'
	]
] );
goto ermsd_end;

rx_notice_insert:
rx_notice_insert();
goto ermsd_end;

rx_notice:
rx_notice_record( '德胜机房运维中心' );

ermsd_end:
exit;
?>
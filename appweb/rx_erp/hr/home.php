<?php
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'hr_staff_insert':	goto hr_staff_insert;
	case 'hr_staff_update':	goto hr_staff_update;
	case 'hr_staff_resign':	goto hr_staff_resign;
	case 'hr_staff_list':	goto hr_staff_list;
	case 'hr_staff_updated':goto hr_staff_updated;
 	default:				goto rx_notice;
};

hr_staff_insert:
wa::htt_form_post( $hr_form_staff_header + $hr_form_staff_update, [
	'username' => wa::$sql->q_query( 'select username from rx_hr_staff order by username desc limit 1' )->fetch_row()[0] + 1,
	'office_date' => date( 'Ymd' ),
	'formal' => 0,
	'resign' => 0
] )['action'] = '?/rx_erp/ajax(1)hr_staff_insert';
goto hr_end;

hr_staff_update:
if ( isset( $_GET[2] ) && ( $q = wa::$sql->get_only( 'rx_hr_staff', 'username', $_GET[2] ) ) )
{
	wa::htt_form_post( $hr_form_staff_update, $q )['action'] = '?/rx_erp/ajax(1)hr_staff_update(2)' . urlencode( $q['username'] );
};
goto hr_end;

hr_staff_resign:
$hr_staff_resign = '`resign`!=0';

hr_staff_list:
if ( isset( $hr_staff_resign ) )
{
	$hr_staff_field_add = TRUE;
	$hr_staff_field = [
		'0' => '操作',
		'resign' => [ TRUE, '离职时间']
	];
}
else
{
	$hr_staff_field_add = FALSE;
	$hr_staff_field = [ '0' => '操作' ];
	$hr_staff_resign = '`resign`=0';
};
$hr_staff_field = $hr_staff_field + [
	0 => '操作',
	'username' => [ 'desc', '员工编号' ],
	'group' => [ TRUE, '部门' ],
	'idcard' => [ TRUE, '身份证号码' ],
	'name' => [ TRUE, '姓名' ],
	'gender' => [ TRUE, '性别' ],
	1 => '年龄',
	2 => '星座',
	'office_date' => [ TRUE, '入职时间' ],
	3 => '工龄(年,月,天)',
	'office_type' => [ TRUE, '类型' ],
	'office_imid' => [ TRUE, '即时消息' ],
	'formal' => [ TRUE, '转正日期' ],
	'socials' => [ TRUE, '社保' ],
	'deal' => [ TRUE, '合同' ],
	'post' => [ TRUE, '职务' ],
	'edu' => [ TRUE, '学历' ],
	'phone' => [ TRUE, '联系电话' ],
	'marry' => [ TRUE, '婚姻' ]
];
$hr_staff_query = wa::get_filter( array_keys( array_filter( $hr_staff_field, 'is_array' ) ), [ $hr_staff_resign ] );
$hr_staff_count = wa::$sql->get_rows( 'rx_hr_staff', $hr_staff_query );
$hr_staff_age = date( 'Y' );
wa::htt_data_table( 'rx_hr_staff', $hr_staff_field, function( $q, $w ) use( &$rx_erp_group, &$hr_staff_field_add, $hr_staff_age )
{
	$e = urlencode( $w['username'] );
	$r = $q->add( 'td' );
	$r->tag_a( '删除', '?/rx_erp/ajax(1)hr_staff_delete(2)' . $e )->set_attrs([
		'onclick' => 'return wa.ajax_query(this.href,this.dataset)',
		'data-confirm' => '无法撤消!'
	]);
	$r->add( 'span', ' | ' );
	$r->tag_a( '编号', '?/rx_erp/ajax(1)hr_staff_rename(2)' . $e )->set_attrs([
		'onclick' => 'return wa.ajax_query(this.href,this.dataset)',
		'data-prompt' => '请输入新编号!'
	]);
	$r->add( 'span', ' | ' );
	$r->tag_a( '修改', '?/rx_erp(1)hr_staff_update(2)' . $e );
	$hr_staff_field_add && $q->add( 'td', $w['resign'] );
	$q->add( 'td', $w['username'] );
	$q->add( 'td', $w['group'] );
	$q->add( 'td', $w['idcard'] );
	$q->add( 'td', $w['name'] );
	$q->add( 'td', $w['gender'] );
	$q->add( 'td', $hr_staff_age - substr( $w['idcard'], 6, 4 ) );
	$q->add( 'td', hr_calc_const_week( $w['idcard'] ) );
	$q->add( 'td', $w['office_date'] );
	$e = hr_calc_work_age( $w['office_date'], isset( $w['resign'] ) ? strtotime( $w['resign'] ) : $_SERVER['REQUEST_TIME'] );
	$q->add( 'td', $e[1] . ',' . substr( '0' . $e[2], -2 ) . ',' . substr( '0' . $e[3], -2 ) . ',' . substr( '000' . $e[4], -4 ) );
	$q->add( 'td', $w['office_type'] );
	$q->add( 'td', $w['office_imid'] );
	$e = $q->add( 'td' );
	if ( $w['formal'] )
	{
		$e[0] = $w['formal'];
	}
	else
	{
		$e[0] = date( 'Ymd', strtotime( $w['office_date'] ) + 7776000 );
		$e['style'] = 'color:silver';
	};
	$q->add( 'td', $w['socials'] );
	$q->add( 'td', $w['deal'] );
	$q->add( 'td', $w['post'] );
	$q->add( 'td', $w['edu'] );
	$q->add( 'td', $w['phone'] );
	$q->add( 'td', $w['marry'] );
}, [
	'merge_query' => $hr_staff_query,
	'stat_rows' => $hr_staff_count
] )->set(function( $q ) use( &$hr_form_staff_update, &$hr_staff_field, &$hr_staff_count )
{
	$q['style'] = 'margin:21px auto';
	//$q->thead->tr[0]->td['colspan'] = 1;
	$w = [];
	foreach ( $hr_staff_field as $e => $r )
	{
		is_array( $r ) && $w[ $e ] = $r[1];
	};
	$e = count( $q->thead->tr->td );
	$q->thead->tr->add_before( 'tr' )->add( 'td' )->set_attr( 'colspan', $e )->ins_filter( $w );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $e );
	$e = &$q->thead->tr[1]->td->form->dl->dt;
	$e->tag_select(
		[ '' => '所有部门' ] + $hr_form_staff_update['group']['value'],
		isset( $_GET[7] ) && preg_match( '/group\.eq\.([^\/]+)/', urldecode( $_GET[7] ), $w ) ? $w[1] : NULL
	)->set_attr( 'onchange', 'wa.query_act({7:this.value?"group.eq."+$.urlencode(this.value):null})' );
	$e->add( 'span' )->tag_button( '导出 Excel' )->set_attrs([
		'data-goto' => '?/rx_erp/ajax(1)hr_staff_output(2)' . ( isset( $_GET[7] ) ? $_GET[7] : '' ),
		'onclick' => '$.go(this.dataset.goto,"output_xls")'
	]);
	$e->add( 'span', '共找到 ' . $hr_staff_count . ' 个员工' )->set_class( 'a_c000' );
});
goto hr_end;

hr_staff_updated:
rx_staff_update( '人力资源部', [
	'rx_team' => [ '' => '不分配' ],
	'rx_action' => [
		'hr_staff_updated' => '允许部门员工更新（重要）',
		'hr_staff_insert' => '允许员工资料录入',
		'hr_staff_rename' => '允许重新编号员工（非常重要）',
		'hr_staff_update' => '允许员工资料更新',
		'hr_staff_delete' => '允许员工资料删除',
		'hr_staff_list' => '允许访问员工资料列表',
		'hr_staff_output' => '允许导出 Excel 员工资料',
		'hr_staff_resign' => '允许访问离职员工记录',
	]
] );
goto hr_end;

rx_notice:
rx_notice_record( '人力资源部' );

hr_end:
exit;
?>
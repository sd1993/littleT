<?php
//wa::locale();
function rx_get_staff( $q )
{
	$w = [];
	foreach ( wa::$sql->q_query( 'select username,name,office_imid from rx_hr_staff where resign=0 and `group`=?s', $q ) as $q )
	{
		$w[ $q['username'] ] = [
			'name' => $q['name'],
			'imid' => $q['office_imid']
		];
	};
	return $w;
}
wa::htt( FALSE );
wa::sql();
wa::$buffers->style = 'pre{font-size:14px}';
$ermsd_staff_list = rx_get_staff( '德胜机房运维中心' );
foreach ( wa::$sql->q_query( 'select * from rx_ermsd_task where over is null and d_userid=?s', isset( $_GET[1] ) ? $_GET[1] : '' ) as $q )
{
	wa::$buffers->write->add( 'pre' )->add_raw( join( "\n", [
		'创建者:' . ( isset( $ermsd_staff_list[ $q['c_userid'] ] ) ? $ermsd_staff_list[ $q['c_userid'] ]['name'] : $q['c_userid'] . '(不是本部门员工)' ),
		'任务标签:' . $q['mark'],
		'开始时间:' . date( 'Y-m-d H:i:s', $q['time'] ),
		'任务类型:' . $q['type'],
		'设备IPv4:' . $q['pkip'],
		'设备编号:' . $q['pkno'],
		'<font class="a_bfee">任务信息:' . htmlentities( $q['note'], ENT_XML1 ) . '</font>',
		'<font class="a_beef">' . htmlentities( $q['sbxx'], ENT_XML1 ) . '</font>'
	] ) );
	wa::$buffers->write->add( 'hr' );
};
isset( wa::$buffers->write->pre ) || wa::$buffers->write->add( 'pre', '没有任务ID:' . $_GET[1] );
?>
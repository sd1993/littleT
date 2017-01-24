<?php
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{

	case 'mfg_staff_update':goto mfg_staff_update;
	case 'rx_notice_insert':goto rx_notice_insert;
 	default:				goto rx_notice;
};

mfg_staff_update:
rx_staff_update( '产品部', [
	'rx_team' => [ '' => '不分配' ],
	'rx_action' => [
		'mfg_staff_update' => '允许部门员工更新（重要）',
		'rx_notice_insert' => '允许发布通知',
		'rx_notice_delete' => '允许删除自己发布的通知'
	]
] );
goto mfg_end;

rx_notice_insert:
rx_notice_insert();
goto mfg_end;

rx_notice:
rx_notice_record( '产品部' );

mfg_end:
exit;
?>
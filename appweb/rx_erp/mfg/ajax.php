<?php
wa::$buffers = [ '无效数据!', 'warn' ];
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'mfg_staff_update':
		wa::$buffers = rx_staff_update( '产品部' );
		break;
	case 'rx_notice_insert':
		rx_notice_insert( '产品部' );
		break;
	case 'rx_notice_delete':
		rx_notice_delete();
		break;
 	default:
 		wa::$buffers = [ '未定义功能!', 'warn' ];
};
wa::end_str_status( join( "\n", wa::$errors ) );
exit;
?>
<?php
require './config.php';
require './core/webapp_sql.php';
header( 'Content-Type: text/html; charset=utf-8' );
$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );
$q = [];
$w_rs = $sql->q_query( 'select * from rx_rd_device where status!="机柜设备"' );

foreach ( $w_rs as $w )
{

	$e = json_decode( $w['sb_info'], TRUE );
	is_array( $e ) || $e = [];
	$e += [
		'主板' => '',
		'CPU' => '',
		'内存' => '',
		'硬盘' => '',
		'电源' => '',
		'风扇' => '',
		'机箱' => ''
	];
	$q[] = [
		'server_id' => '00000000001',
		'order_No' => $w['pw_of'],
		'server_No' => $w['only'],
		'IP' => strtr( $w['ip_main'], ' ', "\n" ),
		'vice_IP' => strtr( $w['ip_vice'], ' ', "\n" ),
		'CPU' => $e['CPU'],
		'mainboard' => $e['主板'],
		'RAM' => $e['内存'],
		'ROM' => $e['硬盘'],
		'bandwidth' => $w['bandwidth'],
		'defend' => $w['defense'],
		'machine_room_name' => $w['room'],
		'rack' => $w['note'],
		'order_start_time' => $w['pw_starts'] ? date( 'Y-m-d H:i:s', $w['pw_starts'] ) : '',
		'order_end_time' => $w['pw_expire'] ? date( 'Y-m-d H:i:s', $w['pw_expire'] ) : '',
		'service_type' => $w['sb_type'],
		'server_specification_type' => $w['useu'] . 'U',
		'relevance_client' => $w['pw_be']
	];
};

echo json_encode( $q, JSON_UNESCAPED_UNICODE );
<?php
require './config.php';
require './core/webapp_sql.php';
$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );
if ( isset( $_GET['no'] ) )
{
	$q = $sql->q_query( 'select * from rx_rd_device where only like "%?b%" and status!="机柜设备"', strlen( $_GET['no'] ) > 1 ? addslashes( $_GET['no'] ) : '!' );
}
else
{
	$q = isset( $_GET['ip'] ) ? addslashes( $_GET['ip'] ) : '!';
	$q = $sql->q_query( 'select * from rx_rd_device where (ip_main like "%?b%" or ip_vice like "%?b%") and status!="机柜设备"', $q, $q );
};
$w = [];
foreach ( $q as $q )
{
	$e = json_decode( $q['sb_info'], TRUE );
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
	$w[] = [
		'server_No' => $q['only'],
		'IP' => $q['ip_main'],
		'vice_IP' => $q['ip_vice'],
		'server_state_type' => $q['status'],
		'rack' => $q['note'],
		'machine_room_name' => $q['room'],

		'CPU' => $e['CPU'],
		'RAM' => $e['内存'],
		'ROM' => $e['硬盘'],
		'mainboard' => $e['主板'],
		'power_supply' => $e['电源'],
		'fan' => $e['风扇'],

		//'server_case' => $q['status'],
		'bandwidth' => $q['bandwidth'],
		'defend' => $q['defense'],
		'client_machine_manager_QQ1' => $q['pw_imid'],
		'client_machine_manager_QQ2' => '',
		'user_name' => $q['pw_userid'],
		'configuration_owner_type' => $q['hold']
	];
};
echo json_encode( $w, JSON_UNESCAPED_UNICODE );
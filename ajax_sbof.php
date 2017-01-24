<?php
error_reporting(0);

if($_GET['key'] != '2016Ruixun!!!'){
	exit("");
}
require './config.php';
require './core/webapp_sql.php';
$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );

if($_GET['qq']){
	$qq = trim($_GET['qq']);//intval($_GET['qq']);
	$data = [];
	$data = $sql->q_query( "SELECT pw_imid FROM `rx_rd_device` WHERE pw_imid LIKE '%$qq%' ");
	//var_dump($data);
	//if ( $data )
	{
		foreach ($data as $key => $value) {
			# code...
			$imid = $value['pw_imid'];
			$arr = explode("\r\n", $imid);
			foreach ($arr as $k => $v) {
				if($v == $qq){
					exit("1");
				}
			}

		}
	};
	exit("0");
}

if ( trim( $_GET['num'] ) )
{
	$data = [];
	$data = $sql->q_query( 'select * from rx_rd_device where pw_of=?s AND status != "回收" limit 1', $_GET['num'] )->fetch_assoc();
	if ( $data && preg_match( '/(服务器|云设备|独立刀片机|刀片服务器)/', $data['sb_type'] ) )
	{
		$data['sb_info'] = json_decode( $data['sb_info'], TRUE );
		unset( $data['sb_info']['密码'] );
		$data['sb_info'] = json_encode( $data['sb_info'], JSON_UNESCAPED_UNICODE );
	};
}else{
	$data = array();
	$d = $sql->q_query( 'select pw_of,ip_vice,ip_main,pw_userid from rx_rd_device where status != "回收" AND  pw_of != "" ' );
	foreach ($d as $dr) {
		$tmp_arr = array();
		$tmp_arr['pw_of'] = $dr['pw_of'];
		$tmp_arr['ip_vice'] = $dr['ip_vice'];
		$tmp_arr['ip_main'] = $dr['ip_main'];
		$tmp_arr['user_name'] = $dr['pw_userid'];
		$data[] = $tmp_arr;
	}
}
if($data){
	echo json_encode( $data, JSON_UNESCAPED_UNICODE );
}else{
	echo "0";
}